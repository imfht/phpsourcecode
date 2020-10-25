<?php
/**
 * @copyright   Copyright (c) 2018 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * API默认继承类
 */
namespace app\common\controller;
use app\common\model\SystemUser;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberWechatTpl;
use app\common\facade\WechatProgram;
use app\common\event\User;
use think\facade\Request;
use filter\Filter;
use sign\Sign;

class Api extends Base {

    protected $miniapp;             //应用信息   
    protected $miniapp_id;          //应用信息ID
    protected $member_miniapp;      //应用信息(兼容处理)
    protected $member_miniapp_id;   //应用信息ID(兼容处理)
    protected $token;               //验证用户
    protected $user;                //登录后用户
    
    /**
     * 初始化类
     */
    protected function initialize(){ 
        parent::initialize();
        if(!Request::param('sapixx/d',0)){
            exit(json_encode(['code'=>403,'msg'=>'禁止非法访问']));
        }
        $this->miniapp  = $this->apiAccess();
        if(!$this->miniapp){
            exit(json_encode(['code'=>403,'msg'=>'应用停止服务']));
        }
        $this->member_miniapp    = $this->miniapp;     //兼容处理
        $this->member_miniapp_id = $this->miniapp->id; //兼容处理
        $this->miniapp_id        = $this->miniapp->id;
        $this->user              = self::getUser();
        $this->isAppTyes($this->miniapp->miniapp->types); //判断应用类型
    }

    /**
     * 方法不存在
     */
    public function _empty(){
        return enjson(403,'未找到API地址');
    }

    /**
     * 读取小程序配置
     * @return void
     */
    public function config(){
        $wxconfig = [
            'app_name'     => $this->miniapp->appname,
            'app_id'       => $this->miniapp->id,
            'navbar_color' => $this->miniapp->navbar_color ? $this->miniapp->navbar_color : '#ffffff',
            'navbar_style' => $this->miniapp->navbar_style ? $this->miniapp->navbar_style : '#000000',
        ];
        $tpl =  SystemMemberWechatTpl::getConfig($this->miniapp_id);
        if($tpl){
            $wxconfig['tplmsg'][] = $tpl->tplmsg_common_app;
        }
        return enjson(200,$wxconfig);
    }

    /**
     * 获取用户数据
     * @return void
     */
    public function getUserInfo(){
        $userinfo = self::getUser();
        if(!$userinfo){
            return json(['code'=>401,'msg'=>'用户认证失败']);
        }
        $data['invite_code'] = $this->user->invite_code;
        $data['phone_uid']   = empty($this->user->phone_uid) ? '' : en_phone($this->user->phone_uid);
        $data['telphone']    = $this->user->phone_uid;
        $data['invite_code'] = $this->user->invite_code;
        $data['face']        = $this->user->face;
        $data['nickname']    = $this->user->nickname;
        $data['login_time']  = date('Y-m-d',$this->user->login_time);
        return enjson(200,$data);
    }

    /**
     *  微信小程序统一登录接口
     */
    public function miniappLogin($type = 'json'){
        if(request()->isPost()){
            $data = [
                'code'           => Request::param('code/s'),
                'user_info'      => Request::param('user_info/s'),
                'encrypted_data' => Request::param('encrypted_data/s'),
                'iv'             => Request::param('iv/s'),
                'signature'      => Request::param('signature/s'),
                'official_uid'   => Request::param('official_uid/s',''),
                'invite_code'    => Request::param('invite_code/s',''),
            ];
            $validate = $this->validate($data,'Miniapp.login');
            if(true !== $validate){
                return enjson(403,$validate,[],$type);
            }
            $userInfo = json_decode(htmlspecialchars_decode($data['user_info']),true);
            if(empty($userInfo)){
                return enjson(403,'用户登录失败',[],$type);
            }
            //判断是否开放平台应用(0是开发平台 1是独立应用)
            $rel = WechatProgram::isTypes($this->miniapp_id);
            if(!$rel){
                return enjson(403,'管理员未授权应用接入',[],$type);
            }
            $miniapp = $rel->auth->session($data['code']);
            if(!empty($miniapp['errcode'])){
                return enjson(403,'Token无效,请联系管理员',[],$type);
            }
            $nickName = Filter::filter_Emoji($userInfo['nickName']);
            //获取(注册/登录)数据
            $regdata['miniapp_uid']  = $miniapp['openid'];
            $regdata['session_key']  = $miniapp['session_key'];
            $regdata['official_uid'] = $data['official_uid']; //绑定公众号的OPENID时候用的
            $regdata['wechat_uid']   = empty($miniapp['unionid']) ? '' : $miniapp['unionid'];
            $regdata['nickname']     = $nickName ?? '微信-'.time();
            $regdata['avatar']       = $userInfo['avatarUrl'];
            $regdata['miniapp_id']   = $this->miniapp_id;
            $regdata['invite_code']  = $data['invite_code'];  //邀请码
            //判断是登录还是注册
            $uid = SystemUser::wechatReg($regdata);
            if(!$uid){
                return enjson(403,'用户认证失败',[],$type);
            }
            //保持注册记录
            User::setLogin(['id'=> $uid,'nickname' => $nickName]);
            //返回信息
            $return_data['token']      = WechatProgram::createToken(['miniapp_id' => $this->miniapp_id,'uid' => $uid,'miniapp_uid' => $miniapp['openid'],'service_id' => $this->miniapp->service_id]);
            $return_data['uid']        = $uid;
            $return_data['ucode']      = create_code($uid);
            $return_data['session_id'] = session_id();
            return enjson(200,'登录成功',$return_data,$type);
        }else{
            return $this->error("404 NOT FOUND");
        }
    } 
    
