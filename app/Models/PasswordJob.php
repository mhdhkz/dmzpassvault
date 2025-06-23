<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordJob extends Model
{
  protected $fillable = ['identity_id', 'scheduled_at', 'status'];

  public function identity()
  {
    return $this->belongsTo(Identity::class);
  }
}
