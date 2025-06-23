<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlatformAndIdentitySeeder extends Seeder
{
  public function run(): void
  {
    DB::table('platforms')->insert([
      ['id' => 'PF001', 'name' => 'Linux', 'description' => 'Linux-based server platform', 'created_at' => now(), 'updated_at' => now()],
      ['id' => 'PF002', 'name' => 'MariaDB', 'description' => 'Database platform', 'created_at' => now(), 'updated_at' => now()],
    ]);

    DB::table('identities')->insert([
      [
        'id' => 'ID001',
        'platform_id' => 'PF001',
        'hostname' => 'srv-linux-01',
        'ip_addr_srv' => '192.168.1.10',
        'username' => 'root',
        'functionality' => 'Web Server App A',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'id' => 'ID002',
        'platform_id' => 'PF002',
        'hostname' => 'db-maria-01',
        'ip_addr_srv' => '192.168.1.20',
        'username' => 'dbadmin',
        'functionality' => 'DB Server App A',

        'created_at' => now(),
        'updated_at' => now()
      ]
    ]);
  }
}
