<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 16:22
 */

namespace app\weixin\controller;


use app\weixin\validate\CodeValidate;
use service\AliyunService;
use think\Controller;
use think\facade\Cache;
use think\facade\Session;

class User extends Controller
{

    public function wxlogin(){
    $id = input('id');
    $nickname = input('nickname');
    $face = input('face');
    $list = db('system_user')->where('openid',$id)->find();
    if($list){
        //有值就是登录
        return json($list);
    }else{
        //没有值就新添加用户
        $data['openid']=$id;
        $data['nickname']=$nickname;
        $data['face']=$face;
        $data['login_ip']=request()->ip();
        $data['creattime']=time();
        db('system_user')->insert($data);
        $userId = db('system_user')->insertGetId($data);
        $res = db('system_user')->where('uid',$userId)->find();
        return json($res);
    }


    }

    public function getOpenId(){
        $code = input('code');
        $res = db('system_weixin')->where('type',2)->find();
        $appid = $res['appid'];
        $appsecret = $res['appsecret'];
        $c= file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=authorization_code");
        return json($c);
    }


    public function address(){
        $id = input('uid');
        $list = db('system_user_address')->where('uid',$id)->select();
        return json($list);
    }


    /**
     * 购物车使用
     */
    public function selectaddress(){
        $uid = input('uid');
        $adid = input('adid');
        $map[] = ['uid','=',$uid];
        if($adid){
            $map[] = ['adId','=',$adid];

        }
        $list = db('system_user_address')->where($map)->find();
        if($list){
            return json(['address'=>$list,'code'=>1]);
        }else{
            return json(['code'=>2]);
        }

    }


    /**
     * 小程序注册使用短信验证码
     */

    public function sendcode(){
        $phone = input('phone');

        $data['phone']=$phone;
        $validate = new CodeValidate();
        if (!$validate->scene('codes')->check($data)) {

         return json(['msg'=>$validate->getError(),'code'=>2]);
        }
        $code = rand_string(4,1);
        $Aliyun = new AliyunService();
        $res = $Aliyun->sendsms($phone,$code,'SMS_118215001');
        if($res['msg'] == 'OK'){
            Cache::set('codes',$code,3600);

            return json(['msg'=>'发送成功','code'=>1]);
        }else{
            return json(['msg'=>'发送失败','code'=>2]);
        }

    }

    //登录注册
    public function wxapplogin(){

        $phone = input('phone');
        $code = input('code');
        $data['phone']=$phone;
        $data['code']=$code;
        $validate = new CodeValidate();
        if (!$validate->scene('login')->check($data)) {

            return json(['msg'=>$validate->getError(),'code'=>2]);
        }


        $scode = Cache::get('codes');
        //return json(['msg'=>$scode,'code'=>2]);
        if ($scode != $code) {
            return json(['msg'=>'短信验证码错误','code'=>2]);

        }
        $user = db('system_user')->where('phone',$phone)->find();
        if($user){
            //如果有值就登录

            return json($user);
        }else{
            //如果没有值就添加用户并登录
            $data['phone']=$phone;
            $data['nickname']="幸福优鲜"+rand(6, 1);
            $data['login_ip']=request()->ip();
            $data['creattime']=time();
            $data['status']=1;
            db('system_user')->insert($data);
            $userId = db('system_user')->insertGetId($data);
            $res = db('system_user')->where('uid',$userId)->find();

            return json($res);
        }




    }

}