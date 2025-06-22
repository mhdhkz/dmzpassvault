<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Landing extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'front'];
    return view('content.pages.landing', ['pageConfigs' => $pageConfigs]);
  }
}
