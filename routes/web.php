<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Landing;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\ServerList;
use App\Http\Controllers\pages\ServerForm;
use App\Http\Controllers\authentications\Register;
use App\Http\Controllers\appcore\ServerController;




// Unaunthenticated Access
Route::get('/', [Landing::class, 'index'])->name('pages-landing');
Route::post('/register', [Register::class, 'store'])->name('register');

// Authenticated Access
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
  Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
  Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');
  //server-part
  Route::get('/server/server-list', [ServerList::class, 'index'])->name('server-server-list');
  Route::get('/server/list/data', [ServerController::class, 'getListData']);
  Route::get('/server/server-form', [ServerForm::class, 'index'])->name('server-server-form');

});
