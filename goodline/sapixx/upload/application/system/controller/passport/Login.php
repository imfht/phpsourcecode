<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 会员管理中心
 */
namespace app\system\controller\passport;
use app\common\facade\Alisms;
use app\common\event\Passport;
use app\common\model\SystemApis;
use app\common\model\SystemMember;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberCloud;
use Yurun\Util\HttpRequest;

class Login extends Common{
    
    /**
     * 会员首页
     */
    public function index(){
        if(request()->isPost()){
            $data = [
                '__token__'      => $this->request->param('__token__/s'),
                'captcha'        => $this->request->param('captcha/s'),
                'login_id'       => $this->request->param('login_id/s'),
                'login_password' => $this->request->param('login_password/s'),
            ];
            $validate = $this->validate($data,'Member.login');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            $result  = SystemMember::login($data);
            if($result){
                Passport::clearMiniapp();
                Passport::setlogout();
                //判断是不是子管理员
                $condition['is_lock'] = 0;
                if($result->bind_member_miniapp_id && $result->parent_id){
                    $condition['id']        = $result->bind_member_miniapp_id;
                    $condition['member_id'] = $result->parent_id;
                }else{
                    $condition['member_id'] = $result->id;
                }
                $miniapp = SystemMemberMiniapp::where($condition)->order('id desc')->find();
                if(!empty($miniapp)){
                    $param = [
                        'member_id'         => $result->parent_id,
                        'miniapp_id'        => $miniapp->miniapp_id,
                        'member_miniapp_id' => $miniapp->id,
                    ];
                    Passport::setMiniapp($param);
                }
                Passport::setLogin($result);
                return json(['code'=>200,'message'=>'登录成功','url' => url('system/passport.Index/index')]);
            }else{
                return json(['code'=>0,'message'=>'密码错误或账户已被锁定']);
            }
        }else{
            $wechataccount = SystemApis::Config('wechataccount');
            $view['wechataccount'] =  empty($wechataccount) || $wechataccount['qrcode_login'] == 0 ? 0 : 1;
            return view('passport/login/index')->assign($view);
        }
    }


    /**
     * 腾讯云市场登录
     * @return void
     */
    public function cloud(){
        $code      = $this->request->param('code');
        $signature = $this->request->param('signature');
        $config    = SystemApis::config('wechatcloud');
        if(empty($config) || empty($this->web)){
            return $this->error('腾讯云授权配置错误,请联系客服.');
        }
        if(md5($code.$config['encry_key']) != $signature){
            return redirect('https://www.qcloud.com/open/authorize?scope=login&app_id='.$config['app_id'].'&redirect_url=https://'.$this->web->url.'/system/passport/cloud.html');
        }
        $http = new HttpRequest;
        $strsign              =  [
            'SecretId'        => $config['secret_id'],
            'Action'          => 'GetUserAccessToken',
            'SignatureMethod' => 'HmacSHA1',
            'Nonce'           => getcode(5),
            'Timestamp'       => time(),
            'userAuthCode'    => $code,
            'Region'          =>'ap-beijing'
        ];
        ksort($strsign);
        $str = [];
        foreach ($strsign as $key=>$value) {
            if (isset($value) && !empty($value)) {
                $str[] = $key."=".trim($value);
            }
        }
        $paramstring = "GETopen.api.qcloud.com/v2/index.php?".join("&", $str);
        $signStr = base64_encode(hash_hmac('sha1', $paramstring, $config['secret_key'], true));
        $strsign['Signature'] = $signStr;
        $response = $http->get('https://open.api.qcloud.com/v2/index.php',$strsign);
        $json = json_decode($response->body());
        if($json->code != 0){
            return redirect('https://www.qcloud.com/open/authorize?scope=login&app_id='.$config['app_id'].'&redirect_url=https://'.$this->web->url.'/system/passport/cloud.html');
        }
        Passport::clearMiniapp();
        Passport::setlogout();
        $userinfo  = [];
        $userCloud = SystemMemberCloud::where(['openId' => $json->data->userOpenId])->find();
        if(!empty($userCloud)){
            $userinfo  = SystemMember::where(['id' => $userCloud->member_id])->find();
        }
        if(empty($userCloud) && empty($userinfo)){
            $data['username']      = '腾讯云'.getcode(5);
            $data['password']      = password_hash(md5($json->data->userOpenId),PASSWORD_DEFAULT);
            $data['safe_password'] = password_hash(md5('123456'),PASSWORD_DEFAULT);  
            $data['login_time']    = time();
            $data['login_ip']      = request()->ip();
            $data['update_time']   = time();
            $data['create_time']   = time();
            $userinfo =  SystemMember::create($data);
            if($userinfo){
                SystemMemberCloud::create(['member_id'=>$userinfo->id,'openId'=>$json->data->userOpenId,'unionId'=>$json->data->userUnionId,'create_time'=> time()]);
            }
        }else{
            $condition['is_lock'] = 0;
            $condition['member_id'] = $userinfo->id;
            $miniapp = SystemMemberMiniapp::where($condition)->order('id desc')->find();
            if(!empty($miniapp)){
                $param = [
                    'member_id'         => $userinfo->parent_id,
                    'miniapp_id'        => $miniapp->miniapp_id,
                    'member_miniapp_id' => $miniapp->id,
                ];
                Passport::setMiniapp($param);
            }
        }
        Passport::setLogin($userinfo);
        return redirect(url('system/passport.Index/index'));
    }

