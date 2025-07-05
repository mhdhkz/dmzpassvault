<?php

namespace App\Http\Controllers\appcore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PasswordRequest;
use App\Models\Identity;
use App\Models\PasswordAuditLog;
use App\Models\PasswordVault;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;



class PasswordRequestController extends Controller
{
  public function index()
  {
    return view('content.pages.vault-list');
  }

  public function getListData(Request $request)
  {
    $query = PasswordRequest::with(['user:id,name', 'identities:id'])->select(['id', 'request_id', 'user_id', 'start_at', 'end_at', 'created_at', 'status'])->latest();


    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('user_name')) {
      $query->whereHas('user', function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->user_name . '%');
      });
    }

    if ($request->filled('date_range')) {
      [$start, $end] = explode(' - ', $request->date_range);
      $query->whereBetween('created_at', [
        \Carbon\Carbon::parse($start)->startOfDay(),
        \Carbon\Carbon::parse($end)->endOfDay()
      ]);
    }

    return DataTables::of($query)
      ->addIndexColumn()

      ->addColumn('request_id', fn($row) => $row->request_id)
      ->addColumn('user.name', fn($row) => optional($row->user)->name ?? '-')
      ->addColumn('created_at', fn($row) => optional($row->created_at)->format('Y-m-d H:i'))

      ->addColumn('duration', function ($row) {
        if (!$row->start_at || !$row->end_at)
          return '-';

        $diff = $row->start_at->diff($row->end_at);
        return ($diff->d ? "{$diff->d} Days " : '') . ($diff->h ? "{$diff->h} Hrs" : '0 Hrs');
      })

      ->addColumn('status', fn($row) => ucfirst($row->status))

      ->addColumn('id', fn($row) => $row->id)

      // Filter kolom
      ->filterColumn(
        'request_id',
        fn($query, $keyword) =>
        $query->where('request_id', 'like', "%$keyword%")
      )
      ->filterColumn(
        'user.name',
        fn($query, $keyword) =>
        $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$keyword%"))
      )
      ->filterColumn(
        'status',
        fn($query, $keyword) =>
        $query->where('status', 'like', "%$keyword%")
      )

      ->make(true);
  }


  public function create()
  {
    $identities = Identity::all();
    return view('content.pages.vault-form', compact('identities'));
  }

  public function store(Request $request)
  {
    $request->merge([
      'purpose' => trim($request->purpose),
      'duration_range' => trim($request->duration_range)
    ]);

    $validated = $request->validate([
      'purpose' => ['required', 'string', 'max:1200', 'not_regex:/^\s|\s$/'],
      'duration_range' => ['required', 'string'],
      'identity_ids' => ['required', 'array'],
      'identity_ids.*' => ['exists:identities,id']
    ]);

    // Ubah ke menit + ambil waktu mulai dan akhir
    [$start, $end] = explode(' - ', $validated['duration_range']);
    $startTime = \Carbon\Carbon::parse($start);
    $endTime = \Carbon\Carbon::parse($end);
    $durationMinutes = $startTime->diffInMinutes($endTime);

    DB::beginTransaction();

    try {
      // Generate request_id dengan format REQYYMMDDNNN
      $todayPrefix = now()->format('ymd'); // YYMMDD
      $prefix = 'REQ' . $todayPrefix;

      $last = PasswordRequest::where('request_id', 'like', "$prefix%")
        ->orderByDesc('request_id')
        ->lockForUpdate()
        ->first();

      $nextNumber = $last ? ((int) substr($last->request_id, -3)) + 1 : 1;
      $nextId = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

      $passwordRequest = PasswordRequest::create([
        'request_id' => $nextId,
        'user_id' => auth()->id(),
        'purpose' => $validated['purpose'],
        'duration_minutes' => $durationMinutes,
        'start_at' => $startTime,
        'end_at' => $endTime,
        'status' => 'pending'
      ]);

      $passwordRequest->identities()->attach($validated['identity_ids']);

      foreach ($validated['identity_ids'] as $identityId) {
        PasswordAuditLog::create([
          'identity_id' => $identityId,
          'event_type' => 'requested',
          'event_time' => now(),
          'user_id' => auth()->id(),
          'triggered_by' => 'user',
          'actor_ip_addr' => $request->ip(),
          'note' => 'User mengajukan permintaan akses password',
        ]);
      }


      DB::commit();

      return back()->with('success', 'Permintaan berhasil dikirim.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withErrors(['db' => 'Gagal menyimpan request.'])->withInput();
    }
  }

  public function show($id)
  {
    $request = PasswordRequest::with(['user', 'identities.platform', 'approvedBy', 'revealedBy'])->findOrFail($id);

    // Tambahkan durasi (agar tersedia di blade)
    $diff = $request->start_at->diff($request->end_at);

    $request->duration_friendly = ($diff->d ? "{$diff->d} Days " : '0 Days ') .
      ($diff->h ? "{$diff->h} Hours" : '0 Hours');


    $identity = $request->identities->first();

    $timelineLogs = [];

    if ($identity) {
      $timelineLogs = PasswordAuditLog::with('user')
        ->where('identity_id', $identity->id)
        ->latest('event_time')
        ->take(20)
        ->get();
    }

    return view('content.pages.vault-detail', [
      'identity' => $identity,
      'vault' => $request,
      'timelineLogs' => $timelineLogs,
      'platforms' => \App\Models\Platform::all()
    ]);
  }

  public function approve($id)
  {
    $request = PasswordRequest::findOrFail($id);
    $request->update([
      'status' => 'approved',
      'approved_at' => now(),
      'approved_by' => Auth::id()
    ]);

    PasswordAuditLog::create([
      'identity_id' => $request->identities()->first()->id ?? null,
      'event_type' => 'approved',
      'event_time' => now(),
      'user_id' => auth()->id(),
      'triggered_by' => 'user',
      'actor_ip_addr' => request()->ip(),
      'note' => 'Permintaan akses disetujui'
    ]);

    return response()->json(['success' => true]);
  }

  public function reject($id)
  {
    $request = PasswordRequest::findOrFail($id);
    $request->update([
      'status' => 'rejected',
      'approved_at' => null,
      'approved_by' => null
    ]);

    PasswordAuditLog::create([
      'identity_id' => $request->identities()->first()->id ?? null,
      'event_type' => 'rejected',
      'event_time' => now(),
      'user_id' => auth()->id(),
      'triggered_by' => 'user',
      'actor_ip_addr' => request()->ip(),
      'note' => 'Permintaan akses ditolak'
    ]);

    return response()->json(['success' => true]);
  }


  public function approveMultiple(Request $request)
  {
    $userId = Auth::id();
    $ipAddr = request()->ip();

    $requests = PasswordRequest::with('identities')->whereIn('id', $request->ids)->get();

    foreach ($requests as $req) {
      $req->update([
        'status' => 'approved',
        'approved_at' => now(),
        'approved_by' => $userId
      ]);

      foreach ($req->identities as $identity) {
        PasswordAuditLog::create([
          'identity_id' => $identity->id,
          'event_type' => 'approved',
          'event_time' => now(),
          'user_id' => $userId,
          'triggered_by' => 'user',
          'actor_ip_addr' => $ipAddr,
          'note' => 'Permintaan akses disetujui (batch)'
        ]);
      }
    }

    return response()->json(['success' => true]);
  }


  public function rejectMultiple(Request $request)
  {
    $userId = Auth::id();
    $ipAddr = request()->ip();

    $requests = PasswordRequest::with('identities')->whereIn('id', $request->ids)->get();

    foreach ($requests as $req) {
      $req->update([
        'status' => 'rejected',
        'approved_at' => null,
        'approved_by' => null
      ]);

      foreach ($req->identities as $identity) {
        PasswordAuditLog::create([
          'identity_id' => $identity->id,
          'event_type' => 'rejected',
          'event_time' => now(),
          'user_id' => $userId,
          'triggered_by' => 'user',
          'actor_ip_addr' => $ipAddr,
          'note' => 'Permintaan akses ditolak (batch)'
        ]);
      }
    }

    return response()->json(['success' => true]);
  }


  public function update(Request $request, $id)
  {
    $request->validate([
      'purpose' => ['required', 'string', 'max:1200', 'not_regex:/^\s|\s$/'],
      'duration_range' => ['required', 'string']
    ]);

    [$startAt, $endAt] = explode(' - ', $request->duration_range);
    $startAt = \Carbon\Carbon::parse($startAt);
    $endAt = \Carbon\Carbon::parse($endAt);

    if ($startAt->gt($endAt)) {
      return response()->json([
        'error' => 'Waktu mulai tidak boleh setelah waktu selesai.'
      ], 422);
    }

    $durationMinutes = $startAt->diffInMinutes($endAt);

    $vault = PasswordRequest::findOrFail($id);

    $vault->update([
      'purpose' => trim($request->purpose),
      'start_at' => $startAt,
      'end_at' => $endAt,
      'duration_minutes' => $durationMinutes,
      'updated_at' => now()
    ]);

    return response()->json([
      'message' => 'Permintaan vault berhasil diperbarui.',
      'updated_by_name' => Auth::user()->name,
      'updated_at' => now()->format('Y-m-d H:i')
    ]);
  }

  public function destroy($id)
  {
    $request = PasswordRequest::findOrFail($id);

    // Hapus relasi terlebih dahulu
    $request->identities()->detach();

    $request->delete();

    return response()->json(['success' => true, 'message' => 'Request berhasil dihapus.']);
  }

  public function getNextRequestId()
  {
    $todayPrefix = now()->format('ymd');
    $prefix = 'REQ' . $todayPrefix;

    $last = PasswordRequest::where('request_id', 'like', "$prefix%")
      ->orderByDesc('request_id')
      ->first();

    $nextNumber = $last ? ((int) substr($last->request_id, -3)) + 1 : 1;
    $nextId = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    return response()->json(['next_id' => $nextId]);
  }
  public function getJson($id)
  {
    $request = PasswordRequest::findOrFail($id);

    return response()->json([
      'id' => $request->id,
      'request_id' => $request->request_id,
      'purpose' => $request->purpose,
      'start_at' => $request->start_at->format('Y-m-d H:i'),
      'end_at' => $request->end_at->format('Y-m-d H:i'),
      'status' => $request->status
    ]);
  }

  public function generatePassword(Request $request)
  {
    $request->validate([
      'identity_ids' => ['required', 'array'],
      'identity_ids.*' => ['exists:identities,id']
    ]);

    DB::beginTransaction();

    try {
      $scriptPath = public_path('assets/python/encrypt_password.py');

      $env = array_merge($_ENV, [
        'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
        'PATH' => getenv('PATH'),
        'USERNAME' => getenv('USERNAME') ?: 'webuser',
      ]);

      // 🔁 Jalankan sekali untuk semua identity
      $process = new Process([
        env('PYTHON_PATH', 'python'),
        $scriptPath,
        '--identities=' . json_encode($request->identity_ids),
        '--updated_by=' . auth()->id(),
        '--ip_addr=' . $request->ip(),
      ], base_path(), $env);

      $process->run();

      if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
      }

      $results = json_decode($process->getOutput(), true);

      if (!is_array($results)) {
        throw new \Exception("Output Python tidak valid.");
      }

      // Optional: simpan ke log atau hanya dikembalikan saja
      DB::commit();

      return response()->json([
        'status' => 'ok',
        'results' => $results,
        'message' => 'Generate password selesai dengan status per server.'
      ]);

    } catch (\Throwable $e) {
      DB::rollBack();

      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat generate password.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function checkAccess($identityId)
  {
    $userId = auth()->id();

    // Cari request aktif & disetujui dari user untuk identity ini
    $request = PasswordRequest::where('user_id', $userId)
      ->where('status', 'approved')
      ->whereHas('identities', function ($q) use ($identityId) {
        $q->where('identities.id', $identityId);
      })
      ->where('start_at', '<=', now())
      ->where('end_at', '>=', now())
      ->latest()
      ->first();

    if (!$request) {
      return response()->json([
        'status' => 'denied',
        'message' => 'Request belum disetujui atau sudah kedaluwarsa.'
      ], 403);
    }

    return response()->json([
      'status' => 'ok',
      'message' => 'Request valid, lanjutkan dekripsi.'
    ]);
  }

  private function decryptAES($encrypted)
  {
    return is_string($encrypted) ? $encrypted : (is_resource($encrypted) ? stream_get_contents($encrypted) : (string) $encrypted);
  }

  public function decryptPassword($identityId)
  {
    $user = auth()->user();

    $request = PasswordRequest::where('user_id', $user->id)
      ->where('status', 'approved')
      ->whereHas('identities', fn($q) => $q->where('identities.id', $identityId))
      ->where('start_at', '<=', now())
      ->where('end_at', '>=', now())
      ->latest()
      ->first();

    if (!$request) {
      return response()->json([
        'status' => 'denied',
        'message' => 'Akses tidak valid atau sudah kedaluwarsa.'
      ], 403);
    }

    $vault = PasswordVault::with('identity')->where('identity_id', $identityId)->first();

    if (!$vault) {
      return response()->json([
        'status' => 'denied',
        'message' => 'Data password tidak ditemukan.'
      ], 404);
    }

    try {
      $scriptPath = public_path('assets/python/decrypt_password.py');

      $env = array_merge($_ENV, [
        'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
        'PATH' => getenv('PATH'),
        'USERNAME' => getenv('USERNAME') ?: 'webuser',
      ]);

      $process = new Process([
        env('PYTHON_PATH', 'python'),
        $scriptPath,
        '--identity=' . $identityId
      ], base_path(), $env);

      $process->run();

      if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
      }

      $output = json_decode($process->getOutput(), true);

      if (!isset($output['decrypted'])) {
        return response()->json([
          'status' => 'error',
          'message' => 'Gagal mendekripsi password.'
        ], 500);
      }

      $decrypted = $output['decrypted'];

      $request->update([
        'revealed_by' => auth()->id(),
        'revealed_at' => now(),
        'reveal_ip' => request()->ip(),
      ]);

      PasswordAuditLog::create([
        'identity_id' => $identityId,
        'event_type' => 'accessed',
        'event_time' => now(),
        'user_id' => auth()->id(),
        'triggered_by' => 'user',
        'actor_ip_addr' => request()->ip(),
        'note' => 'Password berhasil diakses (dekripsi)',
      ]);

      return response()->json([
        'status' => 'ok',
        'hostname' => $vault->identity->hostname ?? null,
        'decrypted_password' => $decrypted
      ]);

    } catch (\Throwable $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Gagal menjalankan proses dekripsi.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function decryptMultiple(Request $request)
  {
    $ids = $request->input('ids', []);

    if (empty($ids)) {
      return response()->json(['status' => 'error', 'message' => 'Tidak ada identity yang dipilih.']);
    }

    $results = [];

    foreach ($ids as $id) {
      try {
        $identity = Identity::findOrFail($id);

        // Jalankan Python script decrypt
        $scriptPath = public_path('assets/python/decrypt_password.py');

        $env = array_merge($_ENV, [
          'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
          'PATH' => getenv('PATH'),
          'USERNAME' => getenv('USERNAME') ?: 'webuser'
        ]);

        $process = new Process([
          env('PYTHON_PATH', 'python'),
          $scriptPath,
          '--identity=' . $identity->id
        ], base_path(), $env); // <- disamakan dengan encrypt

        $process->run();

        if (!$process->isSuccessful()) {
          throw new \Exception($process->getErrorOutput());
        }

        //logger('Raw output from Python:', [$process->getOutput()]);
        $output = trim($process->getOutput());
        $data = json_decode($output, true);

        if (!isset($data['decrypted'])) {
          throw new \Exception('Akses vault belum disetujui atau sudah expired.');
        }

        $password = $data['decrypted'];

        PasswordAuditLog::create([
          'identity_id' => $identity->id,
          'event_type' => 'accessed',
          'event_time' => now(),
          'user_id' => auth()->id(),
          'triggered_by' => 'user',
          'actor_ip_addr' => request()->ip(),
          'note' => 'Password berhasil diakses (dekripsi) melalui batch',
        ]);

        $identity->requests()
          ->whereNull('revealed_at')
          ->where('status', 'approved')
          ->get()
          ->each(function ($request) {
            $request->update([
              'revealed_by' => auth()->id(),
              'revealed_at' => now(),
              'reveal_ip' => request()->ip()
            ]);
          });


        $results[] = [
          'identity_id' => $identity->id,
          'hostname' => $identity->hostname,
          'password' => $password
        ];
      } catch (\Throwable $e) {
        $results[] = [
          'identity_id' => $id,
          'hostname' => '(Gagal)',
          'password' => '[Error: ' . $e->getMessage() . ']'
        ];
      }
    }

    return response()->json([
      'status' => 'ok',
      'data' => $results
    ]);
  }
  public function deleteMultiple(Request $request)
  {
    $ids = $request->input('ids', []);

    try {
      PasswordRequest::whereIn('id', $ids)->delete();

      return response()->json(['success' => true]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

}
