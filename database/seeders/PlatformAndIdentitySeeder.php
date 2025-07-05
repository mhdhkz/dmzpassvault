<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
  }
}
