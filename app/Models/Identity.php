<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Identity extends Model
{
  protected $table = 'identities';

  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = [
    'id',
    'hostname',
    'ip_addr_srv',
    'username',
    'functionality',
    'description',
    'platform_id',
    'created_by',
    'updated_by'
  ];

  protected $with = ['platform', 'createdBy', 'updatedBy'];

  public function platform(): BelongsTo
  {
    return $this->belongsTo(Platform::class, 'platform_id', 'id');
  }

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'created_by');
  }

  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'updated_by');
  }

  public function auditLogs()
  {
    return $this->hasMany(PasswordAuditLog::class);
  }
}
