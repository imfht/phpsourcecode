<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FriendLink extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['name', 'views', 'sort', 'is_open', 'url', 'cover', 'thumb', 'hash'];

  public function setIsOpenAttribute($value)
  {
    $this->attributes['is_open'] = intval($value);
  }

  public function scopeSortByDesc($query,$key)
  {
    if($key != 'id') return $query->orderBy($key,'desc')->orderBy('id','desc');
    return $query->orderBy($key,'desc');
  }

  public function scopeSortBy($query,$key)
  {
    return $query->orderBy($key);
  }

  public function scopeIsOpen($query)
  {
    return $query->where('is_open','>',0);
  }

  public function getCover()
  {
    if($this->thumb != ''){
      return $this->thumb;
    } elseif($this->cover !='') {
      return $this->cover;
    } else {
      return false;
    }
  }
}
