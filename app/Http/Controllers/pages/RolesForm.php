<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Models\Position;
use Illuminate\Http\Request;

class RolesForm extends Controller
{
  // Form tambah Role Position
  public function create()
  {
    $platforms = Platform::orderBy('name')->get();
    $positions = Position::with('platforms')->orderBy('name')->get();

    return view('content.pages.roles-form', compact('platforms', 'positions'));
  }

  // Form edit Role Position
  public function edit($id)
  {
    $position = Position::with('platforms')->findOrFail($id);
    $platforms = Platform::orderBy('name')->get();
    $positions = Position::with('platforms')->orderBy('name')->get();

    return view('content.pages.roles-form', compact('position', 'platforms', 'positions'));
  }

  // Simpan Role Position baru
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:100',
      'platform_ids' => 'required|array',
    ]);

    $position = Position::create([
      'name' => $request->name
    ]);

    $position->platforms()->sync($request->platform_ids);

    return redirect()->route('admin-role-form')->with('success', 'Role Position berhasil ditambahkan.');
  }

  // Update Role Position
  public function update(Request $request, $id)
  {
    $request->validate([
      'name' => 'required|string|max:100',
      'platform_ids' => 'required|array',
    ]);

    $position = Position::findOrFail($id);
    $position->update([
      'name' => $request->name
    ]);

    $position->platforms()->sync($request->platform_ids);

    return redirect()->route('admin-role-form')->with('success', 'Role Position berhasil diperbarui.');
  }

  // Hapus Role Position
  public function destroy($id)
  {
    $position = Position::findOrFail($id);
    $position->platforms()->detach();
    $position->delete();

    return redirect()->route('admin-role-form')->with('success', 'Role Position berhasil dihapus.');
  }
}
