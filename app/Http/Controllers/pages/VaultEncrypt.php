<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;

class VaultEncrypt extends Controller
{
  public function index()
  {
    $platforms = Platform::all();
    return view('content.pages.vault-encrypt', compact('platforms'));
  }
}
