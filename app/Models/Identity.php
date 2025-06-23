<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
  protected $table = 'identities';

  protected $fillable = ['hostname', 'ip_addr_srv', 'username', 'functionality', 'platform_id'];

  protected $with = ['platform'];
  public function platform()
  {
    return $this->belongsTo(Platform::class, 'platform_id', 'id');
  }
}
