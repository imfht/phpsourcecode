<?php
namespace app\api\controller;

use think\Controller;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

// use Flc\Alidayu\Client;
// use Flc\Alidayu\App;
// use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;

class PublicController extends Controller
{
    public function _initialize(){
        if (request()->isOptions()){
            abort(json(true,200));
        }
    }
    // MISS路由
    public function miss()
    {
    	echo '您迷路了哦!';
    }

    //登录
    public function Login()
    {
        $this->debug();//调试模式
        
        $phone = input('param.phone');
        $password = input('param.password');
        $uid = model('User')->login($phone, $password, 3);
        if($uid > 0){
            $data['token'] = $this->get_user_token($uid);
            $data['user'] = model('User')->with('contact,avater')->find($uid)->toArray();

            return json(['data' => $data, 'msg' => '登录成功！', 'code' => 1]);
        }else{
            switch ($uid) {
                case '-1':
                    $info = ['data' => false, 'msg' => '用户不存在或被禁用', 'code' => 0];
                    break;
                case '-2':
                    $info = ['data' => false, 'msg' => '密码错误', 'code' => 0];
                    break;  
                default:
                    $info = ['data' => false, 'msg' => '未知错误', 'code' => 0];
                    break;
            }
            return json($info);
        }
    }
    //用户注册
    public function register(){
        $phone = input('param.phone');
        $username = input('param.username');
        $password = input('param.password');
        
        $uuid = input('param.captcha_uuid');
        $code = input('param.captcha');

        $verify = model('SmsVerify')->where('uuid',$uuid)->find();
        if($verify['phone'] != $phone){
            return json(['data' => false, 'msg' => '手机号不正确', 'code' => 0]);
        }
        if($verify['code'] != $code){
            return json(['data' => false, 'msg' => '验证码不正确', 'code' => 0]);
        }

        $result = model('User')->validate(true)->save([
                'phone' =>  $phone,
                'username' =>  $username,
                'password'  =>  md5($password)
            ]);
        if(false === $result){
            // 验证失败 输出错误信息
            $msg = model('User')->getError();
            return json(['data' => false, 'msg' => $msg, 'code' => 0]);
        }
        model("Analysis")->add(0, 0, 1, 0); //统计
        return json(['data' => false, 'msg' => '注册成功', 'code' => 1]);
    }
    //重置密码
    public function resetPassword(){
        $data = input('param.');

        $verify = model('SmsVerify')->where('uuid',$data['captcha_uuid'])->find();
        if($verify['phone'] != $data['phone']){
            return json(['data' => false, 'msg' => '手机号不正确', 'code' => 0]);
        }
        if($verify['code'] != $data['captcha']){
            return json(['data' => false, 'msg' => '验证码不正确', 'code' => 0]);
        }

        $result = model('User')->where('phone',$data['phone'])->update(['password' => md5($data['password'])]);
        if($result){
            return json(['data' => false, 'msg' => '重置成功', 'code' => 1]);
        }else{
            return json(['data' => false, 'msg' => '重置失败', 'code' => 0]);
        }
    }

    //短信成功返回object(stdClass)#34 (2) {
            //   ["result"] => object(stdClass)#35 (3) {
            //     ["err_code"] => string(1) "0"
            //     ["model"] => string(26) "106059359902^1108216906516"
            //     ["success"] => bool(true)
            //   }
            //   ["request_id"] => string(12) "zqbpj5leacdv"
            // }
    // 错误返回object(stdClass)#34 (5) {
            //   ["code"] => int(15)
            //   ["msg"] => string(20) "Remote service error"
            //   ["sub_code"] => string(26) "isv.BUSINESS_LIMIT_CONTROL"
            //   ["sub_msg"] => string(18) "触发业务流控"
            //   ["request_id"] => string(12) "z28noqlmk1yk"
            // }

