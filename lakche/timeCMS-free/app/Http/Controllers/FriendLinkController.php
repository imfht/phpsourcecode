<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Model\FriendLink;
use Redirect;
use Theme;

class FriendLinkController extends Controller
{
  public function show($id)
  {
    $id = intval($id);
    $friendLink = FriendLink::find($id);
    if($friendLink){
      ++$friendLink->views;
      $friendLink->save();

      if($friendLink->url != '') return Redirect::to(strip_tags($friendLink->url));
      else return Theme::view('friendLink.show',compact('friendLink'));
    } else {
      return Redirect::to('/');
    }
  }
}
