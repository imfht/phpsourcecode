<?php
namespace app\user\controller;
use think\Db;
use OauthSDK\Oauth;
use think\Controller;
class Callback extends Controller{
    public function qq(){
        $type = input('type');
        $code = input('code');

        if (!in_array($type, ['qq', 'wechat', 'sina'])) {
            throw new \Exception("参数错误", 500);
        }
        if(empty($type) || empty($code)) {
            throw new \Exception('参数错误~',500);
        }
        $plugin =Db::name('plugin')->where(['type'=>'login','status'=>1])->select();
        foreach ($plugin as $k=>$v){
            $config[strtoupper($v['code'])]=unserialize($v['config_value']);
        }
        $sns = Oauth::getInstance($type, $config);
        $tokenArr = $sns->getAccessToken($code, []);
        $openid = $tokenArr['openid'];
        $token = $tokenArr['access_token'];
        //获取当前登录用户信息
        if ($openid) {
            $userinfo = $sns->getUserInfo();
            //查看已绑定数据
            $uid = Db::name('oauth')->where([['openid','=',$openid],['type','=',$type]])->value('uid');
            //如果用户已登录
            if (session('user.id')) {
                //绑定QQ
                if($uid){
                    $this->error('该QQ号已绑定账号！','index/index');
                }else{
                    if (session('user.avatar') == '') {
                        Db::name('users')->where('id', session('user.id'))->update(['avatar' => $userinfo['avatar']]);
                    }
                    if (session('user.username') == '') {
                        Db::name('users')->where('id', session('user.id'))->update(['username' => $userinfo['nickname']]);
                    }
                    $data['uid'] = session('user.id');
                    $data['openid'] = $openid;
                    $data['type'] ='qq';
                    Db::name('oauth')->insert($data);
                    Db::name('users')->where('id',session('user.id'))->update(['last_login'=>time()]);
                    $user = Db::name('users')->where('id',session('user.id'))->find();
                    $user['qq']='1';
                    session('user',$user);
                    $this->success('QQ号绑定成功！','index/index');
                }
            }else{
                //未登录
                if($uid){
                    //已绑定过
                    $user = Db::name('users')->where('id', $uid)->find();
                    if ($user['avatar'] == '') {
                        Db::name('users')->where('id', $uid)->update(['avatar' => $userinfo['avatar']]);
                    }
                    if ($user['username'] == '') {
                        Db::name('users')->where('id', $uid)->update(['username' => $userinfo['nickname']]);
                    }
                    Db::name('users')->where('id',$uid)->update(['last_login'=>time()]);
                    $user = Db::name('users')->where('id',$uid)->find();
                    $user['qq']='1';
                    session('user',$user);
                    $this->success('登录成功！','index/index');
                }else{
                    //未绑定过做注册且登录
                    $data['username'] =  $userinfo['nickname'];
                    $data['avatar'] =  $userinfo['avatar'];
                    $data['reg_time'] =  time();
                    $data['last_login'] =  time();
                    $data['password'] =  md5('123456');
                    $data['sex'] =  ($userinfo['gender']=='男')?1:0;
                    $data2['uid'] = Db::name('users')->insertGetId($data);
                    $data2['openid'] = $openid;
                    $data2['type'] = 'qq';
                    Db::name('oauth')->insert($data2);

                    $user = Db::name('users')->where('id',$data2['uid'])->find();
                    $user['qq']='1';
                    session('user',$user);

                    $this->success('登录成功！','index/index');
                }
            }
        } else {
            throw new \Exception("系统出错,请稍后再试！", 500);
        }
    }
}