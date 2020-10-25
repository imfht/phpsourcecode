<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Article extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['title', 'category_id', 'sort', 'views', 'tag', 'is_recommend', 'is_show', 'info', 'url', 'cover', 'thumb', 'text', 'subtitle', 'author', 'source', 'keywords', 'description', 'hash','is_top'];

  public function setIsRecommendAttribute($value)
  {
    $this->attributes['is_recommend'] = intval($value);
  }

  public function setIsShowAttribute($value)
  {
    $this->attributes['is_show'] = intval($value);
  }

  public function setIsTopAttribute($value)
  {
    $this->attributes['is_top'] = intval($value);
  }

  public function setTagAttribute($value)
  {
    $this->attributes['tag'] = json_encode(explode(',', strip_tags($value)));
  }

  public function setDescriptionAttribute($value)
  {
    $this->attributes['description'] = $value ? $value : '';
  }

  public function setTextAttribute($value)
  {
    $this->attributes['text'] = $value ? $value : '';
  }

  public function getCreatedAtAttribute($date)
  {
    return Carbon::parse($date)->toDateTimeString();
  }

  public function getUpdatedAtAttribute($date)
  {
    return Carbon::parse($date)->toDateTimeString();
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

  public function type()
  {
    return $this->belongsTo('App\Model\Category');
  }

  public function category()
  {
    $category = Category::find($this->category_id);
    if(!$category) {
      $category = new Category;
      $category->title = '未归档';
      $category->id = 0;
    }
    return $category;
  }

  public function getCover()
  {
    $cover = '';
    if($this->cover != ''){
      $cover = $this->cover;
    } else {
      $cover = '/uucj/images/no.jpg';
    }
    return $cover;
  }

  public function maketime(){
    $date = $this->created_at;
    $offDays = Carbon::now('Asia/Shanghai')->diffInDays(Carbon::parse($date));
    if ($offDays > 7) {
        return Carbon::parse($date)->toDateString();
    }

    return Carbon::parse($date)->diffForHumans();
  }

}
