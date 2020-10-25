<?php
namespace app\home\controller;

use app\BaseController;
use think\facade\Db;
use think\exception\ValidateException;

class Login extends BaseController
{
    /*
     * 登录模块
     *
     * */
    public function index()
    {
        if(request()->isAjax()){
            $data = input('param.');
            //验证数据
            try {
                validate(\app\index\validate\Login::class)->check($data);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->return_json($e->getError(),'0');
            }
            //检验账号和密码是否正确
            $user = Db::table('admin')->where('username', $data['username'])->find();
            if($user){
                //员工的账号
                if(!empty($user['building_id'])){
                    if($user['password'] == md5($data['password'])){
                        session('admin', $user['username']);
                        session('classe', $data['classes']);
                        return $this->return_json('登录成功','200');
                    }else{
                        return $this->return_json('密码错误','0');
                    }
                }else{
                    //管理者账号
                    if($user['password'] == md5($data['password'])){
                        session('admin', $user['username']);
                        return $this->return_json('登录成功','100');
                    }else{
                        return $this->return_json('密码错误','0');
                    }
                }
            }else{
                return $this->return_json('账号不存在','0');
            }
        }
        $list = Db::table('classes')->select();

        return view('index',['list' => $list]);
    }

    /*
     * 退出登录
     * */
    public function logout()
    {
        session(null);
        return redirect('/home/login/index');
    }
    /*
     * 返回json数据
     * $msg（提示信息）
     * $code（状态码）
     * */
    public function return_json($msg,$code){
        return json([
            'msg' => $msg,
            'code' => $code
        ]);
    }
}
