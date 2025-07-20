<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use App\Models\PasswordRequest;
use App\Models\PasswordVault;
use Carbon\Carbon;

class AutoRotateExpiredPasswords extends Command
{
  protected $signature = 'rotate:expired';
  protected $description = 'Auto revoke dan rotasi password untuk request yang sudah expired';

  public function handle()
  {
    $this->line("⏱ Laravel sekarang: " . now()->toDateTimeString());
    $this->info("🔁 Menjalankan rotasi otomatis...");

    $expiredRequests = PasswordRequest::with('identities')
      ->where('end_at', '<', now())
      ->whereNull('revoked_at')
      ->get();

    if ($expiredRequests->isEmpty()) {
      $this->info("✅ Tidak ada request yang expired.");
      return;
    }

    foreach ($expiredRequests as $request) {
      $this->info("📦 Request: {$request->request_id}");

      $rotatedCount = 0;
      $skippedCount = 0;
      $total = $request->identities->count();

      foreach ($request->identities as $identity) {
        $identityId = $identity->id;

        // 1. Cek apakah masih ada request aktif lain untuk identity ini
        $isStillUsed = PasswordRequest::where('status', 'approved')
          ->where('start_at', '<=', now())
          ->where('end_at', '>=', now())
          ->whereHas('identities', fn($q) => $q->where('identity_id', $identityId))
          ->exists();

        // 2. Ambil waktu rotasi terakhir
        $lastRotated = PasswordVault::where('identity_id', $identityId)->value('last_changed_at');
        $lastRotatedDaysAgo = $lastRotated ? Carbon::parse($lastRotated)->diffInDays(now()) : 999;

        // 3. Tentukan logika aksi
        if ($isStillUsed && $lastRotatedDaysAgo < 7) {
          $this->line("⏭️ Identity {$identityId} masih dipakai dan belum 7 hari → dilewati.");
          $skippedCount++;
          continue;
        }

        $this->info("🔐 Merotasi password untuk Identity {$identityId}...");

        try {
          $identityJson = json_encode([$identityId]);
          $scriptPath = public_path('assets/python/encrypt_password.py');

          $result = Process::run([
            env('PYTHON_PATH', 'python'),
            $scriptPath,
            '--identities=' . $identityJson,
            '--updated_by=1',
            '--ip_addr=127.0.0.1'
          ]);

          if ($result->successful()) {
            $this->line("✅ Sukses: " . trim($result->output()));
            $rotatedCount++;
          } else {
            $this->error("❌ Gagal: " . trim($result->errorOutput()));
          }
        } catch (\Throwable $e) {
          $this->error("💥 Error saat memproses {$identityId}: " . $e->getMessage());
        }
      }

      // ✅ Hanya tandai expired kalau:
      // - setidaknya ada yang berhasil dirotasi
      // - atau semua identity memang tidak eligible (aktif & belum 7 hari)
      if ($rotatedCount > 0 || $skippedCount === $total) {
        $request->update([
          'revoked_at' => now(),
          'status' => 'Expired',
        ]);
        $this->line("🛑 Request {$request->request_id} ditandai expired. ($rotatedCount rotated, $skippedCount skipped)\n");
      } else {
        $this->warn("⚠️ Request {$request->request_id} tidak dirotasi dan tidak ditandai expired (cek log).\n");
      }
    }

    $this->info("✅ Proses rotasi selesai.");
  }
}
