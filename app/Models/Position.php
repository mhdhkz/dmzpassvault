<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
  protected $table = 'positions';

  protected $fillable = [
    'name',
    'description',
  ];

  /**
   * Relasi ke semua user yang memiliki posisi ini.
   */
  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }

  public function platforms()
  {
    return $this->belongsToMany(Platform::class, 'platform_position_access', 'position_id', 'platform_id');
  }

}
