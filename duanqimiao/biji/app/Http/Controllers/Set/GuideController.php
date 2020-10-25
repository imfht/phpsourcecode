<?php

namespace App\Http\Controllers\Set;

use App\Help;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GuideController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function guide(){
        if(\Auth::check()) {
            return view('setting.guide');
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function help($id){
        $count = Help::where('isHelp',true)->where('article_id',$id)->count();
        return view('setting.guide'.$id,compact('id','count'));
    }

    public function yes($id){
        $isRepeat = Help::select('id')->where('user_id',\Auth::id())->where('article_id',$id)->get();
        if($isRepeat->isEmpty()){
            \DB::table('helps')->insert([
                "user_id" =>\Auth::id(),
                "article_id" => $id,
                "isHelp" => true,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
            return response()->json(array(
                'help'=>true
            ));
        }else{
            return response()->json(array(
                'help'=>false
            ));
        }

    }

    public function no($id){
        $isRepeat = Help::select('id')->where('user_id',\Auth::id())->where('article_id',$id)->get();
        if($isRepeat->isEmpty()) {
            \DB::table('helps')->insert([
                "user_id" => \Auth::id(),
                "article_id" => $id,
                "isHelp" => false,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
            return response()->json(array(
                'help'=>true
            ));
        }else{
            return response()->json(array(
                'help'=>false
            ));
        }
    }
}
