<?php

namespace App\Http\Controllers\Set;

use App\Star;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FedBackController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function back(){
        if(\Auth::check()) {
            return view('setting.back');
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(){
        $isStar = Star::where('user_id',\Auth::id())->get();
        if($isStar->isEmpty()){
            Star::create([
                "user_id" => \Auth::id(),
                'stars'=>$_GET['count'],
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
             ]);
        }else{
            Star::where('user_id',\Auth::id())->update(['stars'=>$_GET['count']]);
        }
        return response()->json(array(
            'info' => "谢谢！您的反馈已提交"
        ));
    }

}
