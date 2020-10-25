<?php

namespace App\Http\Controllers\Biji;

use App\Biji;
use App\Book;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MobileController extends Controller
{
    public function biji(){
        //根据笔记列表的id(默认获得ID为第一个)获得笔记本资源句柄
        if(!empty($_GET['biji_id'])){
            $list = Biji::where('id','=',$_GET['biji_id'])->first();
            $book_id = $list->book_id;
            $book = Book::where('id','=',$book_id)->first();
        }else{
            $One = Biji::select(\DB::raw('id'))->where('user_id', \Auth::id())->first();
            $list = Biji::where('id','=',$One->id)->first();
            $book_id = $list->book_id;
            $book = Book::where('id','=',$book_id)->first();
        }
        return response()->json($list);
    }
}
