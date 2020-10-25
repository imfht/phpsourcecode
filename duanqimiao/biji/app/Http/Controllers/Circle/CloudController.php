<?php

namespace App\Http\Controllers\Circle;

use App\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CloudController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
     public function tagsCloud($id){
         if(\Auth::check()) {
             $tagsCloud = Tag::where('id', $id)->get();
             $bijis_id = \DB::table('biji_tag')->select('biji_id')->where('tag_id', $id)->get();
             return view('circle.tagsCloud', compact('tagsCloud', 'bijis_id'));
         }else{
             return redirect('/auth/login');
         }
     }
}
