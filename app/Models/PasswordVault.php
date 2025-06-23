<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordVault extends Model
{
  protected $primaryKey = 'id';
  public $incrementing = false;
  protected $keyType = 'string';
  public $timestamps = false;
  protected $fillable = ['id', 'identity_id', 'encrypted_password', 'created_at', 'last_accessed_at', 'last_changed_at', 'last_changed_by', 'last_changed_ip'];

  public function identity()
  {
    return $this->belongsTo(Identity::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'last_changed_by');
  }
}