    //发送验证码
    public function sendSmsCaptcha(){
        $phone = input('param.phone');
        try {
            $uuid1 = Uuid::uuid4();
            $uuid = $uuid1->toString();
        } catch (UnsatisfiedDependencyException $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
        $sms_config = model('Sms')->find()->toArray();
        $result = sendSmsVerify($sms_config['app_key'],$sms_config['app_secret'],$phone);
        $code = session("smsVerify");
        // $code = mt_rand(100000, 999999);

        // $sms_tpl = model('SmsTpl')->where('type','register')->find()->toArray();
        // $sms_config = model('Sms')->find()->toArray();
        // // 配置信息
        // $config = [
        //     'app_key'    => $sms_config['app_key'],
        //     'app_secret' => $sms_config['app_secret'],
        //     // 'sandbox'    => true,  // 是否为沙箱环境，默认false
        // ];

        // $client = new Client(new App($config));
        // $req    = new AlibabaAliqinFcSmsNumSend;
        // $req->setRecNum($phone)
        //     ->setSmsParam([
        //         'code' => $code
        //     ])
        //     ->setSmsFreeSignName($sms_config['sign'])
        //     ->setSmsTemplateCode($sms_tpl['template_code']);

        // $resp = $client->execute($req);
        // $result = isset($resp->result->success) ? $resp->result->success : false;

        if($result){
            model('SmsVerify')->create([
                'uuid'  =>  $uuid,
                'phone' =>  $phone,
                'code'  =>  $code
            ]);
            $data['uuid'] = $uuid;
            return json(['data' => $data, 'msg' => '发送成功', 'code' => 1]);
        }else{
            $msg = isset($resp->sub_msg) ? $resp->sub_msg : '请输入手机号';
            return json(['data' => false, 'msg' => $msg, 'code' => 0]);
        }
    }

  //插件调试模式
    public function oauthDebug()
    {
        $config = model('app\common\model\Config')->find();

        if($config['debug']){
            session("userId", 1);//插件兼容
        } 
    }
    //插件自动登陆
    public function oauthLogin($redirect)
    {
        $this->oauthDebug();//调试模式
        if (!session("userId")) {
            $weObj = model("app\common\model\WxConfig")->getWeObj();
            $token = $weObj->getOauthAccessToken();
            $wxConfig = model("app\common\model\WxConfig")->find()->toArray();
            if (!$token) {
                $url = $weObj->getOauthRedirect($redirect);
                // $url = 'http://weixin.wemallshop.com/oauth-proxy.html?appid='.$wxConfig["appid"].'&scope=snsapi_userinfo&state=&redirect_uri='.$redirect;
                header("location: $url");
                die();
            }else{
                $userInfo = $weObj->getOauthUserinfo($token["access_token"], $token["openid"]);
    
                $oauth_wx = model('app\common\model\OauthWx')->where('openid',$userInfo["openid"])->find();
                if($oauth_wx){
                    model('app\common\model\User')->where('id',$oauth_wx['user_id'])->update(['last_login_ip' => request()->ip]);
                    
                    session("userId", $oauth_wx['user_id']);
                }else{
                    $avater_id = $this->getavater($userInfo["headimgurl"]);
                    $user = model('app\common\model\User')->create([
                        'avater_id' => $avater_id,
                        'username' => $userInfo['nickname'],
                    ]);
                    $oauth_wx = model('app\common\model\OauthWx')->create([
                        'user_id' => $user->id,
                        'openid' => $token["openid"],
                        'nickname' => $userInfo['nickname'],
                        'sex' => $userInfo['sex'],
                        'city' => $userInfo['city'],
                        'country' => $userInfo['country'],
                        'province' => $userInfo['province'],
                        'language' => $userInfo['language'],
                        'headimgurl' => $userInfo['headimgurl'],
                        'subscribe_time' => date("Y-m-d h:i:s"),
                        'subscribe' => 1,
                    ]);
                   
                    model("app\common\model\Analysis")->add(0, 0, 1, 0); //统计
                    session("userId", $user->id);
                } 
            }
        }
        $user = model('app\common\model\User')->with('contact,avater,wx')->find(session("userId"));
        return $user->toArray();
    }

    //公众号授权登录
    public function oauth(){
        $this->debug();//调试模式

        $redirect = input('param.redirect');

        $weObj = model("WxConfig")->getWeObj();
        $token = $weObj->getOauthAccessToken();
        $wxConfig = model("WxConfig")->find()->toArray();
        if (!$token) {
            $url = $weObj->getOauthRedirect($redirect);
            // $url = 'http://weixin.wemallshop.com/oauth-proxy.html?appid='.$wxConfig["appid"].'&scope=snsapi_userinfo&state=&redirect_uri='.$redirect;
            $data['url'] = $url;
            return json(['data' => $data, 'msg' => '授权url', 'code' => 0]);
        }else{

            $userInfo = $weObj->getOauthUserinfo($token["access_token"], $token["openid"]);

            $oauth_wx = model('OauthWx')->where('openid',$userInfo["openid"])->find();
            if($oauth_wx){
                model('User')->where('id',$oauth_wx['user_id'])->update(['last_login_ip' => request()->ip]);

                $data['token'] = $this->get_user_token($oauth_wx['user_id']);
                $data['user'] = model('User')->with('contact,avater,wx')->find($oauth_wx['user_id']);
                return json(['data' => $data, 'msg' => '登录成功！', 'code' => 1]);
            }else{
                $avater_id = $this->getavater($userInfo["headimgurl"]);
                $user = model('User')->create([
                    'avater_id' => $avater_id,
                    'username' => $userInfo['nickname'],
                ]);
                $oauth_wx = model('OauthWx')->create([
                    'user_id' => $user->id,
                    'openid' => $token["openid"],
                    'nickname' => $userInfo['nickname'],
                    'sex' => $userInfo['sex'],
                    'city' => $userInfo['city'],
                    'country' => $userInfo['country'],
                    'province' => $userInfo['province'],
                    'language' => $userInfo['language'],
                    'headimgurl' => $userInfo['headimgurl'],
                    'subscribe_time' => date("Y-m-d h:i:s"),
                    'subscribe' => 1,
                ]);
               
                model("Analysis")->add(0, 0, 1, 0); //统计

                $data['token'] = $this->get_user_token($user->id);
                $data['user'] = model('User')->with('contact,avater,wx')->find($user->id);
                return json(['data' => $data,'msg' => '登录成功！', 'code' => 1]);
            }
        }
    }
    
    //小程序授权登录
    public function x_oauth(){
        $data = input('param.');

        $userInfo = input('?param.userInfo') ? $data['userInfo'] : '';
        if(!$userInfo){
            return json(['data' => false, 'msg' => '微信未授权，请退出微信试试！', 'code' => 0]);
        }

        $weObj = model("WxConfig")->getWeObj(2);
        $token = $weObj->getxOauthAccessToken($data['code']);

        $oauth_applet = model('OauthApplet')->where('openid',$token["openid"])->find();
        if($oauth_applet){
            model('User')->where('id',$oauth_applet['user_id'])->update(['last_login_ip' => request()->ip]);

            $data2['token'] = $this->get_user_token($oauth_applet['user_id']);
            $data2['user'] = model('User')->with('contact,avater,applet')->find($oauth_applet['user_id']);
            return json(['data' => $data2, 'msg' => '登录成功！', 'code' => 1]);
        }else{
            $avater_id = $this->getavater($userInfo["avatarUrl"]);
            $user = model('User')->create([
                'avater_id' => $avater_id,
                'username' => $userInfo['nickName'],
            ]);
            $oauth_applet = model('OauthApplet')->create([
                'user_id' => $user->id,
                'openid' => $token["openid"],
                'nickname' => $userInfo['nickName'],
                'gender' => $userInfo['gender'],
                'city' => $userInfo['city'],
                'province' => $userInfo['province'],
                'language' => $userInfo['language'],
                'avatarUrl' => $userInfo['avatarUrl'],
            ]);

            model("Analysis")->add(0, 0, 1, 0); //统计

            $data2['token'] = $this->get_user_token($user->id);
            $data2['user'] = model('User')->with('contact,avater,applet')->find($user->id);
            return json(['data' => $data2,'msg' => '登录成功！', 'code' => 1]);
        }
    }
    //获取微信头像
    public function getavater($headimgurl){

        try {
            $uuid1 = Uuid::uuid4();
            $uuid = $uuid1->toString();
        } catch (UnsatisfiedDependencyException $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }

        $savename = $uuid .'.png';
        $savepath = 'avatar/';

        $filename = ROOT_PATH . 'public' . DS . 'uploads/' . $savepath . $savename;
        
        http_down($headimgurl, $filename);

        $file = model('app\common\model\File')->create([
                    'name' => $savename,
                    'ext' => 'png',
                    'type' => 'image/jpeg',
                    'savename' => $savename,
                    'savepath' => $savepath,
                ]);
        return $file->id;
    }

    //获取用户token
    public function get_user_token($user_id){
        $token = new \Gamegos\JWT\Token();
        $token->setClaim('user_id', $user_id); // alternatively you can use $token->setSubject('someone@example.com') method
        $token->setClaim('exp', time() + config('jwt_time'));

        $encoder = new \Gamegos\JWT\Encoder();
        $encoder->encode($token, config('jwt_key'), config('jwt_alg'));
        return $token->getJWT();
    }
    //调试模式
    public function debug(){
        $config = model('Config')->find()->toArray();

        if($config['debug']){
            $data['token'] = $this->get_user_token(1);
            $data['user'] = model('User')->with('contact,avater')->find(1)->toArray();

            abort(json(['data' => $data, 'msg' => '登录成功！', 'code' => 1]));
        }
    }












}