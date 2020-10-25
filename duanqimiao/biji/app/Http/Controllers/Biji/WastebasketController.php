<?php

namespace App\Http\Controllers\Biji;

use App\Biji;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WastebasketController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wastebasket(){
        if(\Auth::check()) {
            $wastebasket = Biji::where('user_id', \Auth::id())->where('wastebasket', 1)->get();
            $id = 1;
            return view('biji.actions.wastebasket', compact('wastebasket', 'id'));
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function recover($id){
        Biji::where('id',$id)->update(['wastebasket'=>0]);
        return response(array(
            "wastebasket" => Biji::where('user_id',\Auth::id())->where('wastebasket',1)->get()
        ));
    }

    public function clear($id){
        $biji = Biji::where('id',$id);
        $biji->delete();
        return response(array(
            "wastebasket" => Biji::where('user_id',\Auth::id())->where('wastebasket',1)->get()
        ));
    }

}
