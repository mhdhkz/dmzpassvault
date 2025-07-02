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

      // Tambahan index
      $table->index('hostname');
      $table->index('ip_addr_srv');
      $table->index('username');
      $table->index('functionality');
      $table->index('platform_id');
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
      $table->string('identity_id', 10);
      $table->enum('event_type', ['created', 'updated', 'rotated', 'requested', 'accessed']);
      $table->dateTime('event_time')->useCurrent();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->enum('triggered_by', ['user', 'system'])->default('user');
      $table->string('actor_ip_addr', 45)->nullable();
      $table->text('note')->nullable();
      $table->timestamps();

      $table->foreign('identity_id')->references('id')->on('identities')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });

    Schema::create('password_requests', function (Blueprint $table) {
      $table->id();
      $table->string('request_id', 20)->unique();
      $table->unsignedBigInteger('user_id');
      $table->text('purpose')->nullable();
      $table->timestamp('start_at')->nullable();
      $table->timestamp('end_at')->nullable();
      $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
      $table->unsignedBigInteger('approved_by')->nullable();
      $table->timestamp('approved_at')->nullable();
      $table->timestamp('revealed_at')->nullable();
      $table->unsignedBigInteger('revealed_by')->nullable();
      $table->string('reveal_ip', 45)->nullable();
      $table->timestamp('revoked_at')->nullable();
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
      $table->foreign('revealed_by')->references('id')->on('users')->onDelete('set null');

      // Tambahan index
      $table->index('user_id');
      $table->index('status');
    });

    Schema::create('request_identity', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('password_request_id');
      $table->string('identity_id', 10);
      $table->timestamps();

      $table->foreign('password_request_id')->references('id')->on('password_requests')->onDelete('cascade');
      $table->foreign('identity_id')->references('id')->on('identities')->onDelete('cascade');
    });

    // Index tambahan untuk tabel users.name (via relasi user.name)
    Schema::table('users', function (Blueprint $table) {
      $table->index('name');
    });
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropIndex(['name']);
    });

    Schema::dropIfExists('request_identity');
    Schema::dropIfExists('password_requests');
    Schema::dropIfExists('password_audit_logs');
    Schema::dropIfExists('password_jobs');
    Schema::dropIfExists('password_vaults');
    Schema::dropIfExists('identities');
    Schema::dropIfExists('platforms');
  }
};
