<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Request;
use Theme;
use Logs;
use Redirect;
use Hash;

class PasswordController extends Controller
{
    public function index()
    {
        return Theme::view('user.password.show');
    }

    public function store(Request $request)
    {
        $input = Request::only(['password_old', 'password', 'password_confirmation']);
        $rules = array(
            'password' => 'required|confirmed|between:6,20',
        );
        $message = array(
            'password.confirmed' => '重复密码不一致',
            "required"           => ":attribute 不能为空",
            "unique"           => ":attribute 已被使用",
            "between"            => ":attribute 长度必须在 :min 和 :max 之间"
        );
        $attributes = array(
            'password' => '新密码',
        );
    
        $validator = Validator::make(
            $input,
            $rules,
            $message,
            $attributes
        );
        if ($validator->fails()) {
          return Redirect::back()->withInput()->withErrors($validator);
        }
    
        $credentials = ['name'=>auth()->user()->name,'password'=>$input['password_old']];
        if(!auth()->validate($credentials)){
          $validator->errors()->add('password_old','原密码不正确');
          return Redirect::back()->withInput()->withErrors($validator);
        }
    
        $thisUser = User::find(auth()->user()->id);
        $thisUser->password = Hash::make($input['password']);
        $thisUser->save();
        Logs::save('password',$thisUser->id,'update','用户修改密码');
        auth()->logout();
        $message = '密码已变更，请重新登陆！';
        $url = ['返回首页'=>['url'=>url('/'),'style'=>'primary'],'重新登陆'=>['url'=>url('auth/login'),'style'=>'default']];
        return Theme::view('message.show',compact(['message','url']));
    }
}
