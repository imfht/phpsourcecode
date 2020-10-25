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
        $type= strtoupper(I('get.type'));
        $sns  = \ThinkOauth::getInstance($type);

        //腾讯微博需传递的额外参数
        $extend = null;
        if($type == 'tencent'){
            $extend = array('openid' => I('get.openid'), 'openkey' =>  I('get.openkey'));
        }
        //获取token
        $token = $sns->getAccessToken($code , $extend);
        $user_info = D('Addons://SyncLogin/SyncLogin')->$type($token); //获取传递回来的用户信息
        $condition = array(
            'openid' => $token['openid'],
            'type' => $type,
            'status' => 1,
        );
        //绑定账号
        if(S('USER_AUTH_UID')){
            $uid = S('USER_AUTH_UID');
            $map['access_token'] = $token['access_token'];
            $map['openid'] = $token['openid'];
            $map['type'] = $type;
            $map['status'] = 1;
            $sync =D('sync_login');
            $user = $sync->where($map)->select();
            //找到。说明已经被绑定了
            if($user){
                $this->error('此'.$type.'账号已被绑定！',addons_url('SyncLogin://Member/SyncLogin'),2);
                exit;
            }else{
                //查找此用户是否存在
                $model = D('Member')->where($uid)->select();
                if($model){
                    $map['uid'] = $uid;
                    $map['refresh_token'] = $token['refresh_token'];
                    S('USER_AUTH_UID',null);

                    if($sync->add($map)){
                        $this->success('绑定成功,正在返回...',addons_url('SyncLogin://Member/SyncLogin'),2);
                        exit;
                    }else{
                        $this->error('绑定失败，第三方信息不正确',addons_url('SyncLogin://Member/SyncLogin'),2);
                    }
                }else{
                    $this->error('sorry,找不到此用户，无法绑定');
                }
            }
        }

        //有个问题？那么 如果说有sian 和 qq 两个第三方登录的返回。改怎么做呢？
        //查找用户的唯一openid，uid, 是否存在，
        $user_info_sync_login = D('sync_login')->where($condition)->select(); // 根据openid等参数查找同步登录表中的用户信息
        if($user_info_sync_login){
            //用户存在
            $map['uid'] = $user_info_sync_login [0]['uid'];
            $map['type'] = $type;
            $user_info_user_center = D('Member')->where($map)->select(); //根据UID查找Ucenter中是否有此用户
            //存在用户，则重新获取一个access_token 一面用户的access_token过期
            if($user_info_user_center){
                $syncdata ['access_token'] = $token['access_token'];
                $syncdata ['refresh_token'] = $token['refresh_token'];
                $syncdata ['openid'] = $token['openid'];
                D('sync_login')->where($map)->save($syncdata); //更新Token
                $Member = D('Member');
                //保存数据后。进行登录
                if( $Member->login($user_info_user_center[0]['uid'],'',5) ){
                    //$this->assign('jumpUrl', U('Home/Index/index'));
                    $this->success('同步登录成功！',U('User/Index/index'));
                }else{
                    //否则抛出异常
                    $this->error($Member->getError());
                }
            }else{
                //如果不存在用户则删除掉此第三方登录的openid
                $condition = array(
                    'openid' => $token['openid'],
                    'type' => $type
                );
                D('sync_login')->where($condition)->delete();
            }
        }else{
            //没绑定过，去注册页面
            //如果不是。则执行原来的方法
            session( 'user', $user_info );
            session( 'token', $token );
            redirect(U('User/Public/register'));
        }
    }
}