<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
  use HasFactory;
  public $incrementing = false;
  protected $primaryKey = 'id';
  protected $keyType = 'string';
  protected $fillable = ['id', 'name', 'description'];
}

class Identity extends Model
{
  use HasFactory;
  public $incrementing = false;
  protected $primaryKey = 'id';
  protected $keyType = 'string';
  protected $fillable = [
    'id',
    'platform_id',
    'hostname',
    'ip_addr_srv',
    'username',
    'functionality',
    'description',
    'created_by',
    'updated_by'
  ];

  public function platform()
  {
    return $this->belongsTo(Platform::class);
  }
}

class PasswordVault extends Model
{
  use HasFactory;
  public $incrementing = false;
  protected $primaryKey = 'id';
  protected $keyType = 'string';
  public $timestamps = false;

  protected $fillable = [
    'id',
    'identity_id',
    'encrypted_password',
    'last_accessed_at',
    'last_changed_at',
    'last_changed_by',
    'last_changed_ip'
  ];

  public function identity()
  {
    return $this->belongsTo(Identity::class);
  }
}

class PasswordJob extends Model
{
  use HasFactory;
  protected $fillable = ['identity_id', 'scheduled_at', 'status'];

  public function identity()
  {
    return $this->belongsTo(Identity::class);
  }
}

class PasswordAuditLog extends Model
{
  use HasFactory;
  protected $fillable = [
    'identity_id',
    'event_type',
    'event_time',
    'user_id',
    'triggered_by',
    'actor_ip_addr',
    'note'
  ];

  public function identity()
  {
    return $this->belongsTo(Identity::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}

class PasswordRequest extends Model
{
  use HasFactory;
  protected $fillable = [
    'request_id',
    'user_id',
    'purpose',
    'duration_minutes',
    'status',
    'approved_by',
    'approved_at',
    'revealed_at',
    'revealed_by',
    'reveal_ip',
    'revoked_at'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function approvedBy()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function revealedBy()
  {
    return $this->belongsTo(User::class, 'revealed_by');
  }

  public function identities()
  {
    return $this->belongsToMany(Identity::class, 'request_identity');
  }
}

class RequestIdentity extends Model
{
  use HasFactory;
  protected $table = 'request_identity';
  protected $fillable = ['password_request_id', 'identity_id'];

  public function passwordRequest()
  {
    return $this->belongsTo(PasswordRequest::class);
  }

  public function identity()
  {
    return $this->belongsTo(Identity::class);
  }
}
