<?php

namespace App\Http\Controllers;

use App\Model\Category;
use App\Model\Article;
use Redirect;
use Theme;
use Request;

class SearchController extends Controller
{
  public function index()
  {
    $key = Request::get('key','');
    if($key == '') return Redirect::to('/');

    $articles = Article::where('title','like',"%$key%")->sortByDesc('id')->paginate(10);

    return Theme::view('search.index',compact('key','articles'));
  }

}
