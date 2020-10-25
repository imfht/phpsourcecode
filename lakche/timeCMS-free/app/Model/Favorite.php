<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Article;

class Favorite extends Model
{
    protected $hidden = ['deleted_at', 'created_at'];
  
    protected $fillable = ['user_id', 'model', 'article_id'];
  
    public function scopeSortByDesc($query,$key)
    {
      if($key != 'id') return $query->orderBy($key,'desc')->orderBy('id','desc');
      return $query->orderBy($key,'desc');
    }
  
    public function scopeSortBy($query,$key)
    {
      return $query->orderBy($key);
    }

    public function article()
    {
      $article = Article::find($this->article_id);
      if(!$article) {
        $article = new Article;
        $article->title = '';
        $article->id = 0;
      }
      return $article;
    }
  

}
