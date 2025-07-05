<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // Seed users
    User::factory(1)->create();

    User::factory()->create([
      'name' => 'Test User',
      'email' => 'test@example.com',
    ]);

    // Seed platforms and identities
    $this->call([
      \Database\Seeders\PlatformAndIdentitySeeder::class,
    ]);
  }
}
