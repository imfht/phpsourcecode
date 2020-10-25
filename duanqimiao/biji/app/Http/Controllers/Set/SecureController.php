<?php

namespace App\Http\Controllers\Set;

use App\Ip;
use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SecureController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function secure(){
        if(\Auth::check()) {
            $user = User::where('id', \Auth::id())->first();
            $pass = \DB::select('select * from password_resets where email = :email', ['email' => $user->email]);
            foreach ($pass as $day) {
                $sub = ceil((strtotime(date("Y-m-d")) - strtotime($day->created_at)) / 86400);
            }
            return view('setting.secure', compact('user', 'sub'));
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * @param Requests\ModifyPassRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function modify(Requests\ModifyPassRequest $request){
        $old_pass = $request->input('old_pass');
        $new_pass = $request->input('new_pass');
        $confirm_new_pass = $request->input('confirm_new_pass');
        $pass = User::select('password')->where('id',\Auth::id())->first();

        if($old_pass==""||$new_pass==""||$confirm_new_pass==""){
            return response()->json(array(
                'info'=>'必填项不能为空！'
            ));
        }else if(!($new_pass === $confirm_new_pass)){
            return response()->json(array(
                'info'=>'密码错误！'
            ));
        }else if(!(\Hash::check($old_pass,$pass->password))){
            return response()->json(array(
                'info'=>'原始密码不正确！'
            ));
        }
        else{
            \DB::table('users')->where('id',\Auth::id())->update(['password'=>\Hash::make($new_pass)]);
            return response()->json(array(
                'info'=>'已成功修改密码，下次登录时生效！'
            ));
        }
    }
}
