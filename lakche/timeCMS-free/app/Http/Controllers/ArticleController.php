<?php

namespace App\Http\Controllers;

use App\Model\Article;
use App\Model\Category;
use App\Model\Favorite;
use App\Model\Comment;
use Redirect;
use Theme;

class ArticleController extends Controller
{
  public function index()
  {
    return Redirect::to('/');
  }

  public function show($id = 0)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $article = Article::where('id',$id)->where('is_show','>',0)->first();
    if(empty($article)) return Redirect::to('/');

    $type = Category::find($article->category_id);
    if(empty($type)) return Redirect::to('/');

    ++$article->views;
    $article->save();

    $keywords = $article->keywords;
    $description = $article->description;

    if($article->url != '') return Redirect::to($article->url);

    //收藏
    $is_favorite = 0;
    if(auth()->check()){
      $user = auth()->user();
      $favorite = Favorite::where('user_id',$user->id)->where('model','article')->where('article_id',$article->id)->first();
      if(!empty($favorite)){
        $is_favorite = 1;
      }
    }

    //留言，只显示最新的十条
    $comments = Comment::where('article_id',$article->id)->where('is_open',1)->where('is_show',1)->sortByDesc('updated_at')->limit(10)->get();

    $templet = 'show';
    if($type->templet_article != '') $templet = $type->templet_article;
    return Theme::view('article.'.$templet,compact('article','type','keywords','description','is_favorite','comments'));
  }
}
