<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\Landing;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\Login;
use App\Http\Controllers\authentications\Register;
use App\Http\Controllers\authentications\ForgotPassword;

// Main Page Route
Route::get('/', [Landing::class, 'index'])->name('pages-landing');
Route::get('/home', [HomePage::class, 'index'])->middleware('auth')->name('home');
Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');

// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// authentication
// Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
// Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
//Route::get('/auth/login', [Login::class, 'index'])->name('auth-login');
//Route::get('/auth/register', [Register::class, 'index'])->name('auth-register');
//Route::get('/auth/forgot-password', [ForgotPassword::class, 'index'])->name('auth-forgot-password');
Route::post('/register', [Register::class, 'store'])->name('register');
