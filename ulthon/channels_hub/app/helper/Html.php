<?php
namespace app\helper;

use think\facade\View;

class Html 
{
  public static function message($msg)
  {
    return View::fetch(__DIR__.'/html/message.html',[
      'message'=>$msg
    ]);
  }
}
