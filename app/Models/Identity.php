<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    'platform_id'
  ];

  protected $with = ['platform'];

  public function platform()
  {
    return $this->belongsTo(Platform::class, 'platform_id', 'id');
  }
}
