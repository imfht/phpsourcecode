<?php

namespace App\Http\Controllers\Sign;

use App\Sign;

use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SignController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign()
    {
        $isSign = Sign::where('user_id',\Auth::id())->where('signed_at',Carbon::now()->format("Y-m-d"))->get();
        /*如果当前用户当天签到记录在数据库中已存在则无法签到*/
        if($isSign->isEmpty()){
            \DB::table('signs')->insert([
                "user_id" =>\Auth::id(),
                "signed_at" => Carbon::now()->format("Y-m-d"),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
            return response()->json(array(
                'sign' => true
            ));
        }else{
            return response()->json(array(
                'sign' => false
            ));
        }

    }
}
