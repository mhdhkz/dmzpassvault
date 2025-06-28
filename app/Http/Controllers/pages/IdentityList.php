<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;

class IdentityList extends Controller
{
  public function index()
  {
    $platforms = Platform::all();
    return view('content.pages.identity-list', compact('platforms'));
  }
}
