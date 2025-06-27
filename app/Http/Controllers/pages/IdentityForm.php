<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Platform;

class IdentityForm extends Controller
{
  public function index()
  {
    $platforms = Platform::orderBy('name')->get();
    return view('content.pages.identity-form', compact('platforms'));
  }
}
