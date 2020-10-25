<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['url', 'name', 'thumb', 'sort', 'is_recommend', 'is_show', 'is_cover', 'type', 'attr', 'hash', 'project_id'];

  public function scopeSortByDesc($query,$key)
  {
    if($key != 'id') return $query->orderBy($key,'desc')->orderBy('id','desc');
    return $query->orderBy($key,'desc');
  }

  public function scopeSortBy($query,$key)
  {
    return $query->orderBy($key);
  }

  public function attr()
  {
    $attr = ucwords($this->attr);
    return $this->hasMany('App\Model\\'.$attr,'project_id','id');
  }

}
