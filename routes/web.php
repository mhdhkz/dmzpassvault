<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Landing;
use App\Http\Controllers\pages\IdentityList;
use App\Http\Controllers\pages\VaultEncrypt;
use App\Http\Controllers\pages\VaultDecrypt;
use App\Http\Controllers\pages\AuditLogging;
use App\Http\Controllers\pages\UserList;
use App\Http\Controllers\pages\RolesForm;
use App\Http\Controllers\pages\DashboardController;
use App\Http\Controllers\authentications\RegisterController;
use App\Http\Controllers\appcore\IdentityController;
use App\Http\Controllers\appcore\PasswordRequestController;

// ===========================
// Public / Guest Routes
// ===========================
Route::get('/', [Landing::class, 'index'])->name('landing');
Route::post('/register', [RegisterController::class, 'store'])->name('register');


// ===========================
// Authenticated (user & admin)
// ===========================
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartDataByMonth'])->name('dashboard.chart.data');

  // Identity
  Route::prefix('identity')->group(function () {
    Route::get('/identity-list', [IdentityList::class, 'index'])->name('identity-identity-list');
    Route::get('/stats', [IdentityController::class, 'getStats'])->name('identity.stats');
    Route::get('/list/data', [IdentityController::class, 'getListData']);
    Route::get('/detail/{id}', [IdentityController::class, 'show']);
    Route::get('/{id}/activity-log', [IdentityController::class, 'activityLog']);
  });

  // User
  Route::prefix('admin')->group(function () {
    Route::put('/user-list/{id}', [UserList::class, 'update'])->name('admin-user-update');
    Route::post('/user-list/{id}/change-password', [UserList::class, 'changePassword']);
    Route::get('/user-list/stats', [UserList::class, 'getUserStats']);
    Route::get('/user/detail/{id}', [UserList::class, 'detail'])->name('admin-user-detail');
  });

  Route::get('/user/{id}/activity-log', [UserList::class, 'activityLog']);

  // Vault
  Route::prefix('vault')->group(function () {
    Route::get('/vault-form', [PasswordRequestController::class, 'create'])->name('vault-vault-form');
    Route::post('/vault-form', [PasswordRequestController::class, 'store'])->name('vault.store');

    Route::get('/vault-list', [PasswordRequestController::class, 'index'])->name('vault-vault-list');
    Route::get('/data', [PasswordRequestController::class, 'getListData'])->name('vault-vault-data');

    Route::get('/vault-decrypt', [VaultDecrypt::class, 'index'])->name('vault-vault-decrypt');
    Route::post('/decrypt/multiple', [PasswordRequestController::class, 'decryptMultiple']);

    // Route special
    Route::get('/next-request-id', [PasswordRequestController::class, 'getNextRequestId'])->name('vault.next-id');
    Route::get('/decrypt-password/{identity}', [PasswordRequestController::class, 'decryptPassword']);
    Route::get('/check-access/{identity}', [PasswordRequestController::class, 'checkAccess']);
    Route::get('/{id}/json', [PasswordRequestController::class, 'getJson']);

    // Route most special
    Route::get('/detail/{id}', [PasswordRequestController::class, 'show'])->name('vault.show');
    Route::put('/detail/{id}', [PasswordRequestController::class, 'update'])->name('vault.update');
    Route::delete('/{id}', [PasswordRequestController::class, 'destroy'])->name('vault.destroy');


  });
});

// ===========================
// Admin-only Routes
// ===========================
Route::middleware(['auth:sanctum', 'verified', 'role:admin'])->group(function () {

  Route::prefix('identity')->group(function () {
    Route::get('/identity-form', [IdentityController::class, 'identityForm'])->name('identity-identity-form');
    Route::post('/store/data', [IdentityController::class, 'store'])->name('identities.store');
    Route::post('/delete/multiple', [IdentityController::class, 'deleteMultiple']);
    Route::delete('/delete/{id}', [IdentityController::class, 'destroy']);
    Route::put('/{id}', [IdentityController::class, 'update'])->name('identity.update');
  });

  Route::prefix('vault')->group(function () {
    Route::get('/vault-encrypt', [VaultEncrypt::class, 'index'])->name('vault-vault-encrypt');
    Route::post('/generate-password', [PasswordRequestController::class, 'generatePassword']);
    Route::post('/approve/multiple', [PasswordRequestController::class, 'approveMultiple']);
    Route::post('/reject/multiple', [PasswordRequestController::class, 'rejectMultiple']);
    Route::post('/delete/multiple', [PasswordRequestController::class, 'deleteMultiple']);
    Route::post('/{id}/approve', [PasswordRequestController::class, 'approve'])->name('vault.approve');
    Route::post('/{id}/reject', [PasswordRequestController::class, 'reject'])->name('vault.reject');
  });


  Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/audit-logs', [AuditLogging::class, 'index'])->name('admin-audit-logs');
    Route::get('/audit-logs/data', [AuditLogging::class, 'getListData']);

    // Role list
    Route::get('/role-form', [RolesForm::class, 'create'])->name('admin-role-form');
    Route::post('/role-form/store', [RolesForm::class, 'store'])->name('admin-role-store');
    Route::get('/role-form/{id}/edit', [RolesForm::class, 'edit'])->name('admin-role-edit');
    Route::put('/role-form/{id}/update', [RolesForm::class, 'update'])->name('admin-role-update');
    Route::delete('/role-form/{id}/delete', [RolesForm::class, 'destroy'])->name('admin-role-delete');

    // User list
    Route::get('/user-form', [UserList::class, 'create'])->name('admin-user-form');
    Route::get('/user-list', [UserList::class, 'index'])->name('admin-user-list');
    Route::post('/user-list', [UserList::class, 'store'])->name('admin-user-store');
    Route::get('/user-list/data', [UserList::class, 'getListData'])->name('admin-user-data');
    Route::post('/user-list/delete-multiple', [UserList::class, 'deleteMultiple'])->name('admin-user-delete-multiple');
    Route::get('/user-list/{id}', [UserList::class, 'show'])->name('admin-user-show');
    Route::delete('/user-list/delete/{id}', [UserList::class, 'destroy'])->name('admin-user-destroy');
  });
});
