<?php

namespace App\Http\Controllers\Circle;

use App\Biji;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        //获得当前用户查找笔记
        if(!($_GET['search']=="")){
            //显示当前用户要查找的笔记
            return response()->json(array(
                'bijis' => Biji::select('title','id')->where('title','like','%'.$_GET['search'].'%')->where('share','=',1)->get()
            ));
        }else{
            //默认显示当前用户所有笔记
            return response()->json(array(
                'bijis' =>Biji::select('title','id')->where('share','=',1)->get()
            ));
        }
    }
}
