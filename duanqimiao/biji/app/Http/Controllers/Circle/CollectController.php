<?php

namespace App\Http\Controllers\Circle;

use App\Collect;
use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CollectController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function collect($id){
        $isCollect = Collect::where('user_id',$id)->where('biji_id',$_GET['biji_id'])->get();
        /*如果当前用户收藏的笔记在数据库中已存在则无法收藏*/
        if($isCollect->isEmpty()){
            \DB::table('collects')->insert([
                "user_id" =>$id,
                "biji_id" => $_GET['biji_id'],
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
            return response()->json(array(
                'collect' => true
            ));
        }else{
            return response()->json(array(
                'collect' => false
            ));
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(){
        if(\Auth::check()) {
            $collects = Collect::where('user_id', \Auth::id())->get();
            return view('circle.collect', compact('collects'));
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id){
        Collect::where('biji_id',$id)->delete();
        return redirect('/collect/')
            ->withSuccess('成功删除一条收藏.');
    }
}
