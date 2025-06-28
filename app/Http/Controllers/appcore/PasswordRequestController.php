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
    $query = PasswordRequest::with(['user', 'identities', 'identities.platform'])->latest();

    return DataTables::of($query)
      ->addIndexColumn()
      ->addColumn('request_id', fn($row) => $row->request_id)
      ->addColumn('identity.hostname', function ($row) {
        return $row->identities->isNotEmpty()
          ? $row->identities->map(fn($i) => $i->hostname ?? '-')->implode(', ')
          : '-';
      })
      ->addColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
      ->addColumn('duration', fn($row) => $row->duration_minutes . ' menit')
      ->addColumn('status', fn($row) => ucfirst($row->status))
      ->addColumn('id', fn($row) => $row->id)

      // === Tambahan ini penting untuk mendukung fitur search ===
      ->filterColumn('request_id', function ($query, $keyword) {
        $query->where('request_id', 'like', "%$keyword%");
      })
      ->filterColumn('identity.hostname', function ($query, $keyword) {
        $query->whereHas('identities', function ($q) use ($keyword) {
          $q->where('hostname', 'like', "%$keyword%");
        });
      })
      ->filterColumn('status', function ($query, $keyword) {
        $query->where('status', 'like', "%$keyword%");
      })

      ->make(true);
  }


  public function create()
  {
    $identities = Identity::all();
    return view('content.pages.vault-form', compact('identities'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'identity_ids' => 'required|array',
      'purpose' => 'required|string',
      'duration_minutes' => 'required|integer|min:1'
    ]);

    DB::beginTransaction();
    try {
      $prefix = 'REQ' . now()->format('ymd');
      $latest = PasswordRequest::where('request_id', 'like', "$prefix%")
        ->orderByDesc('request_id')
        ->first();
      $seq = $latest ? intval(substr($latest->request_id, -3)) + 1 : 1;
      $newId = $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);

      $req = PasswordRequest::create([
        'request_id' => $newId,
        'user_id' => Auth::id(),
        'purpose' => $request->purpose,
        'duration_minutes' => $request->duration_minutes,
      ]);

      foreach ($request->identity_ids as $identityId) {
        RequestIdentity::create([
          'password_request_id' => $req->id,
          'identity_id' => $identityId
        ]);
      }

      DB::commit();
      return redirect()->route('vault.list')->with('success', 'Request berhasil dibuat.');
    } catch (\Exception $e) {
      DB::rollback();
      return back()->withErrors(['msg' => 'Gagal menyimpan request: ' . $e->getMessage()]);
    }
  }

  public function show($id)
  {
    $request = PasswordRequest::with(['user', 'identities.platform'])->findOrFail($id);
    $identity = $request->identities->first(); // ← Tanpa typo

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
      'purpose' => 'required|string|max:1200',
      'start_time' => 'required|date|before_or_equal:end_time',
      'end_time' => 'required|date|after_or_equal:start_time',
      'duration_minutes' => 'required|integer|min:1|max:7200' // 5 hari = 7200 menit
    ]);

    $vault = PasswordRequest::findOrFail($id);

    $vault->update([
      'purpose' => $request->purpose,
      'duration_minutes' => $request->duration_minutes,
      'start_time' => $request->start_time,
      'end_time' => $request->end_time,
      'updated_by' => Auth::id()
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

}