    /**
     * 会员注册
     */
    public function reg(){
        if(request()->isPost()){
            $data = [
                'captcha'        => $this->request->param('captcha/s'),
                'phone_id'       => $this->request->param('phone_id/s'),
                'login_password' => $this->request->param('login_password/s'),
                'sms_code'       => $this->request->param('sms_code/s'),
                'username'       => $this->request->param('username/s'),
            ];
            $validate = $this->validate($data,'Member.reg');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            $result  = SystemMember::reg($data);
            if($result['code'] == 200){
                $userinfo = SystemMember::where(['id' => $result['data']['id']])->find();
                Passport::clearMiniapp();
                Passport::setlogout();
                Passport::setLogin($userinfo);
                return json(['code'=>200,'message'=>'注册成功','url' => url('system/passport.Index/index')]);
            }else{
                return json($result);
            }
        }else{
            return view('passport/login/reg');
        }
    }

    /**
     * 忘记密码
     */
    public function getPassword(){
        if(request()->isPost()){
            $data = [
                'captcha'        => $this->request->param('captcha/s'),
                'phone_id'       => $this->request->param('phone_id/s'),
                'login_password' => $this->request->param('login_password/s'),
                'sms_code'       => $this->request->param('sms_code/s'),
            ];
            $validate = $this->validate($data,'Member.getpasspord');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            //判断验证码
            if(!Alisms::isSms($data['phone_id'],$data['sms_code'])){
                return json(['code'=>0,'message'=>"验证码错误"]);
            }
            //验证码通过
            $result  = SystemMember::getPasspord($data);
            if($result){
                Passport::clearMiniapp();
                Passport::setlogout();
                return json(['code'=>200,'message'=>'密码重置成功','url' => url('system/passport.Login/index')]);
            }else{
                return json(['code'=>0,'message'=>'密码重置失败']);
            }
        }else{
            return view('passport/login/getpassword');
        }
    }

    /**
     * 会员退出
     */
    public function logout(){
        Passport::setlogout();
        Passport::clearMiniapp();
        $this->redirect('system/passport.Login/index');
    }

    /**
     * 获取注册验证码
     */
    public function getRegSms(){
        if(request()->isPost()){
            $data = [
                'phone_id'  => $this->request->param('phone/s')
            ];
            $validate = $this->validate($data,'Sms.getsms');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            $user  = SystemMember::where(['phone_id' => $data['phone_id']])->find();
            if(isset($user)) {
                return json(['code'=>0,'message' => "手机已被注册"]);
            }
            $sms = Alisms::putSms($data['phone_id']);
            return json($sms);
        }else{
            return $this->error("404 NOT FOUND");
        }
    }

    /**
     * 获取登录/找回密码等验证码
     */
    public function getLoginSms(){
        if(request()->isPost()){
            $data = [
                'phone_id' => $this->request->param('phone/s')
            ];
            $validate = $this->validate($data,'Sms.getsms');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            //判断是否登录
            $getuser = Passport::getUser();
            if($getuser){
                if($data['phone_id'] != $getuser['phone_id']){
                    return json(['code'=>0,'message'=>"请确认手机号输入正确"]);
                }
            }
            $user  = SystemMember::where(['phone_id' => $data['phone_id']])->find();
            if(empty($user)) {
                return json(['code'=>0,'message'=>"用户不存在"]);
            }
            $sms = Alisms::putSms($data['phone_id']);
            return json($sms);
        }else{
            return $this->error("404 NOT FOUND");
        }
    }
}