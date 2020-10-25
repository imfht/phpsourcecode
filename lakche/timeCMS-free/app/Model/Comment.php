<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\User;

class Comment extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['article_id','user_id','comment_id','name','phone','is_show','is_open','info','hash','ip'];

  public function setIsShowAttribute($value)
  {
    $this->attributes['is_show'] = intval($value);
  }

  public function setIsOpenAttribute($value)
  {
    $this->attributes['is_open'] = intval($value);
  }

  public function setInfoAttribute($value)
  {
    $this->attributes['info'] = $value ? $value : '';
  }

  public function setNameAttribute($value)
  {
    $this->attributes['name'] = $value ? $value : '';
  }

  public function setPhoneAttribute($value)
  {
    $this->attributes['phone'] = $value ? $value : '';
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

  public function user()
  {
    $user = User::find($this->user_id);
    if(!$user) {
      $user = new User;
      $user->name = '游客';
      $user->id = 0;
    }
    return $user;
  }

  public function article()
  {
    $article = Article::find($this->article_id);
    if(!$article) {
      $article = new Article;
      $article->title = '未知';
      $article->id = 0;
    }
    return $article;
  }

  public function comment()
  {
    $comment = Comments::find($this->comment_id);
    if(!$comment) {
      $comment = new Comments;
      $comment->title = '未知';
      $comment->id = 0;
    }
    return $comment;
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
