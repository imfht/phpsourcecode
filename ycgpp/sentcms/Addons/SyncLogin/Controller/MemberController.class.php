<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015-09-16
 * Time: 14:00
 */
namespace Addons\SyncLogin\Controller;
use Common\Api\UserApi as UserApi;

class MemberController extends \User\Controller\AddonsController{
    public function SyncLogin(){
        if(!session('user_auth')){
            $this->error('需要登录',U('Public/login'));
        }
        S('USER_AUTH_UID',null);
        //实例化一个第三方数据库
        $model = D('sync_login');
        //实例化一个用户控制器类
        //设置模板的左导航
        //查询所有信息，注入模板
        $map['uid'] = session('user_auth')['uid'];
        $info = $model->field('uid,type')->where($map)->select();
        foreach($info as $key=>$value){
            $userconfig[$value['type']] = $value['type'];
        }
        $this->assign('uid',$map['uid']);
        $this->assign('info',$userconfig);
        //调用绑定钩子
        Hook('SyncBind');
    }


    /**
     * 第三方帐号集成 - 绑定本地帐号
     * @return void
     */
    public function dobind(){
        header('Content-type:text/html;charset="utf-8"');
        //判断类型是否存在
        S('USER_AUTH_UID',null);
        if(!I('get.type'))$this->error('类型不存在');
        $uid = D('Member')->where(I('get.uid'))->select();
        if($uid > 0 ) {
            //注册来源-第三方帐号绑定
            if(isset($_POST)){
                //获取uid
                $other['uid'] = $uid[0]['uid'];
                $other['type'] = strtoupper(I('get.type'));
                $user_info_sync_login = D('sync_login')->where($other)->select(); // 根据openid等参数查找同步登录表中的用户信息
                if($user_info_sync_login){
                    //如果已经绑定了，那么就返回，什么也不做
                    $this->success('此'.$other['type'].'账号已被绑定！','',2);
                    exit;
                }else{
                    //设置缓存
                    S('USER_AUTH_UID',I('get.uid'));
                    //判断类型，跳转不一致的第三方登录接口地址
                    //重新绑定，重新点击QQ登录按钮，重新配置
                    header('Location:'.addons_url('SyncLogin://Base/login',array('type'=>strtolower(I('get.type')))));
                }
            }else{
                $this->error('绑定失败，第三方信息不正确');
                exit;
            }
        }else{
            $this->error('绑定失败，找不到此用户');
            exit;
        }

    }

     /**
     * 第三方帐号集成 - 取消绑定本地帐号
     * @return void
     */
    public function cancelbind(){
        //实例化第三方数据库
        $ret = D('sync_login');
        //如果传入的get  uid 不存在
        if(!I('get.uid')){
            //提示
            $this->error('参数有误！','',2);
        }
        if(!I('get.type')){
            //提示
            $this->error('参数有误！','',2);
        }
        $map['type'] = strtoupper(I('get.type'));
        $map['uid'] = I('get.uid');
        //如果删除成功
        $del = $ret->where($map)->delete();
        if($del){
            //提示
            $this->success('取消绑定成功','',1);
        }else{
            //提示
            $this->error('取消绑定失败','',1);
        }
    }
}