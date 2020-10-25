<?php
namespace Api\Controller;
use Think\Controller;
class OauthController extends Controller {
    //登录地址
    public function login($type = null){
        empty($type) && $this->error('参数错误');
        $_SESSION['login_http_referer']=$_SERVER["HTTP_REFERER"];
        // 获取对象实例
        $sns  = \Cp\Sys\Oauth::getInstance($type);
        //跳转到授权页面
        redirect($sns->getRequestCodeURL());
    }

    //授权回调地址
    public function callback($type = null, $code = null){
        (empty($type)) && $this->error('参数错误');

        if(empty($code)){
            redirect(__ROOT__."/");
        }

        $sns  = \Cp\Sys\Oauth::getInstance($type);
        //腾讯微博需传递的额外参数
        $extend = null;
        // 获取TOKEN
        $token = $sns->getAccessToken($code , $extend);

        //获取当前第三方登录用户信息
        if(is_array($token)){
            $user_info = \Cp\Sys\GetInfo::getInstance($type,$token);
            // 获取第三方用户资料成功
            sys_p($user_info);
        }else{
            echo "获取第三方用户的基本信息失败";
        }
    }
}