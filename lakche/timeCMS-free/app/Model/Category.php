<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['title', 'info', 'sort', 'parent_id', 'cover', 'thumb', 'is_nav_show', 'keywords', 'description', 'templet_all', 'templet_nosub', 'templet_article', 'hash'];

  public function setIsNavShowAttribute($value)
  {
    $this->attributes['is_nav_show'] = intval($value);
  }

  public function scopeSortByDesc($query,$key)
  {
    return $query->orderBy($key,'desc');
  }

  public function scopeSortBy($query,$key)
  {
    return $query->orderBy($key);
  }

  public function scopeIsNavShow($query)
  {
    return $query->where('is_nav_show',1);
  }

  public function subs()
  {
    return $this->hasMany('App\Model\Category','parent_id','id');
  }

  public function root()
  {
    return $this->belongsTo('App\Model\Category','root_id','id');
  }

  public function parent()
  {
    return $this->belongsTo('App\Model\Category','parent_id','id');
  }

  public function articles()
  {
    return $this->hasMany('App\Model\Article');
  }

  public function projects()
  {
    return $this->hasMany('App\Model\Project');
  }

  public function getCover()
  {
    if($this->thumb != ''){
      return $this->thumb;
    } else {
      $this->cover;
    }
  }

}
