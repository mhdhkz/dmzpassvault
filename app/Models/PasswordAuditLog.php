<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordAuditLog extends Model
{
  protected $fillable = ['identity_id', 'event_type', 'event_time', 'triggered_by', 'actor_ip_addr'];

  public function identity()
  {
    return $this->belongsTo(Identity::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'triggered_by');
  }
}
