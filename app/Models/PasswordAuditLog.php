<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    'note',
  ];

  protected $casts = [
    'event_time' => 'datetime',
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
