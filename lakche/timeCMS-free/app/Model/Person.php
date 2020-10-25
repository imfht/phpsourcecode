<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

define('Male', 0);   //男
define('Female', 1);   //女

class Person extends Model
{
  use SoftDeletes;

  protected $table = 'persons';

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['name', 'title', 'sex', 'sort', 'point', 'age', 'tag', 'is_recommend', 'is_show', 'head', 'head_thumbnail', 'url', 'keywords', 'description', 'info', 'text', 'hash'];

  public function setIsRecommendAttribute($value)
  {
    $this->attributes['is_recommend'] = intval($value);
  }

  public function setIsShowAttribute($value)
  {
    $this->attributes['is_show'] = intval($value);
  }

  public function setTagAttribute($value)
  {
    $this->attributes['tag'] = json_encode(explode(',', strip_tags($value)));
  }

  public function setSexAttribute($value)
  {
    $this->attributes['sex'] = $value ? Female : Male;
  }

  public function setTextAttribute($value)
  {
    $this->attributes['text'] = $value ? $value : '';
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

  public function getHead()
  {
      if($this->head_thumbnail != ''){
        return $this->head_thumbnail;
      } else {
        $this->head;
      }
  }

}
