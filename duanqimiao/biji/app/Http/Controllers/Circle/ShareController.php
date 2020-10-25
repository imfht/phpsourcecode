<?php

namespace App\Http\Controllers\Circle;

use App\Biji;
use App\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function share(){
        $shares = Biji::where('share',1)->where('user_id',\Auth::id())->get();
        return view('circle.share',compact('shares'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id){
        \DB::table('bijis')->where('user_id',\Auth::id())->where('id',$id)->update(['share'=>0]);
        return response()->json(array(
            "share"=>Biji::where('user_id',\Auth::id())->where('share',1)->get()
        ));
    }
}
