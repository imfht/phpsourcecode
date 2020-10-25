<?php

namespace App\Http\Controllers\Admin;

use App\Biji;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DataManageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.dataManage');
    }

    public function chart(){
        $data = array();
        $users = User::where('auth',0)->get();
        //统计所有用户分享笔记数
        $i = 0;
        foreach($users as $user){
            $userName = $user->name;
            $count = Biji::where('user_id',$user->id)->where('share',1)->count();
            /*$data = array_push($data,array('userName'=>$userName,'count'=>$count));*/
            $data[$i++] = array($userName,$count);
        }
        //逆序排序数组
        $flag=array();
        foreach($data as $arr){
            $flag[]=$arr[1];
        }
        array_multisort($flag, SORT_DESC, $data);
        /*rsort($data);*/
       /* $new_array = array();
        $new_sort = array();
        foreach($data as $key => $value){
            $new_array[] = $value;
        };
        arsort($new_array);
        foreach($new_array as $k => $v){
            foreach($data as $key => $value){
                if($v==$value){
                    $new_sort[$key] = $value;
                    unset($data[$key]);
                    break;
                }
            }
        }*/
        return response()->json(array(
            'counts' => $data
        ));
    }

}
