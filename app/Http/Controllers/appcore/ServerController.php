<?php

namespace App\Http\Controllers\appcore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Identity;
use Yajra\DataTables\Facades\DataTables;

class ServerController extends Controller
{
  public function getListData()
  {
    $data = Identity::with('platform')
      ->select('id', 'hostname', 'ip_addr_srv', 'username', 'platform_id', 'functionality');

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
}
