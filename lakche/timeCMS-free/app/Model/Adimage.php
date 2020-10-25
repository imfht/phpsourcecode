<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Adspace;

class Adimage extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['name', 'views', 'sort', 'is_open', 'url', 'cover', 'thumb', 'hash', 'adspace_id'];

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

  public function type()
  {
    return $this->belongsTo('App\Model\Adspace');
  }

  public function space()
  {
    $category = Adspace::find($this->adspace_id);
    if(!$category) {
      $category = new Adspace;
      $category->title = '未归档';
      $category->id = 0;
    }
    return $category;
  }

  public function getCover()
  {
    $cover = '';
    if($this->thumb != ''){
      $cover = $this->thumb;
    } else {
      $cover =$this->cover;
    }
    if($cover == ''){
      $cover = '/noad.png';
    }
    return $cover;
  }
}
