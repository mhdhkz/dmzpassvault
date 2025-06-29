<?php

namespace App\Http\Controllers\appcore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\PasswordRequest;
use App\Models\Identity;
use App\Models\RequestIdentity;
use Yajra\DataTables\Facades\DataTables;

class PasswordRequestController extends Controller
{
  public function index()
  {
    return view('content.pages.vault-list');
  }

  public function getListData(Request $request)
  {
    $query = PasswordRequest::with(['user', 'identities'])->latest();

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
      'approved_at' => now(),
      'approved_by' => Auth::id()
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
      'approved_at' => now(),
      'approved_by' => Auth::id()
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

}
