<?php
namespace app\index\controller\wxapp;

use plugins\login\model\Wxapp AS UserModel;
use app\common\controller\IndexBase;
use QCloud_WeApp_SDK\Auth\LoginService;
use QCloud_WeApp_SDK\Constants as Constants;
use QCloud_WeApp_SDK\Auth\AuthAPI;


//小程序 用户登录与退出
class Login extends IndexBase
{
    /**
     * webapp退出
     */
    public function getout(){
        UserModel::quit($this->user['uid']);
        return $this->ok_js([],'退出成功');
    }
    
    /**
     * webapp登录
     */
    public function goin(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if(empty($data['cookietime'])){
                $data['cookietime'] = $this->webdb['login_time']?:3600*24*30;
            }
            $result = UserModel::login($data['username'],$data['password'],$data['cookietime']);
            if($result==0){
                return $this->err_js("当前用户不存在,请重新输入");
            }elseif($result==-1){
                return $this->err_js("密码不正确,点击重新输入");
            }elseif(is_array($result)){
                $user = $result;
                $token = md5( $user['uid'] . $user['password']  . time() );
                cache($token,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",1800);
                $array = [
                    'uid'=>$result['uid'],
                    'token'=>$token,
                ];
                return $this->ok_js($array,'登录成功');
            }else{
                return $this->err_js('未知错误!');
            }
        }else{
            return $this->err_js('提交方式有误!');
        }
    }
    
    /**
     * 网页端AJAX检查登录状态
     */
    public function web_login_check(){
        if ($this->user) {
            return $this->ok_js(['uid'=>$this->user['uid']],'已登录');
        }else{
            return $this->err_js('未登录');
        }
    }
    
    /**
     * 微信开放平台移动应用APP登录
     * @param string $code
     */
    public function wxopen($code=''){
        if (empty($code)) {
            return $this->err_js("code值不存在");
        }
        $string = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->webdb['wxopen_appid'].'&secret='.$this->webdb['wxopen_appkey'].'&code='.$code.'&grant_type=authorization_code');
        $array = json_decode($string,true);
        if(empty($array['unionid'])){
            return $this->err_js("unionid获取失败");
        }elseif(empty($array['openid'])){
            return $this->err_js("openid获取失败");
        }
        
