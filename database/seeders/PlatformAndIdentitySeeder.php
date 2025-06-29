<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PlatformAndIdentitySeeder extends Seeder
{
  public function run(): void
  {
    // 1. Seed platform
    DB::table('platforms')->insert([
      ['id' => 'PF001', 'name' => 'Linux', 'description' => 'Linux-based server platform', 'created_at' => now(), 'updated_at' => now()],
      ['id' => 'PF002', 'name' => 'Database', 'description' => 'Database platform', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // 2. Seed identities
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
        'created_by' => 1,
        'updated_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ];
    }

    DB::table('identities')->insert($identities);

    // 3. Seed password_requests dan request_identity
    $identityIds = array_column($identities, 'id');

    for ($i = 1; $i <= 10; $i++) {
      $prefix = 'REQ' . now()->format('ymd');
      $requestId = $prefix . str_pad($i, 3, '0', STR_PAD_LEFT);

      $startAt = Carbon::now()->addDays($i);
      $endAt = (clone $startAt)->addMinutes(rand(15, 60));

      $prId = DB::table('password_requests')->insertGetId([
        'request_id' => $requestId,
        'user_id' => 1,
        'purpose' => 'Akses untuk maintenance server ke-' . $i,
        'start_at' => $startAt,
        'end_at' => $endAt,
        'status' => ['pending', 'approved', 'rejected'][rand(0, 3)],
        'created_at' => Carbon::now()->subDays(10 - $i),
        'updated_at' => now()
      ]);

      foreach (collect($identityIds)->random(rand(1, 3)) as $identityId) {
        DB::table('request_identity')->insert([
          'password_request_id' => $prId,
          'identity_id' => $identityId,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }
    }
  }
}
