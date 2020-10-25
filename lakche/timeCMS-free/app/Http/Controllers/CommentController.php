<?php namespace App\Http\Controllers;

use Theme;
use App\Model\Comment;
use App\Model\Article;
use App\Http\Requests\CommentRequest;
use Carbon\Carbon;
use Request;
use Hash;
use Auth;

class CommentController extends Controller
{
  public function store()
  {
    $request = Request::all();
    $article_id = intval($request['article_id']);
    $article = Article::where('id',$article_id)->where('is_show','>',0)->first();
    if(empty($article)) return ['error'=>1,'message'=>'文章查找不到，请刷新后重试！'];

    if(auth()->check()){
      $user_id = Auth::user()->id;
    } else {
      $user_id = 0;
    }

    $comment_id = 0;

    $hash = Hash::make(time());

    $name = self::format($request['name']);
    $phone = self::format($request['phone']);
    $info = self::format($request['info']);

    if($name == '' || $phone == '' || $info == ''){
      return ['error'=>1,'message'=>'请填写完整信息！'];
    }

    $ip = Request::getClientIp();
    $old = Comment::where('ip',$ip)->sortByDesc('id')->first();
    if($old){
      $offMinu = Carbon::now()->diffInMinutes(Carbon::parse($old->created_at));
      if($offMinu < 10){
        return ['error'=>1,'message'=>'一个IP十分钟内只能留言一次！'];
      }
    }

    $comment = Comment::create([
      'article_id' => $article_id,
      'user_id' => $user_id,
      'comment_id' => $comment_id,
      'name' => $name,
      'phone' => $phone,
      'is_show' => 0,
      'is_open' => 0,
      'info' => $info,
      'hash' => $hash,
      'ip' => $ip,
    ]);

    if ($comment) {
      return ['error'=>0,'message'=>'留言成功，请等待审核！'];
    }

    return ['error'=>1,'message'=>'系统繁忙中，请刷新后重试！'];
  }

  public function format($date){
    $date = strip_tags($date);
    $pattern = '/\s/';//去除空白
    $date = preg_replace($pattern, '', $date);   
    return $date;
  }
}
