<?php

namespace App\Http\Controllers\appcore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Identity;
use App\Models\Platform;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class IdentityController extends Controller
{
  public function getListData()
  {
    $data = Identity::with('platform')
      ->select('id', 'hostname', 'ip_addr_srv', 'username', 'functionality', 'platform_id');

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
    $validated = $request->validate([
      'hostname' => 'required|string|max:100|unique:identities,hostname|regex:/^(?!\s)(.*\S)?$/',
      'ip_addr_srv' => 'required|ipv4|regex:/^(?!\s)(.*\S)?$/',
      'username' => 'required|string|max:100|regex:/^(?!\s)(.*\S)?$/',
      'functionality' => 'required|string|max:100|regex:/^(?!\s)(.*\S)?$/',
      'description' => 'nullable|string|max:500',
      'platform_id' => 'required|exists:platforms,id'
    ]);

    // Gunakan transaksi agar ID selalu konsisten
    DB::beginTransaction();

    try {
      $last = Identity::where('id', 'like', 'ID%')
        ->orderByDesc('id')
        ->lockForUpdate() // 🔒 Hindari race condition
        ->first();

      if ($last) {
        $lastNum = (int) substr($last->id, 2); // ID005 → 5
        $nextId = 'ID' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
      } else {
        $nextId = 'ID001';
      }

      $validated['id'] = $nextId;

      Identity::create($validated);

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

}
