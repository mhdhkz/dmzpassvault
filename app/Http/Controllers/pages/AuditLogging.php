<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;
use App\Models\PasswordAuditLog;
use Yajra\DataTables\DataTables;

class AuditLogging extends Controller
{
  public function index()
  {
    $platforms = Platform::all();
    return view('content.pages.admin-auditlogs', compact('platforms'));
  }

  public function getListData(Request $request)
  {
    $query = PasswordAuditLog::latest('event_time')->with(['identity', 'user']);

    if ($request->filled('event_type')) {
      $query->where('event_type', $request->event_type);
    }

    if ($request->filled('actor_ip')) {
      $query->where('actor_ip_addr', 'like', '%' . $request->actor_ip . '%');
    }

    if ($request->filled('user_name')) {
      $query->whereHas('user', function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->user_name . '%');
      });
    }

    return DataTables::of($query)
      ->addColumn('hostname', fn($log) => optional($log->identity)->hostname)
      ->addColumn('ip_address', fn($log) => optional($log->identity)->ip_addr_srv)
      ->addColumn('user_name', fn($log) => optional($log->user)->name ?? 'Unknown')
      ->filterColumn('user_name', function ($query, $keyword) {
        $query->whereHas('user', function ($q) use ($keyword) {
          $q->where('name', 'like', "%$keyword%");
        });
      })
      ->make(true);
  }

}
