<?php

namespace App\Http\Controllers\appcore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PasswordRequest;
use App\Models\Identity;
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
      $timelineLogs = \App\Models\PasswordAuditLog::with('user')
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

    return response()->json(['success' => true]);
  }


  public function approveMultiple(Request $request)
  {
    PasswordRequest::whereIn('id', $request->ids)->update([
      'status' => 'approved',
      'approved_at' => now(),
      'approved_by' => Auth::id()
    ]);
    return response()->json(['success' => true]);
  }

  public function rejectMultiple(Request $request)
  {
    PasswordRequest::whereIn('id', $request->ids)->update([
      'status' => 'rejected',
      'approved_at' => null,
      'approved_by' => null
    ]);
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
    $results = [];

    try {
      foreach ($request->identity_ids as $identityId) {
        $identity = Identity::findOrFail($identityId);

        // 🔐 Jalankan skrip Python
        $scriptPath = public_path('assets/python/encrypt_password.py');

        // Ambil environment bawaan sistem dan tambahkan yang penting
        $env = array_merge($_ENV, [
          'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
          'PATH' => getenv('PATH'),
          'USERNAME' => getenv('USERNAME') ?: 'webuser', // Optional
        ]);

        $process = new Process([
          env('PYTHON_PATH', 'python'), // default ke 'python3' kalau .env kosong
          $scriptPath,
          '--identity=' . $identityId
        ], base_path(), $env); // ⬅️ Ini yang penting: base_path & env

        $process->run();


        if (!$process->isSuccessful()) {
          throw new ProcessFailedException($process);
        }

        //logger('Raw output from Python:', [$process->getOutput()]);
        $output = json_decode($process->getOutput(), true);

        // Jika output valid dan ada password
        if (!isset($output['encrypted'])) {
          throw new \Exception('Output dari Python tidak valid.');
        }

        $encrypted = $output['encrypted'];

        // Cek apakah vault sudah ada
        $vault = PasswordVault::where('identity_id', $identityId)->first();

        if (!$vault) {
          $lastId = PasswordVault::where('id', 'like', 'p%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->value('id');

          $lastNumber = $lastId ? intval(substr($lastId, 1)) : 0;
          $newId = 'p' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

          $vault = new PasswordVault([
            'id' => $newId,
            'identity_id' => $identityId,
            'created_at' => now()
          ]);
        }

        $results[] = [
          'identity_id' => $identityId,
          'status' => $output['status'],
          'message' => $output['message']
        ];

      }

      DB::commit();

      return response()->json([
        'status' => 'ok',
        'results' => $results,
        'message' => 'Proses generate selesai.'
      ]);
    } catch (\Throwable $e) {
      DB::rollBack();

      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat menyimpan data.',
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
}
