<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['user_id', 'module', 'module_id', 'operation', 'info', 'ip'];

  public function scopeSortByDesc($query,$key)
  {
    if($key != 'id') return $query->orderBy($key,'desc')->orderBy('id','desc');
    return $query->orderBy($key,'desc');
  }

  public function scopeSortBy($query,$key)
  {
    return $query->orderBy($key);
  }

}