        $result = UserModel::where('unionid',$array['unionid'])->find();
        if(empty($result)){ //新用户,自动注册帐号
            $string2 = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$array['access_token'].'&openid='.$array['openid'].'&lang=zh_CN');
            $data = json_decode($string2,true);
            if(empty($data['openid'])){
                return $this->err_js("用户资料获取失败");
            }
            $data['nickName'] = $data['nickname'];
            $data['avatarUrl'] = $data['headimgurl'];
            $user = UserModel::api_reg($array['openid'],$data);
            if(is_array($user) && $user['uid']>0){
                UserModel::edit_user([
                    'uid'=>$user['uid'],
                    'wxapp_api'=>'',
                    'wxopen_api'=>$array['openid'],
                    'sex'=>intval($data['sex']),
                ]);                
            }else{
                return $this->err_js('注册失败:'.$user);
            }
        }elseif(empty($result['wxopen_api'])){
            UserModel::edit_user([
                'uid'=>$result['uid'],
                'wxopen_api'=>$array['openid'],
            ]);            
        }
        $result = UserModel::login($array['unionid'],'',3600*24,true,'unionid');
        $user = $result;
        $token = md5( $user['uid'] . $user['password']  . time() );
        cache($token,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",1800);
        $array = [
            'uid'=>$result['uid'],
            'token'=>$token,
        ];
        return $this->ok_js($array,'登录成功');
    }
    
    /**
     * 小程序用户登录
     * @param string $code 微信端提交过来的
     * @param string $encryptedData 微信端提交过来的
     * @param string $iv 微信端提交过来的
     * @param string $iv 微信端提交过来的之前的WEB框架登录标志
     * @return string
     */
    public function index($code='',$encryptedData='',$iv='',$uids=''){
        if($code=='the code is a mock one'||empty($code)){
            return $this->err_js('无法登录,code 获取失败');
        }
        $array = AuthAPI::login($code, $encryptedData, $iv);
        if(!is_array($array)){
            return $this->err_js($array);
        }
        $skey = $array['skey'];
        $sessionKey = $array['sessionKey'];
        $info = $array['userinfo'];
        $openid = $info['openId'];
        
        if (empty($openid)) {
            return $this->err_js('登录失败,openid获取不到');
        }
        
        $user = UserModel::check_wxappIdExists($openid);  //根据小程序ID获取用户信息,优先级最高
 
        if ( $user && $info['unionId'] && empty($user['unionid']) ) {   //后来开通了认证微信开放平台的老用户处理
            UserModel::edit_user([
                'uid'=>$user['uid'],
                'unionid'=>$info['unionId'],
            ]);
        }
        
        if (empty($user) && $info['unionId']) {     //绑定了微信认证开放平台
            $user = UserModel::get(['unionid'=>$info['unionId']]);

            if ( $user && empty($user['wxapp_api']) ) { //开通了微信认证开放平台正常情况新用户都是这种
                UserModel::edit_user([
                    'uid'=>$user['uid'],
                    'wxapp_api'=>$openid,
                ]);
            }
        }
        
        //write_file(ROOT_PATH.'WXAPP.txt', var_export($info,true).'---'.$user['uid'].'---'.$info['unionId']);
        
        if(empty($user) && $uids){  //有传递WEB框架用户已登录的标志过来 , 这个是针对没有绑定认证开放平台处理的,已认证的话,用不到这里
            list($uid,$time) = explode(',',mymd5($uids,'DE'));
            if (time()-$time<600) {
                $user = UserModel::getById($uid);
                if (empty($user['wxapp_api'])) {
                    UserModel::edit_user([
                            'uid'=>$uid,
                            'wxapp_api'=>$openid,
                    ]);
                }
            }
        }
        if(empty($user)){
            if($info['unionId']){
                $info['unionid']=$info['unionId'];  //注意I是大写
            }
            $user = UserModel::api_reg($openid,$info);
            if(!is_array($user)||$user['uid']<1){
                return $this->err_js('注册失败:'.$user);
            }
        }
        
        UserModel::login($user['username'], '', '',true);   //这个并不能真正的登录.只是做一些登录的操作日志及其它接口处理
        
        $user = UserModel::get_info($user['uid']);  //这句可以删除,主要是考虑到以前password没有统一在一个数据表的情况
        cache($skey,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t$sessionKey",3600*72);
        $array = [
                'token'=>$skey,
                'userInfo'=>UserModel::get_info($user['uid']),
        ];
        return $this->ok_js($array);
    }
    
    /**
     * 小程序或APP客户端检查登录状态
     * @param string $token
     * @return \think\response\Json
     */
    public function check($token=''){    
        list($uid,$username) = explode("\t", cache($token));
        $code = 1;
        $userInfo = [];
        $msg = '调用失败';
        if($uid&&$username){
            $code = 0;
            $userInfo = UserModel::get_info($uid);
            unset($userInfo['password_rand'],$userInfo['qq_api'],$userInfo['weixin_api'],$userInfo['wxapp_api']);
            $msg = '调用成功';
        }
        $data = [
                'meta'=>[
                        'code'=>$code,
                        'message'=>$msg,
                ],
                'data'=>[
                        'userInfo'=>$userInfo,
                ],
        ];
        return json($data);
    }    
    
    /**
     * 小程序或APP退出登录 ,还需进一步完善
     * @param string $code
     * @return string
     */
    public function quit($code=''){
        $string = '{"meta":{"code":0,"message":"登出成功"},"data":null}';
        return $string;
    }
}
