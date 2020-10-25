<?php

namespace App\Http\Controllers;

use App\Model\Category;
use App\Model\Article;
use Redirect;
use Theme;

class CategoryController extends Controller
{
  public function index()
  {
    $types = Category::where('parent_id',0)->isNavShow()->sortByDesc('sort')->get();
    return Theme::view('category.index',compact('types'));
  }

  public function show($id = 0)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $type = Category::find($id);
    if(empty($type)) return Redirect::to('/');

    $keywords = $type->keywords;
    $description = $type->description;

    $subs = $type->subs()->get();
    if(count($subs)>0){
      $templet = 'sub';
      if($type->templet_all != '') $templet = $type->templet_all;
      return Theme::view('category.'.$templet,compact('type','subs','keywords','description'));
    } else {
      $templet = 'show';
      if($type->templet_nosub != '') $templet = $type->templet_nosub;
      $articles = Article::where('category_id',$id)->where('is_show','>',0)->sortByDesc('id')->paginate(20);
      return Theme::view('category.'.$templet,compact('type','articles','keywords','description'));
    }
  }

}
