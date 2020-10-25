<?php

namespace App\Http\Controllers\Biji;

use App\Biji;
use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class LinkController extends Controller
{
    public function show($id){
        $biji = Biji::where('id',$id)->where('user_id',\Auth::id())->first();
        $user = User::where('id',\Auth::id())->first();
        return view('biji.partials.link',compact('biji','user'));
    }
}
