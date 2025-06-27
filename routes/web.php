<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Landing;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\IdentityList;
use App\Http\Controllers\pages\IdentityForm;
use App\Http\Controllers\authentications\RegisterController;
use App\Http\Controllers\appcore\IdentityController;
use Illuminate\Http\Request;




// Unaunthenticated Access
Route::get('/', [Landing::class, 'index'])->name('pages-landing');
Route::post('/register', [RegisterController::class, 'store'])->name('registerresponse');

// Authenticated Access
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
  Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
  Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');
  //identity-part
  Route::get('/identity/identity-list', [IdentityList::class, 'index'])->name('identity-identity-list');
  Route::get('/identity/list/data', [IdentityController::class, 'getListData']);
  Route::get('/identity/identity-form', [IdentityController::class, 'identityForm'])->name('identity-identity-form');
  Route::post('/identity/store/data', [IdentityController::class, 'store'])->name('identities.store');
  Route::post('/identity/delete/multiple', [IdentityController::class, 'deleteMultiple']);
  Route::post('/identity/check-hostname', function (Request $request) {
    $exists = \App\Models\Identity::where('hostname', $request->hostname)->exists();
    return response()->json(['exists' => $exists]);
  })->name('identity.check-hostname');
  Route::post('/identity/check-ip', function (Illuminate\Http\Request $request) {
    $exists = \App\Models\Identity::where('ip_addr_srv', $request->ip_addr_srv)->exists();
    return response()->json(['exists' => $exists]);
  });
});
