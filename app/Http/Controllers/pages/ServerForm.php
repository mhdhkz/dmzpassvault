<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Platform;

class ServerForm extends Controller
{
  public function index()
  {
    $platforms = Platform::orderBy('name')->get();
    return view('content.pages.server-form', compact('platforms'));
  }
}
