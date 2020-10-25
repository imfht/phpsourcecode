<?php
namespace app\index\controller;

use app\common\model\User AS UserModel;
use app\common\controller\IndexBase;
use think\Controller;

class Getpassword extends IndexBase
{
    /**
     * 获取邮箱或手机注册码
     * @param string $type 可选参数 mobphone email
     * @param string $to 加密后的邮箱或手机号
     */
    public function getnum($type='',$to=''){
        
        $to = mymd5($to,'DE'); //解密手机或邮箱号

        if (!$to) {
            return $this->err_js($type=='mobphone'?'没绑定手机':'没绑定邮箱');
        }
        
        if( time()-cache('send_num'.$to) <60 ){
            return $this->err_js('1分钟后,才能再次获取验证码!');
        }
        
        $send_num = cache('get_password'.$to) ?: rand(100000,999999);
        cache('get_password'.$to,$send_num,600);
        
        if($type=='mobphone'){
            $result = send_sms($to,$send_num);
            $msg = '验证码已成功发出,请耐心等候,注意查收手机号: '.substr($to,0,8).'***';
        }elseif($type=='email'){
            $title = '来自《'.config('webdb.webname').'》的验证码,请注册查收';
            $content = '你的验证码是:'.$send_num;
            $result = send_mail($to,$title,$content);
            $msg = '验证码已成功发出,请耐心等候,注意查收邮箱: '.substr($to,0,2).'***'.strstr($to,'@');
        }else{
            $result = '请选择类型!';
        }        
        if($result===true){
            cache('send_num'.$to, time(),600);
            return $this->ok_js([],$msg);
        }else{
            return $this->err_js($result);
        }
    }
    
    /**
     * 核对手机或邮箱注册码
     * @param string $num 验证码
     * @param string $field 加密后的邮箱或手机号
     * @return void|\think\response\Json
     */
    public function check_num($num='',$field=''){
        $field = mymd5($field,'DE'); //解密手机或邮箱号
        if (!$num) {
            return $this->err_js('验证码不能为空');
        }elseif(!$field){
            return $this->err_js('参数不全');
        }
        if( $num == cache('get_password'.$field) ){
            return $this->ok_js();
        }
        return $this->err_js('验证码不正确');
    }
    
    /**
     * 获取用户信息及图形验证码的验证
     */
    public function check($username=''){
        if($username!=''){
            $info = UserModel::get_info($username,'username');
            if($info){
                $array = [
                    'uid'=>$info['uid'],
                    'email'=>$info['email']?mymd5($info['email']):'',
                    'mobphone'=>$info['mobphone']?mymd5($info['mobphone']):'',
                ];
                return $this->ok_js($array);
            }else{
                return $this->err_js('用户不存在!');
            }
        }else{
            $data = get_post('get');
            if(isset($data['captcha'])&&$data['captcha']==''){
                $data['captcha'] = 'test';
            }
            foreach($data AS $key=>$value){
                $name = $key;
                break;
            }
            $result = $this->validate($data, 'Reg.'.$name);
            if( $result!==true ){
                return $this->err_js($result);
            }else{
                return $this->ok_js();
            }            
        }		
    }
    
    /**
     * 取回密码
     * @return mixed|string
     */
    public function index()
    {
        if ($this->user) {
            $this->error('你已经登录了!');
        }
        
        $data = get_post('post');

        $this->get_hook('getpassword_begin',$data);
        hook_listen('getpassword_begin',$data);		
        
        if(IS_POST){
            $info = UserModel::get_info($data['username'],'username');
            if(!$info){
                $this->error('帐号不存在');
            }
            
            if($data['captcha']==''){
                $data['captcha'] = 'test';
            }
            $result = $this->validate($data, 'Reg.captcha');
            if(true !== $result) $this->error($result);
            
            
            if (!$data['email_code'] && !$data['phone_code']) {
                $this->error('验证码不能为空');
            }
            if ($data['num_type']=='mobphone') {                
                $getcache = cache('get_password'.$info['mobphone']);
                cache('get_password'.$info['mobphone'],null);
                if( $getcache != $data['phone_code'] || empty($data['phone_code'])) {
                    $this->error('手机验证码不对,请重新获取');
                }                
            }else{                
                $getcache = cache('get_password'.$info['email']);
                cache('get_password'.$info['email'],null);
                if( $getcache != $data['email_code'] || empty($data['email_code'])) {
                    $this->error('邮箱验证码不对,请重新获取');
                }                
            }
            
            $array = [
                    'uid'=>$info['uid'],
                    'password'=>$data['password'],
            ];
            
            $result = UserModel::edit_user($array); 

            $this->get_hook('getpassword_end',$data,$info);
            hook_listen('getpassword_end',$info,$data);			
            
            if($result){
                $this->success('密码设置成功','index/index');
            }else{
                $this->error('密码设置失败！');
            }
        }
		return $this->fetch();
    }
}
