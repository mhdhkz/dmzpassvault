<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Landing;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\IdentityList;
use App\Http\Controllers\pages\IdentityForm;
use App\Http\Controllers\pages\VaultDecrypt;
use App\Http\Controllers\authentications\RegisterController;
use App\Http\Controllers\appcore\IdentityController;
use App\Http\Controllers\appcore\PasswordRequestController;
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
  Route::delete('/identity/delete/{id}', [IdentityController::class, 'destroy']);
  Route::get('/identity/detail/{id}', [IdentityController::class, 'show']);
  Route::put('/identity/{id}', [IdentityController::class, 'update'])->name('identity.update');
  Route::get('/identity/{id}/activity-log', [IdentityController::class, 'activityLog']);
  Route::post('/identity/check-hostname', function (Request $request) {
    $exists = \App\Models\Identity::where('hostname', $request->hostname)->exists();
    return response()->json(['exists' => $exists]);
  })->name('identity.check-hostname');
  Route::post('/identity/check-ip', function (Illuminate\Http\Request $request) {
    $exists = \App\Models\Identity::where('ip_addr_srv', $request->ip_addr_srv)->exists();
    return response()->json(['exists' => $exists]);
  });

  //vault-part
  Route::get('/vault/vault-decrypt', [VaultDecrypt::class, 'index'])->name('vault-vault-decrypt');
  Route::get('/vault/vault-list', [PasswordRequestController::class, 'index'])->name('vault-vault-list');
  Route::get('/vault/data', [PasswordRequestController::class, 'getListData'])->name('vault-vault-data');
  Route::get('/vault/vault-form', [PasswordRequestController::class, 'create'])->name('vault-vault-form');
  Route::post('/vault/vault-form', [PasswordRequestController::class, 'store'])->name('vault.store');
  Route::post('/vault/approve/multiple', [PasswordRequestController::class, 'approveMultiple']);
  Route::post('/vault/reject/multiple', [PasswordRequestController::class, 'rejectMultiple']);
  Route::get('/vault/next-request-id', [PasswordRequestController::class, 'getNextRequestId'])->name('vault.next-id');
  Route::get('/vault/{id}', [PasswordRequestController::class, 'show'])->name('vault.show');
  Route::put('/vault/{id}', [PasswordRequestController::class, 'update'])->name('vault.update');
  Route::delete('/vault/{id}', [PasswordRequestController::class, 'destroy'])->name('vault.destroy');
  Route::post('/vault/{id}/approve', [PasswordRequestController::class, 'approve'])->name('vault.approve');
  Route::post('/vault/{id}/reject', [PasswordRequestController::class, 'reject'])->name('vault.reject');
  Route::get('/vault/{id}/json', [PasswordRequestController::class, 'getJson']);
});
