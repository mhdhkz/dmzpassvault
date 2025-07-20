<?php

use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // Buat posisi/role awal
    $systemPosition = Position::firstOrCreate([
      'name' => 'System',
    ], ['description' => 'System internal role']);

    $dba = Position::firstOrCreate([
      'name' => 'Database Administrator',
    ], ['description' => 'Access to database servers']);

    $sysadmin = Position::firstOrCreate([
      'name' => 'System Administrator',
    ], ['description' => 'Access to OS']);

    $frontendDev = Position::firstOrCreate([
      'name' => 'Frontend Developer',
    ], ['description' => 'Frontend application developer']);

    $nuclearEng = Position::firstOrCreate([
      'name' => 'Sr. Nuclear Engineer',
    ], ['description' => 'Nuclear-related system access']);

    // 1. User ID 1: System
    User::create([
      'id' => 1,
      'name' => 'System',
      'email' => 'system@localhost',
      'role' => 'system',
      'password' => Hash::make('system'),
      'birth_date' => null,
      'employee_id' => null,
      'job_title' => 'System Process',
      'position_id' => $systemPosition->id,
      'work_mode' => 'Onsite',
      'work_location' => 'Internal',
      'nationality' => 'System',
    ]);

    // 2. Dhika Mahendra Sudrajat
    User::create([
      'id' => 2,
      'name' => 'Dhika Mahendra Sudrajat',
      'email' => 'dhikamahendra789@gmail.com',
      'role' => 'admin',
      'password' => Hash::make('dhika123'),
      'birth_date' => '2000-05-19',
      'employee_id' => 'GRH.ID.99',
      'job_title' => 'Staff',
      'position_id' => $dba->id,
      'work_mode' => 'Onsite',
      'work_location' => 'PT Bank UOB Indonesia',
      'nationality' => 'Indonesia',
    ]);

    // 3. Vladimir Trump
    User::create([
      'name' => 'Vladimir Trump',
      'email' => 'vladimir.trump@example.com',
      'role' => 'admin',
      'password' => Hash::make('admin123'),
      'birth_date' => '1995-07-10',
      'employee_id' => 'ADM.001',
      'job_title' => 'Supervisor',
      'position_id' => $sysadmin->id,
      'work_mode' => 'Remote',
      'work_location' => 'Pentagon Red Square',
      'nationality' => 'Classified',
    ]);

    // 4. Nakashima Yuzuki
    User::create([
      'name' => 'Nakashima Yuzuki',
      'email' => 'yuzwukichan@sakurazaka46.com',
      'role' => 'user',
      'password' => Hash::make('yuzu12345'),
      'birth_date' => '2003-02-17',
      'employee_id' => 'USR.101',
      'job_title' => 'Staff',
      'position_id' => $frontendDev->id,
      'work_mode' => 'Remote',
      'work_location' => 'Fukuoka',
      'nationality' => 'Nippon',
    ]);

    // 5. Rudi Batagor
    User::create([
      'name' => 'Rudi Batagor',
      'email' => 'rudibatagor@example.com',
      'role' => 'user',
      'password' => Hash::make('rudi12345'),
      'birth_date' => '1952-08-30',
      'employee_id' => 'USR.102',
      'job_title' => 'Senior Staff',
      'position_id' => $nuclearEng->id,
      'work_mode' => 'Onsite',
      'work_location' => 'Kaliningrad',
      'nationality' => 'Soviet Union',
    ]);

    // Seeder lain
    $this->call([
      \Database\Seeders\PlatformAndIdentitySeeder::class,
    ]);
  }
}
