<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Model\Page;
use Redirect;
use Theme;

class PageController extends Controller
{

  public function show($id)
  {
    $id = htmlspecialchars($id);
    $page = Page::where('url',$id)->isOpen()->first();
    if($page){
      ++$page->views;
      $page->save();

      if($page->openurl != '') return Redirect::to(strip_tags($page->openurl));
      else return Theme::view('page.'.$page->view, array('page' => $page));
    } else {
      return Redirect::to('/');
    }
  }
}
