<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Favorite;
use Request;
use Theme;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $articles = Favorite::where('user_id',$user->id)->where('model','article')->paginate(20);
        return Theme::view('user.favorite.show',compact('articles'));
    }

    public function store(Request $request)
    {
        $input = Request::only(['article_id', 'model', 'type']);
        $user = auth()->user();

        $msg = ['error'=>1,'message'=>'收藏失败！请刷新！'];
        switch($input['model']){
            case 'article':
                $favorite = Favorite::where('user_id',$user->id)->where('model','article')->where('article_id',$input['article_id'])->first();
                if($input['type']=='add'){
                    if(empty($favorite)){
                        $favorite = Favorite::create([
                            'user_id' => $user->id,
                            'model' => $input['model'],
                            'article_id' => $input['article_id'],
                        ]);
                    }
                    $msg = ['error'=>0,'message'=>'收藏成功！'];
                }
                if($input['type']=='del'){
                    if($favorite){
                        Favorite::destroy($favorite->id);
                    }
                    $msg = ['error'=>0,'message'=>'取消成功！'];
                }
                break;
            case 'person':

                break;
            case 'project':

                break;
            default :
                break;
        }
        return $msg;
    }
}
