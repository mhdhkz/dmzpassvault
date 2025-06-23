<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
  public $incrementing = false;
  protected $keyType = 'string';
  protected $table = 'platforms';

  protected $fillable = ['id', 'name'];
}
