<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\logic\Member;
use think\Controller;
use app\ucenter\logic\UcenterMember;
use app\common\model\Config;
/**
 * 后台基础控制器
 * @author Patrick <contact@uctoo.com>
 */
class Base extends Controller {

    /**
     * 后台用户登录
     * @author Patrick <contact@uctoo.com>
     */
    public function login(){
        if($this->request->isPost()){
            $username = input('username');
            $password = input('password');
            $verify = input('verify');

            /* 检测验证码 TODO: */
            if (config('app_debug')==false){
                if(!captcha_check($verify)){
                    $this->error(lang('_VERIFICATION_CODE_INPUT_ERROR_'));
                }
            }
            /* 系统级用户登录 UcenterMember */
            $User = new UcenterMember;
            $uid = $User->login($username, $password);
            if(0 < $uid){ //UC登录成功
                /* 应用级用户登录 */
                $Member = new Member();
                if($Member->login($uid)){ //登录用户
                    //TODO:跳转到登录前页面
                    $data = ['name'=>'UCToo','url'=>'uctoo.com'];
                    return ['data'=>$data,'status'=>true,'message'=>lang('_LOGIN_SUCCESS_'),'url'=>url('Index/index')];
                } else {

                    $this->error($Member->getError());
                }
            } else { //登录失败
                switch($uid) {
                    case -1: $error = lang('_USERS_DO_NOT_EXIST_OR_ARE_DISABLED_'); break; //系统级别禁用
                    case -2: $error = lang('_PASSWORD_ERROR_'); break;
                    default: $error = lang('_UNKNOWN_ERROR_'); break; // 0-接口参数错误（调试阶段使用）
                }
                $data = ['name'=>'UCToo','url'=>'uctoo.com'];
                return ['data'=>$data,'status'=>false,'message'=>$error,'url'=>url('Index/index')];
            }

        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
                $config	=	cache('DB_CONFIG_DATA');
                if(!$config){
                    $config	= new Config();
                    $configData = $config ->lists();
                    cache('DB_CONFIG_DATA',$configData);
                }
                config($config); //添加配置

                return $this->fetch();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            $Member = new Member();
            $Member->logout();
            session('[destroy]');
            $this->success(lang('_EXIT_SUCCESS_'), url('login'));
        } else {
            $this->redirect('login');
        }
    }


}