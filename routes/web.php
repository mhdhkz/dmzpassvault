<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Landing;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\ServerList;
use App\Http\Controllers\pages\ServerForm;
use App\Http\Controllers\authentications\RegisterController;
use App\Http\Controllers\appcore\ServerController;
use Illuminate\Http\Request;




// Unaunthenticated Access
Route::get('/', [Landing::class, 'index'])->name('pages-landing');
Route::post('/register', [RegisterController::class, 'store'])->name('registerresponse');

// Authenticated Access
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
  Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
  Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');
  //server-part
  Route::get('/server/server-list', [ServerList::class, 'index'])->name('server-server-list');
  Route::get('/server/list/data', [ServerController::class, 'getListData']);
  Route::get('/server/server-form', [ServerController::class, 'serverForm'])->name('server-server-form');
  Route::post('/server/store/data', [ServerController::class, 'store'])->name('identities.store');
  Route::post('/server/check-hostname', function (Request $request) {
    $exists = \App\Models\Identity::where('hostname', $request->hostname)->exists();
    return response()->json(['exists' => $exists]);
  })->name('server.check-hostname');
  Route::post('/server/check-ip', function (Illuminate\Http\Request $request) {
    $exists = \App\Models\Identity::where('ip_addr_srv', $request->ip_addr_srv)->exists();
    return response()->json(['exists' => $exists]);
  });
});
