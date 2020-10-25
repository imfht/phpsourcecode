<?php

namespace Addons\SyncLogin\Controller;
use Think\Hook;
use User\Api\UserApi;
use Home\Controller\AddonsController;
require_once(dirname(dirname(__FILE__))."/ThinkSDK/ThinkOauth.class.php");


class BaseController extends AddonsController{

    //登录地址
    public function login(){
        $type= I('get.type');
        empty($type) && $this->error('参数错误');
        //加载ThinkOauth类并实例化一个对象
        $sns  = \ThinkOauth::getInstance($type);
        //跳转到授权页面
        redirect($sns->getRequestCodeURL());
    }

    //登陆后回调地址
    public function callback(){
        $code =  I('get.code');
        $type= I('get.type');
        $sns  = \ThinkOauth::getInstance($type);

        //腾讯微博需传递的额外参数
        $extend = null;
        if($type == 'qq'){
            $extend = array('openid' => I('get.openid'), 'openkey' =>  I('get.openkey'));
        }

        $token = $sns->getAccessToken($code , $extend);
        $user_info = D('Addons://SyncLogin/SyncLogin')->$type($token); //获取传递回来的用户信息
        $condition = array(
            'openid' => $token['openid'],
            'type' => $type,
            'status' => 1,
        );
        $user_info_sync_login = D('sync_login')->where($condition)->find(); // 根据openid等参数查找同步登录表中的用户信息
        if($user_info_sync_login) {//曾经绑定过
            $user_info_user_center = D('UcenterMember')->find($user_info_sync_login ['uid']); //根据UID查找Ucenter中是否有此用户
            if($user_info_user_center){
                $syncdata ['access_token'] = $token['access_token'];
                $syncdata ['refresh_token'] = $token['refresh_token'];
                D('sync_login')->where( array('uid' =>$user_info_user_center ['id'] ) )->save($syncdata); //更新Token
                $Member = D('Member');                   
                if( $Member->login($user_info_user_center['id']) ){    
                    $this->assign('jumpUrl', U('Home/Index/index'));
                    $this->success('同步登录成功');                
                }else{
                    $this->error($Member->getError());
                }
            }else{
                $condition = array(
                    'openid' => $token['openid'],
                    'type' => $type
                );
                D('sync_login')->where($condition)->delete();
            }
        } else { //没绑定过，去注册页面
            $this->assign ( 'user', $user_info );
            $this->assign ( 'token', $token );
            $this->assign ( 'site_title', '注册绑定帐号' );
            $this->display(T('Addons://SyncLogin@./'.C('DEFAULT_THEME').'/reg'));
        }
    }

}