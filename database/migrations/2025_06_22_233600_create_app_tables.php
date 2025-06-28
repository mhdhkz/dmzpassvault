<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('platforms', function (Blueprint $table) {
      $table->string('id', 10)->primary();
      $table->string('name', 100);
      $table->text('description')->nullable();
      $table->timestamps();
    });

    Schema::create('identities', function (Blueprint $table) {
      $table->string('id', 10)->primary();
      $table->string('platform_id', 10);
      $table->string('hostname', 100);
      $table->string('ip_addr_srv', 45)->nullable();
      $table->string('username', 100);
      $table->string('functionality', 100)->nullable();
      $table->text('description')->nullable();
      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();
      $table->timestamps();

      $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
      $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
      $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
    });

    Schema::create('password_vaults', function (Blueprint $table) {
      $table->string('id', 10)->primary();
      $table->string('identity_id', 10);
      $table->binary('encrypted_password');
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('last_accessed_at')->nullable();
      $table->timestamp('last_changed_at')->useCurrent();
      $table->unsignedBigInteger('last_changed_by')->nullable();
      $table->string('last_changed_ip', 45)->nullable();

      $table->foreign('identity_id')->references('id')->on('identities')->onDelete('cascade');
      $table->foreign('last_changed_by')->references('id')->on('users')->onDelete('set null');
    });

    Schema::create('password_jobs', function (Blueprint $table) {
      $table->id();
      $table->string('identity_id', 10);
      $table->dateTime('scheduled_at');
      $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
      $table->timestamps();

      $table->foreign('identity_id')->references('id')->on('identities')->onDelete('cascade');
    });

    Schema::create('password_audit_logs', function (Blueprint $table) {
      $table->id();

      // ID identity terkait
      $table->string('identity_id', 10);

      // Jenis peristiwa
      $table->enum('event_type', [
        'created',
        'updated',
        'rotated',
        'requested',
        'accessed'
      ]);

      // Waktu kejadian (boleh pakai default timestamp)
      $table->dateTime('event_time')->useCurrent();

      // User yang memicu event (null kalau oleh sistem)
      $table->unsignedBigInteger('user_id')->nullable();

      // Apakah dipicu user atau sistem
      $table->enum('triggered_by', ['user', 'system'])->default('user');

      // IP address pelaku (jika relevan)
      $table->string('actor_ip_addr', 45)->nullable();

      // Catatan atau info tambahan opsional
      $table->text('note')->nullable();

      $table->timestamps();

      // Relasi
      $table->foreign('identity_id')->references('id')->on('identities')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });

  }

  public function down(): void
  {
    Schema::dropIfExists('password_audit_logs');
    Schema::dropIfExists('password_jobs');
    Schema::dropIfExists('password_vaults');
    Schema::dropIfExists('identities');
    Schema::dropIfExists('platforms');
  }
};
