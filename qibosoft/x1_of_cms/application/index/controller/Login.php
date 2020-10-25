<?php
namespace app\index\controller;

use app\common\model\User AS UserModel;
use app\common\controller\IndexBase;
use QCloud_WeApp_SDK\Auth\LoginService;
use QCloud_WeApp_SDK\Constants as Constants;

class Login extends IndexBase
{
    /**
     * 框架套壳小程序登录
     * @param string $type 值为wx的时候,要判断当前是否微信登录,不是的话,就强制退出,再用微信登录
     * @return mixed|string
     */
    public function wxapp($type=''){
//         if ($type=='wx' && $this->user){
//             UserModel::quit($this->user['uid']);
//             weixin_login();
//         }
        if (empty($this->user)) {
            weixin_login();
        }else{
            $user = $this->user;
            $token = md5( $user['uid'] . $user['lastip'] . $user['lastvist'] );
            cache($token,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",60);
            $this->assign('token',$token);
            return $this->fetch();
        }        
    }
    
    /**
     * 原生小程序检查是否已登录过
     * @return \think\response\Json
     */
    public function wxapp_check(){
        $result = LoginService::check();        
        if ($result['loginState'] === Constants::S_AUTH) {
            return json([
                    'code' => 0,
                    'data' => $result['userinfo']
            ]);
        } else {
            return json([
                    'code' => -1,
                    'data' => []
            ]);
        }
    }
    
    /**
     * 原生小程序登录
     * @return \think\response\Json
     */
    public function wxapp_login(){
        $result = LoginService::login();        
        if ($result['loginState'] === Constants::S_AUTH) {
            return json([
                    'code' => 0,
                    'data' => $result['userinfo']
            ]);
        } else {
            return json([
                    'code' => -1,
                    'error' => $result['error']
            ]);
        }
    }
    
    public function quit()
    {
        if (empty($this->user)) {
            $this->error('你还没登录！','index/login/index');
        }
        UserModel::quit($this->user['uid']);
        $this->success('成功退出','index/login/index');
    }
    

    /**
     * 帐号登录
     * @param string $fromurl 返回地址
     * @param string $type 等于iframe 是框架登录
     * @return mixed|string
     */
    public function index($fromurl='',$type='')
    {
        if (!empty($this->user)) {
            if($type=='iframe'){
                return $this->fetch('ok');
            }
            $this->error('你已经登录了','index/index','',1);
        }
        if(IS_POST){
            $data= get_post('post');
            if(empty($data['cookietime'])){
                $data['cookietime'] = $this->webdb['login_time']?:3600*24*30;
            } 
            $result = UserModel::login($data['username'],$data['password'],$data['cookietime']);
            if($result==0){
                $this->error("当前用户不存在,请重新输入");
            }elseif($result==-1){
                $this->error("密码不正确,点击重新输入");
            }elseif(is_array($result)){
                if($type=='iframe'){
                    return $this->fetch('ok');
                }
                $jump = $fromurl ? urldecode($fromurl) : iurl('index/index/index');
                $this->success('登录成功',$jump);
            }else{
                $this->error("未知错误");
            }
        }
        if($type=='iframe'){
            $fromurl = url('index').'?type='.$type;
        }
        if(strstr($this->fromurl,'index/login/quit')){
            $this->fromurl = url('index/index/index');
        }
        $this->assign('fromurl',urlencode(filtrate($fromurl?:$this->fromurl)));
        $this->assign('type',$type);
        return $this->fetch($type=='iframe'?'iframe':'index');
    }
    
    /**
     * 手机登录或注册
     */
    public function phone(){
        $url = iurl('index/index/index');
        if($this->fromurl && !strstr($this->fromurl,'index/login') && !strstr($this->fromurl,'index/reg')){
            $url = filtrate($this->fromurl);
        }
        $this->assign('fromurl',urlencode($url));
        return $this->fetch();
    }
    
    /**
     * 获取手机验证码
     * @param string $phone
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function get_phone_num($phone=''){
        if( time()-get_cookie('send_num') <60 ){
            return $this->err_js('1分钟后,才能再次获取验证码!');
        }elseif( time()-cache('send_num'.md5(get_ip()))<60){
            return $this->err_js('1分钟后,当前IP才能再次获取验证码!');
        }
        $send_num = $this->webdb['phone_rand_num_complex'] ? get_md5_num($phone.rands(5) , 6) : rand(100000,999999);
        $content = '你的验证码是:'.$send_num;
        cache('phone_login'.$send_num,$phone,1800);
        
        $result = send_sms($phone,$send_num);

        if($result===true){
            set_cookie('send_num', time());
            cache('send_num'.md5(get_ip()),time(),100);
            return $this->ok_js();
        }else{
            return $this->err_js($result);
        }
    }
    
    /**
     * 核对手机验证码,如果帐号存在,就直接登录
     * @param string $num
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function check_phone_num($num=''){
        $phone_num = cache('phone_login'.$num);
        if (empty($num)) {
            return $this->err_js('请输入验证码');
        }elseif(empty($phone_num)){
            return $this->err_js('验证码不正确!');
        }
        $info = UserModel::get_info($phone_num,'mobphone');
        if ($info) {    //已注册过的手机号
            cache('phone_login'.$num,null);
            $result = UserModel::login($phone_num,'',3600*24*7,true,'mobphone');
            if (is_array($result)) {
                $token = md5( $result['uid'] . $result['lastip']  . $result['lastvist'] );
                cache($token,"{$result['uid']}\t{$result['username']}\t".mymd5($result['password'],'EN')."\t",60);
                return $this->ok_js([
                    'token'=>$token,
                    'type'=>'login',
                    'uid'=>$result['uid'],
                    'username'=>$result['username'],
                    ],'登录成功');
            }else{
                return $this->err_js('系统故障,登录失败!');
            }
        }else{  //新用户,提示注册一个帐号
            return $this->ok_js(
                    [
                            'type'=>'reg',
                            'uid'=>0,
                            'username'=>substr($phone_num,9),
                    ],'验证输入正确');
        }
    }
    
    public function phone_reg()
    {
        if ($this->webdb['forbid_normal_reg']) {
            $this->error('系统关闭了手工注册功能,你可以选择QQ登录或微信登录!!');
        }
        if ($this->user) {
            $this->error('你已经注册过了!');
        }
		
		$this->get_hook('reg_by_hand_begin',$data=[]);
		hook_listen('reg_by_hand_begin',$data=[]);		
        
        if(IS_POST){
            
            $data = get_post('post');
            if(!empty($data)){
                $array = explode(',','username,password,password2,email,mobphone,captcha,email_code,phone_code,weixin_code,fromurl');  //允许注册的字段
                foreach($data AS $key=>$value){
                    if(!in_array($key, $array)){
                        unset($data[$key]);
                    }
                }
                if(isset($this->webdb['RegYz'])){
                    $data['yz'] = $this->webdb['RegYz'];
                }
                $data['money'] = $this->webdb['regmoney'];
            }
            
            $phone_num = cache('phone_login'.$data['phone_code']);
            if (empty($data['phone_code'])) {
                $this->error('请输入验证码');
            }elseif(empty($phone_num)){
                $this->error('验证码不正确!');
            }
            cache('phone_login'.$data['phone_code'],null);
            $data['mobphone'] = $phone_num;     //避免用户中途换号码
            
            if( UserModel::get_info( $phone_num , 'mobphone')){
                $this->error('当前手机号已经注册过了,请直接登录即可!');
            }
            
            $data['email'] || $data['email'] = substr($phone_num,7).rands(3).'@phone.cn';
            $data['password'] || $data['password'] = rands(6);
            $data['mob_yz'] = 1;
            
            
            // 验证
//             $result = $this->validate($data, 'Reg');
//             if(true !== $result) $this->error($result);
            
            $uid = UserModel::register_user($data); //注册帐号
            if ($uid<2) {
                $this->error($uid);
            }

            $this->get_hook('reg_by_hand_end',$data,[],['uid'=>$uid]);
            hook_listen('reg_by_hand_end',$uid,$data);			
            
            $result = UserModel::login($phone_num,'',3600*24*7,true,'mobphone');   //帐号同时实现登录
            if(is_array($result)){
                $url = murl('member/index/index');
                if($data['fromurl'] && !strstr($data['fromurl'],'index/login')){
                    $url = $data['fromurl'];
                }
                $token = md5( $result['uid'] . $result['lastip']  . $result['lastvist'] );
                cache($token,"{$result['uid']}\t{$result['username']}\t".mymd5($result['password'],'EN')."\t",60);
                $this->success('注册成功',$url,$token);
            }else{
                $this->error('注册失败！');
            }
        }
        return $this->fetch();
    }
    
}
