<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use App\Models\PasswordRequest;
use App\Models\PasswordAuditLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoRotateExpiredPasswords extends Command
{
  protected $signature = 'rotate:expired';
  protected $description = 'Auto revoke dan rotasi password untuk request yang sudah expired';

  public function handle()
  {
    $this->info("Menjalankan rotasi otomatis...");

    $expiredRequests = PasswordRequest::with('identities')
      ->where('end_at', '<', now())
      ->whereNull('revoked_at')
      ->get();

    if ($expiredRequests->isEmpty()) {
      $this->info("Tidak ada request yang expired.");
      return;
    }

    foreach ($expiredRequests as $request) {
      foreach ($request->identities as $identity) {
        $identityId = $identity->id;

        // Path dan command
        $scriptPath = public_path('assets/python/encrypt_password.py');
        $pythonCmd = '%SYSTEMROOT%\System32\cmd.exe /c python "' . $scriptPath . "\" --identity={$identityId} --updated_by=1";

        // Eksekusi
        $this->info("Merotasi password untuk Identity {$identityId}");
        $process = Process::timeout(60)->run($pythonCmd);

        if ($process->successful()) {
          $this->info("Sukses: " . $process->output());
        } else {
          $this->error("Gagal: " . $process->errorOutput());
        }
      }

      // Update status request setelah semua identity diproses
      $request->update([
        'revoked_at' => now(),
        'status' => 'Expired',
      ]);
      $this->info("Request {$request->request_id} telah direvoke.\n");
    }
    $this->info("Proses rotasi selesai.");
  }
}
