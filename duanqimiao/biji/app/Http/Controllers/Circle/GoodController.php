<?php

namespace App\Http\Controllers\Circle;

use App\Good;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GoodController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function good(){
        $isGood = Good::where('user_id',\Auth::id())->where('biji_id',$_GET['biji_id'])->get();
        /*如果当前用户点赞记录在数据库中已存在则无法点赞*/
        if($isGood->isEmpty()){
            \DB::table('goods')->insert([
                "user_id" =>\Auth::id(),
                "biji_id" => $_GET['biji_id'],
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
            return response()->json(array(
                'good' => true
            ));
        }else{
            return response()->json(array(
                'good' => false
            ));
        }
    }
}
