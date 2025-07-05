<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

class PasswordRequest extends Model
{
  use HasFactory;

  protected $fillable = [
    'request_id',
    'user_id',
    'purpose',
    'start_at',
    'end_at',
    'status',
    'approved_by',
    'approved_at',
    'revealed_at',
    'revealed_by',
    'reveal_ip',
    'revoked_at'
  ];

  protected $casts = [
    'start_at' => 'datetime',
    'end_at' => 'datetime',
    'approved_at' => 'datetime',
    'revealed_at' => 'datetime',
    'revoked_at' => 'datetime',
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
    return $this->belongsToMany(Identity::class, 'request_identity')->withTimestamps();
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
