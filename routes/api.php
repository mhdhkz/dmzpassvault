<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuditLogController;

Route::middleware('auth:sanctum')->group(function () {
  // Mengambil user login
  Route::get('/user', function (Request $request) {
    return $request->user();
  });

  // Audit log dari Python / UI yang terautentikasi
  Route::post('/audit-log', [AuditLogController::class, 'store']);
});
