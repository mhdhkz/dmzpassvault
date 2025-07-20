<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use App\Models\PasswordVault;
use Carbon\Carbon;

class RotateStalePassword extends Command
{
  protected $signature = 'rotate:stale';
  protected $description = 'Rotasi paksa password untuk identity yang sudah lebih dari 7 hari tidak diganti';

  public function handle()
  {
    $this->line("⏱ Laravel sekarang: " . now()->toDateTimeString());
    $this->info("🔁 Menjalankan rotasi untuk password yang sudah stale (≥7 hari)...");

    $staleVaults = PasswordVault::whereDate('last_changed_at', '<=', now()->subDays(7))->get();

    if ($staleVaults->isEmpty()) {
      $this->info("✅ Tidak ada password yang perlu dirotasi.");
      return;
    }

    foreach ($staleVaults as $vault) {
      $identityId = $vault->identity_id;
      $lastChanged = $vault->last_changed_at;
      $daysAgo = Carbon::parse($lastChanged)->diffInDays(now());

      $this->info("🔐 Identity {$identityId} terakhir diganti {$daysAgo} hari lalu → Rotasi paksa...");

      try {
        $identityJson = json_encode([$identityId]);
        $scriptPath = public_path('assets/python/encrypt_password.py');

        $result = Process::run([
          env('PYTHON_PATH', 'python'),
          $scriptPath,
          '--identities=' . $identityJson,
          '--updated_by=1',
          '--ip_addr=127.0.0.1',
        ]);

        if ($result->successful()) {
          $this->line("✅ Sukses: " . trim($result->output()));
        } else {
          $this->error("❌ Gagal: " . trim($result->errorOutput()));
        }
      } catch (\Throwable $e) {
        $this->error("💥 Error saat memproses {$identityId}: " . $e->getMessage());
      }
    }

    $this->info("✅ Proses rotasi password stale selesai.");
  }
}
