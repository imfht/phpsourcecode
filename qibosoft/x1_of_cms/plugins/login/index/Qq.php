<?php
namespace plugins\login\index;
use app\common\controller\IndexBase;
use plugins\login\model\Qq AS UserModel;


class Qq extends IndexBase
{
    
    /**
     * 网页端 QQ登录
     * @param string $fromurl 返回的地址
     * @param string $type 设置为bind的时候,就是绑定帐号
     */
    public function index($fromurl='',$type='',$sid=''){

        if($type=='bind'){
            if(!$this->user){
                $this->error('请用其它帐号登录后,才能绑定QQ登录!');
            }
            $sid || $sid = get_cookie('user_sid');
            if(cache('bind_'.$sid)==''){
                $this->error('验证有误!');
            }
        }elseif($this->user){
            $this->error('你已经登录了',get_url('member'),'',1);
        }
        
        $state = input('state');
        $code = input('code');
        
        if($code){
            $access_token = $this->get_access_token($code);
            if(!$access_token){
                $this->error('获取access_token失败');
            }
            $openid = $this->get_openid($access_token);
            if(!$openid){
                $this->error('获取openid失败');
            }
            
            $this->login_in($openid,$access_token,$type,$fromurl);
            
        }else{
            set_cookie('From_url',$fromurl?:$this->fromurl);
            $url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=' . config('webdb.qqlogin_appid') . '&state=1&redirect_uri=' . urlencode($this->weburl);
            header("Location:$url");
            exit;
        }        
    }
    
    /**
     * app登录
     * @param string $openid
     * @param string $access_token
     * @param string $type
     */
    public function app($openid='',$access_token='',$type='',$fromurl=''){
        if(!$access_token){
            $this->error('获取access_token失败');
        }elseif(!$openid){
            $this->error('获取openid失败');
        }
        $array = $this->get_user_info($access_token,$openid);
        if(!isset($array['nickname'])){
            $this->error_msg('出错了');
        }
        return $this->login_in($openid,$access_token,$type,$fromurl);
    }
    
    protected function login_in($openid='',$access_token='',$type='',$fromurl=''){
        
        $rs = UserModel::get_info(['qq_api'=>$openid]);
        
        if($type=='bind'){  //绑定帐号
            
            if($rs){    //解绑以前的帐号
                $array = [
                    'uid'=>$rs['uid'],
                    'qq_api'=>'',
                ];
                UserModel::edit_user($array);
            }
            
            $array = [
                'uid'=>$this->user['uid'],
                'qq_api'=>$openid,
            ];
            UserModel::edit_user($array);
            cache('user_'.$this->user['uid'],null);
            
        }else{      //注册与登录
            
            $rs && $userdb = UserModel::get_info($rs['uid'],'uid');
            
            //还没有注册，自动注册一个帐号
            if(empty($rs['username']) || empty($userdb)){
                $data = $this->get_user_info($access_token,$openid);
                $userdb = UserModel::api_reg($openid,$data);
                if(!is_array($userdb)){
                    $this->error('注册失败,详情如下：'.$userdb);
                }
            }
            
            UserModel::login($userdb['username'], '', 3600*24*30,true);
        }
        
        $fromurl = $fromurl ? $fromurl : get_cookie('From_url');
        if( $fromurl ){
            set_cookie('From_url','');
            $jumpto = filtrate(urldecode($fromurl));
        }else{
            $jumpto = iurl('index/index/index');
        }
        $this->success($type=='bind'?'绑定成功':'登录成功',$jumpto);
    }
    
    private function error_msg($content){
        $url='http://wiki.opensns.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E5%85%AC%E5%85%B1%E8%BF%94%E5%9B%9E%E7%A0%81%E8%AF%B4%E6%98%8E#100000-100031.EF.BC.9APC.E7.BD.91.E7.AB.99.E6.8E.A5.E5.85.A5.E6.97.B6.E7.9A.84.E5.85.AC.E5.85.B1.E8.BF.94.E5.9B.9E.E7.A0.81';
        $this->error("<a href='$url' target='_blank'>出错了,以下是QQ网站返回的错误信息提示，请点击查看具体的错误原因<br>$content</a>");
    }
    
    /**
     * web方式获取token
     * @param unknown $code
     * @return unknown
     */
    private function get_access_token($code){
        $url = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id='.config('webdb.qqlogin_appid').'&client_secret='.config('webdb.qqlogin_appsecret').'&code='.$code.'&redirect_uri='.urlencode($this->weburl);
        
        $content = http_curl($url);
        //正确时返回access_token=2936BADC028D4410D787A1FC77AC3576&expires_in=7776000&refresh_token=E48365B72DD821D0B696330E6951B86B
        preg_match("/access_token=([\d\w]+)&expires_in=([\d]+)/is",$content,$array);
        $access_token = $array[1];
        if( !preg_match("/^[a-z0-9]+$/i",$access_token) ){
            //callback( {"error":100020,"error_description":"code is reused error"} );
            $this->error_msg('获取access_token失败:'.$content);
        }
        return $access_token;
    }
    
    /**
     * web方式获取用户的openid
     * @param unknown $access_token
     * @return unknown
     */
    private function get_openid($access_token){
        $url = 'https://graph.qq.com/oauth2.0/me?access_token='.$access_token;
        $content = http_curl($url);
        //正确时返回callback( {"client_id":"100204298","openid":"B7FCB3B9337167B0EC04E6A9D3DC3E8E"} );
        preg_match('/"openid"([ ]*):([ ]*)"([\d\w]+)"/is',$content,$array);
        $openid = $array[3];
        if( !preg_match("/^[a-z0-9]+$/i",$openid) ){
            $this->error_msg('获取openid失败:'.$content);
        }
        return $openid;
    }
    
    /**
     * 根据TOKEN获取用户详细资料,WEB形式获取
     * @param unknown $access_token
     * @param unknown $openid
     * @return mixed
     */
    protected function get_user_info($access_token,$openid){
        $str = http_curl('https://graph.qq.com/user/get_user_info?access_token='.$access_token.'&oauth_consumer_key='.config('webdb.qqlogin_appid').'&openid='.$openid);
        $array = json_decode($str,true);
        if(!isset($array['nickname'])){
            $this->error_msg('获取用户资料失败:'.$str);
        }
        return $array;
    }
    
}