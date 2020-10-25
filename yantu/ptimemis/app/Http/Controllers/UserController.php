<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Session;
use Storage;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

class UserController extends BaseController{

    /**
     * check session.
     */
    public function check(){

        $sessionData = Session::all();
        if(!$sessionData['id']){
            Session::flush();
            Session::save();
            return $this::jsonResponse(true,'','用户验证失败');
        }
        $userResult = DB::select("select * from user where name = '".$sessionData['name']."'");
        if(!$userResult){
            return $this::jsonResponse(true,'','用户不存在');
        }
        $permission = DB::table('role_permission')
                        ->join('permission','permission.id','=','role_permission.permission_id')
                        ->where('role_permission.role_id','=',$userResult[0]->role_id)
                        ->select('permission_id','permission.name as permission_name','permission.object')
                        ->get();            
        $result = ['id'=>$sessionData['id'],'name'=>$sessionData['name'],'role_id'=>$userResult[0]->role_id,'permission'=>$permission];
        return $this::jsonResponse(false,$result);
    }

    
	/**
     * User login.
     */
    public function login(Request $request){
    	$userName = $request->userName;
    	if(!$userName){
    		return $this::jsonResponse(true,'','请输入用户名');
    	}
    	$userResult = DB::select("select id,password from user where name = '".$userName."'");
    	if(!$userResult){
    		return $this::jsonResponse(true,'','用户不存在');
    	}
    	$passWord = $request->passWord;
    	if(!$passWord){
    		return $this::jsonResponse(true,'','请输入密码');
    	}
    	if(Hash::check($passWord,$userResult[0]->password)){
    		Session::put('id',$userResult[0]->id);
    		Session::put('name',$userName);
    		Session::put('password',$passWord);
            Session::save();
    		return $this::jsonResponse(false,'','登录成功');
    	}else{
    		return $this::jsonResponse(true,'','密码错误');
    	}
    }



    /**
     * User logout.
     */
    public function logout(Request $request){
        Session::flush();
        Session::save();
        return $this::jsonResponse(false,'','注销成功');
    }

    /**
     * set password.
     */
    public function password(Request $request,$id){
        $data = $request->all();
        if(!$data){
            return $this::jsonResponse(true,'','请输入密码');
        }
        $passOld = $data['passOld'];
        $passNew = $data['passNew'];
        $passRepeat = $data['passRepeat'];
        $password = DB::select("select password from user where id='".$id."'");
        if($passOld == $passNew){
            return $this::jsonResponse(true,'','与原密码相同！');
        }
        if($passNew != $passRepeat){
            return $this::jsonResponse(true,'','两次密码不一致！');
        }   
        if($passOld == $password[0]->password){
            $password = Hash::make($passNew);
            DB::table('user')->where('id',$id)->update(array('password'=>$password));
            return $this::jsonResponse(false,'','密码修改成功');
        }else{
            return $this::jsonResponse(true,'','密码修改失败');
        }
    }
 
}
