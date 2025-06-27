<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlatformAndIdentitySeeder extends Seeder
{
  public function run(): void
  {
    DB::table('platforms')->insert([
      ['id' => 'PF001', 'name' => 'Linux', 'description' => 'Linux-based server platform', 'created_at' => now(), 'updated_at' => now()],
      ['id' => 'PF002', 'name' => 'Database', 'description' => 'Database platform', 'created_at' => now(), 'updated_at' => now()],
    ]);

    $identities = [];

    for ($i = 1; $i <= 25; $i++) {
      $id = 'ID' . str_pad($i, 3, '0', STR_PAD_LEFT);
      $isLinux = $i % 2 !== 0;

      $identities[] = [
        'id' => $id,
        'platform_id' => $isLinux ? 'PF001' : 'PF002',
        'hostname' => $isLinux ? "srv-linux-" . str_pad($i, 2, '0', STR_PAD_LEFT) : "db-maria-" . str_pad($i, 2, '0', STR_PAD_LEFT),
        'ip_addr_srv' => '192.168.' . ($isLinux ? '1' : '2') . '.' . $i,
        'username' => $isLinux ? 'root' : 'dbadmin',
        'functionality' => $isLinux ? 'Web Server App ' . chr(64 + $i) : 'DB Server App ' . chr(64 + $i),
        'description' => $isLinux ? 'Server backend aplikasi ' . chr(64 + $i) : 'Database untuk aplikasi ' . chr(64 + $i),
        'created_at' => now(),
        'updated_at' => now(),
      ];
    }

    DB::table('identities')->insert($identities);
  }
}
