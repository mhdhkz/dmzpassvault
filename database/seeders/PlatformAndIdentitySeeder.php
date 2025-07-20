<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Platform;
use App\Models\Position;

class PlatformAndIdentitySeeder extends Seeder
{
  public function run(): void
  {
    // 1. Pastikan user ID 1 adalah system
    DB::table('users')->updateOrInsert(
      ['id' => 1],
      [
        'name' => 'system',
        'email' => 'system@example.com',
        'password' => Hash::make(Str::random(16)), // Tidak untuk login
        'created_at' => now(),
        'updated_at' => now()
      ]
    );

    // 2. Platform Linux & Database
    DB::table('platforms')->insertOrIgnore([
      [
        'id' => 'PF001',
        'name' => 'Linux',
        'description' => 'Linux-based server platform',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id' => 'PF002',
        'name' => 'Database',
        'description' => 'Database platform',
        'created_at' => now(),
        'updated_at' => now(),
      ]
    ]);

    // 3. Ambil data platform
    $linuxPlatform = Platform::where('name', 'Linux')->first();
    $dbPlatform = Platform::where('name', 'Database')->first();

    // 4. Ambil data posisi
    $dba = Position::where('name', 'Database Administrator')->first();
    $sysadmin = Position::where('name', 'System Administrator')->first();

    // 5. Mapping role ↔ platform (jika ada)
    if ($dba && $dbPlatform) {
      $dba->platforms()->syncWithoutDetaching([$dbPlatform->id]);
    }

    if ($sysadmin && $linuxPlatform && $dbPlatform) {
      $sysadmin->platforms()->syncWithoutDetaching([$linuxPlatform->id]);
    }
  }
}
