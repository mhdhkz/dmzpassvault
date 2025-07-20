<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordAuditLog;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\Position;


class UserList extends Controller
{
  public function index()
  {
    return view('content.pages.user-list');
  }

  public function getListData(Request $request)
  {
    $query = User::select(['id', 'name', 'email', 'role']);

    return DataTables::of($query)
      ->filterColumn('name', fn($query, $keyword) =>
        $query->where('name', 'like', '%' . trim($keyword, '^$') . '%'))
      ->filterColumn('email', fn($query, $keyword) =>
        $query->where('email', 'like', '%' . trim($keyword, '^$') . '%'))
      ->filterColumn('role', fn($query, $keyword) =>
        $query->where('role', 'like', '%' . trim($keyword, '^$') . '%'))
      ->addColumn('action', fn() => '') // tombol dibuat di JS
      ->make(true);
  }


  public function show($id)
  {
    $user = User::findOrFail($id);
    return response()->json($user);
  }

  public function update(Request $request, $id)
  {
    $user = User::findOrFail($id);

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email,' . $user->id,
      'role' => 'required|in:admin,user',
      'birth_date' => 'nullable|date',
      'nationality' => 'nullable|string|max:255',
      'employee_id' => 'nullable|string|max:255',
      'job_title' => 'nullable|string|max:255',
      'position_id' => 'nullable|exists:positions,id',
      'work_mode' => 'nullable|in:Onsite,Remote',
      'work_location' => 'nullable|string|max:255',
    ]);

    $originalEmail = $user->email;
    $originalRole = $user->role;

    $user->fill($validated);

    try {
      $user->save();

      return response()->json([
        'success' => true,
        'message' => 'User berhasil diperbarui',
        'email_changed' => $originalEmail !== $user->email,
        'role_changed' => $originalRole !== $user->role,
        'is_self' => Auth::id() === $user->id
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Gagal memperbarui user: ' . $e->getMessage(),
      ], 500);
    }
  }

  public function destroy($id)
  {
    if (Auth::id() === (int) $id) {
      return response()->json([
        'success' => false,
        'message' => 'Kamu tidak bisa menghapus akun yang sedang login.'
      ], 403);
    }

    $user = User::findOrFail($id);
    $user->delete();

    return response()->json([
      'success' => true,
      'message' => 'User berhasil dihapus.'
    ]);
  }

  public function deleteMultiple(Request $request)
  {
    $ids = $request->input('ids', []);
    $currentUserId = Auth::id();

    if (empty($ids)) {
      return response()->json([
        'success' => false,
        'message' => 'Tidak ada ID yang dikirim.'
      ], 400);
    }

    if (in_array($currentUserId, $ids)) {
      return response()->json([
        'success' => false,
        'message' => 'Kamu tidak bisa menghapus akunmu sendiri.'
      ], 403);
    }

    User::whereIn('id', $ids)->delete();

    return response()->json([
      'success' => true,
      'message' => 'Beberapa user berhasil dihapus.'
    ]);
  }

  public function detail($id)
  {
    $user = User::with('position')->findOrFail($id); // relasi position
    $timelineLogs = PasswordAuditLog::with('user')
      ->where('user_id', $id)
      ->orderByDesc('event_time')
      ->take(5)
      ->get();

    $positions = Position::all(); // untuk dropdown

    return view('content.pages.user-detail', compact('user', 'timelineLogs', 'positions'));
  }


  public function activityLog($id, Request $request)
  {
    if ($request->ajax()) {
      $logs = PasswordAuditLog::with('user')
        ->where('user_id', $id)
        ->orderByDesc('event_time');

      return DataTables::of($logs)
        ->addIndexColumn()
        ->editColumn('event_type', fn($row) => match ($row->event_type) {
          'created' => 'Dibuat',
          'updated' => 'Diperbarui',
          'deleted' => 'Dihapus',
          'accessed' => 'Diakses',
          'login' => 'Login',
          'logout' => 'Logout',
          default => ucfirst($row->event_type),
        })
        ->editColumn('event_time', fn($row) => $row->event_time->format('d M Y H:i'))
        ->addColumn('user', fn($row) => $row->user->name ?? '-')
        ->rawColumns(['event_type', 'user'])
        ->make(true);
    }
  }

  public function changePassword(Request $request, $id)
  {
    $user = User::findOrFail($id);

    $request->validate([
      'password' => 'required|string|min:8|confirmed'
    ]);

    $user->password = bcrypt($request->password);

    try {
      $user->save();
      return response()->json([
        'success' => true,
        'message' => 'Password berhasil diubah.',
        'is_self' => Auth::id() === $user->id
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Gagal mengubah password: ' . $e->getMessage()
      ], 500);
    }
  }

  public function create()
  {
    return view('content.pages.user-form');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email',
      'role' => 'required|in:admin,user',
      'birth_date' => 'nullable|date',
      'nationality' => 'nullable|string|max:255',
      'employee_id' => 'nullable|string|max:255',
      'job_title' => 'nullable|string|max:255',
      'position_id' => 'nullable|exists:positions,id',
      'work_mode' => 'nullable|in:Onsite,Remote',
      'work_location' => 'nullable|string|max:255',
      'password' => 'required|string|min:8|confirmed',
    ]);

    try {
      User::create([
        ...$validated,
        'password' => bcrypt($validated['password']),
      ]);

      return redirect()->route('admin-user-list')->with('success', 'User berhasil ditambahkan.');
    } catch (\Exception $e) {
      return back()->withErrors(['error' => 'Gagal menambahkan user: ' . $e->getMessage()])->withInput();
    }
  }
  public function getUserStats()
  {
    return response()->json([
      'total' => User::count(),
      'system' => User::where('role', 'system')->count(),
      'admin' => User::where('role', 'admin')->count(),
      'user' => User::where('role', 'user')->count(),
    ]);
  }
}
