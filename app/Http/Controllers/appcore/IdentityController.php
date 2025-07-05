<?php

namespace App\Http\Controllers\appcore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Identity;
use App\Models\Platform;
use App\Models\PasswordAuditLog;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IdentityController extends Controller
{
  private function logAudit($identityId, $eventType, $note = null)
  {
    PasswordAuditLog::create([
      'identity_id' => $identityId,
      'event_type' => $eventType,
      'event_time' => now(),
      'user_id' => auth()->id(),
      'triggered_by' => 'user',
      'actor_ip_addr' => request()->ip(),
      'note' => $note,
    ]);
  }
  public function getListData()
  {
    $data = Identity::with('platform:id,name')
      ->select('id', 'hostname', 'ip_addr_srv', 'username', 'functionality', 'platform_id', 'description');

    return DataTables::of($data)
      ->addColumn('platform_name', function ($row) {
        return $row->platform->name ?? '-';
      })
      ->addColumn('action', function ($row) {
        return '<button class="btn btn-sm btn-primary">Detail</button>';
      })
      ->rawColumns(['action'])
      ->make(true);
  }

  public function identityForm()
  {
    $platforms = Platform::all();
    return view('content.pages.identity-form', compact('platforms'));
  }

  public function store(Request $request)
  {
    // Trim input untuk menghindari spasi di awal/akhir
    $request->merge([
      'hostname' => trim($request->hostname),
      'ip_addr_srv' => trim($request->ip_addr_srv),
      'username' => trim($request->username),
      'functionality' => trim($request->functionality),
    ]);

    $validated = $request->validate([
      'hostname' => [
        'required',
        'string',
        'max:100',
        Rule::unique('identities', 'hostname'),
        'not_regex:/^\s|\s$/'
      ],
      'ip_addr_srv' => [
        'required',
        'ipv4',
        Rule::unique('identities', 'ip_addr_srv'),
        'not_regex:/^\s|\s$/'
      ],
      'username' => ['required', 'string', 'max:100', 'not_regex:/^\s|\s$/'],
      'functionality' => ['required', 'string', 'max:100', 'not_regex:/^\s|\s$/'],
      'description' => ['nullable', 'string', 'max:500'],
      'platform_id' => ['required', 'exists:platforms,id']
    ]);

    DB::beginTransaction();

    try {
      // Generate ID unik
      $last = Identity::where('id', 'like', 'ID%')->orderByDesc('id')->lockForUpdate()->first();
      $nextId = $last ? 'ID' . str_pad((int) substr($last->id, 2) + 1, 3, '0', STR_PAD_LEFT) : 'ID001';

      $validated['id'] = $nextId;
      $validated['created_by'] = auth()->id();
      $validated['updated_by'] = auth()->id();

      $identity = Identity::create($validated);
      $this->logAudit($identity->id, 'created', 'Identity dibuat oleh user');
      DB::commit();

      return redirect()->route('identity-identity-form')->with('success', 'Identity berhasil ditambahkan.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withErrors(['db' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
    }
  }

  public function deleteMultiple(Request $request)
  {
    $ids = $request->input('ids', []);

    try {
      DB::beginTransaction();
      Identity::whereIn('id', $ids)->delete();
      DB::commit();
      return response()->json(['success' => true]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

  public function destroy($id)
  {
    $identity = Identity::find($id);
    if (!$identity) {
      return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }

    $this->logAudit($identity->id, 'deleted', 'Identity dihapus oleh user');
    $identity->delete();

    return response()->json(['success' => true]);
  }

  public function show($id)
  {
    $identity = Identity::with(['platform', 'createdBy', 'updatedBy'])->findOrFail($id);
    $platforms = Platform::all();

    $timelineLogs = PasswordAuditLog::with('user')
      ->where('identity_id', $id)
      ->orderByDesc('event_time')
      ->take(5)
      ->get();

    return view('content.pages.identity-detail', compact('identity', 'platforms', 'timelineLogs'));
  }


  public function update(Request $request, $id)
  {
    // Trim input
    $request->merge([
      'hostname' => trim($request->hostname),
      'ip_addr_srv' => trim($request->ip_addr_srv),
      'username' => trim($request->username),
      'functionality' => trim($request->functionality),
    ]);

    $data = $request->validate([
      'hostname' => [
        'required',
        'string',
        'max:100',
        Rule::unique('identities', 'hostname')->ignore($id),
        'not_regex:/^\s|\s$/'
      ],
      'ip_addr_srv' => [
        'required',
        'ipv4',
        Rule::unique('identities', 'ip_addr_srv')->ignore($id),
        'not_regex:/^\s|\s$/'
      ],
      'username' => ['required', 'string', 'max:100', 'not_regex:/^\s|\s$/'],
      'functionality' => ['required', 'string', 'max:100', 'not_regex:/^\s|\s$/'],
      'description' => ['nullable', 'string', 'max:500'],
      'platform_id' => ['required', 'exists:platforms,id']
    ]);

    $identity = Identity::findOrFail($id);

    $data['updated_by'] = auth()->id();

    $identity->update($data);
    $this->logAudit($identity->id, 'updated', 'Identity diperbarui oleh user');

    return response()->json([
      'success' => true,
      'message' => 'Data berhasil diperbarui.',
      'updated_by_name' => $identity->updatedBy->name ?? '-',
      'updated_at' => $identity->updated_at ? $identity->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-'
    ]);

  }
  public function activityLog($id, Request $request)
  {
    if ($request->ajax()) {
      $logs = PasswordAuditLog::with('user')
        ->where('identity_id', $id)
        ->orderByDesc('event_time');

      return DataTables::of($logs)
        ->addIndexColumn()
        ->editColumn('event_type', function ($row) {
          return match ($row->event_type) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Removed',
            'accessed' => 'Accessed',
            default => ucfirst($row->event_type),
          };
        })
        ->editColumn('event_time', fn($row) => $row->event_time->format('d M Y H:i'))
        ->addColumn('user', fn($row) => $row->user->name ?? '-')
        ->rawColumns(['event_type', 'user'])
        ->make(true);
    }
  }
}