    /**
     * 生成小程序码
     * @param [array] $scene  格式的参数
     * @param [string] $page  小程序路径
     * @return void
     */
    public function MiniProgramCode(array $scene, $page, $name){
        if(request()->isPost()){
            $filepath = PATH_RES.'qrcode/';
            $response = WechatProgram::isTypes($this->miniapp_id)->app_code->getUnlimit(http_build_query($scene),['page' => Filter::filter_escape($page)]);
            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $filename = $response->saveAs($filepath,md5($name));
                $path = '/' . str_replace('\\', '/', substr($filepath.$filename,strlen(PATH_PUBLIC)));
                return json(['code' => 200, 'msg' => '成功', 'data' => Request::root(true).$path]);
            }
            return enjson(404,'您的应用未上线');
        }
    } 
  
    /**
     * 生成小程序码二维码
     * @param [array] $scene  格式的参数
     * @param [string] $page  小程序路径
     * @return void
     */
    public function MiniProgramQrCode(array $scene, $page, $name){
        $filepath = PATH_RES.'qrcode/';
        $response = WechatProgram::isTypes($this->miniapp_id)->app_code->getQrCode($page.'?'.http_build_query($scene));
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs($filepath,md5($name));
            $path = '/' . str_replace('\\', '/', substr($filepath.$filename, strlen(PATH_PUBLIC)));
            return json(['code' => 200, 'msg' => '成功', 'data' => Request::root(true).$path]);
        }
        return enjson(404,'您的应用未上线');
    } 

    /**
     * 禁止用户登录
     */
    protected function isUserAuth($code = 401){
        if(!$this->user){
            exit(json_encode(['code' => $code,'msg'=>'用户认证失败']));
        }
    }

    /**
     * 接口验证
     * @param mixed   $var 签名验证的参数
     * @return array
     */
    protected function apiSign($var = [],$signType = 'md5'){
        $sign      = Request::param('sign/s');      //获取签名
        $publickey = Request::param('publickey/s'); //获取公钥
        if(empty($sign)){
            $code = 401;
            $msg  = '没有验证签名';
        }else{
            $secret = $this->miniapp->service_id;
            if(empty($publickey) && empty($secret)){
                $code  = 403;
                $msg = '签名秘钥或公钥错误';
            }else{
                $var['sign']      = $sign;
                $var['publickey'] = $publickey;
                $sign = Sign::makeSign($var,$secret,$signType);
                if($var['sign'] == $sign){
                    $code = 200;
                    $msg = '成功';
                }else{
                    $code = 403;
                    $msg = '参数验证失败';
                }
            }
        }
        if($code != 200){
            exit(json_encode(['code' => $code,'msg' => $msg]));
        }
        return ['code' => $code,'msg' => $msg];
    }

    /**
     * 接口验证
     * @param mixed   $var 签名验证的参数
     * @return array
     */
    protected function makeSign($var = [],$secret = null,$signType = 'md5'){
        if(empty($secret)){
            $secret = $this->miniapp->service_id;
        }
        $var['sign'] = Sign::makeSign($var,$secret,$signType);
        return $var;
    }

    /**
     * 如果增加双向验证请在这里增加
     * 增加服务器登录安全认证
     * @return void
     */
    protected function getUser(){
        $rel = WechatProgram::checkToken(['service_id' => $this->miniapp->service_id,'token'=>$this->token]);
        if($rel){
            return SystemUser::where(['id' => $rel['uid'],'is_lock' => 0])->find();
        }
        return;
    }
}