<?php

namespace app\exwechat\controller;

use think\Controller;
use youwen\exwechat\api\accessToken;
use youwen\exwechat\api\OAuth\OAuth;

define('CURL_LOG', true);

class Demoauth extends Controller
{
    public $appid = '';
    public $secret = '';
    
    public function __construct()
    {
        parent::__construct();
        $weconf = new WechatConfig();
        $this->appid = $weconf->appid;
        $this->secret = $weconf->secret;
    }
    public function index()
    {
        return $this->fetch();
    }

    public function snsapi_base()
    {

        $redirect_uri = url('callback_base','', false, true);
        $scope = 'snsapi_base';
        $state = '123';
        $redirect_uri = urlencode($redirect_uri);
        $OAuth = new OAuth($this->appid, $this->secret);
        $url = $OAuth->getCodeUrl($redirect_uri, $scope, $state);
        echo '<pre>';
        print_r( $url );
        exit('</pre>');
        header('Location: '.$url);
        exit();
        // $this->assign('url', $url);
        // $this->assign('redirect_uri', $redirect_uri);
        // return $this->fetch();
    }

    public function snsapi_userinfo()
    {
        $redirect_uri = url('callback_userinfo','', false, true);
        $scope = 'snsapi_userinfo';
        $state = '123';
        $redirect_uri = urlencode($redirect_uri);
        $OAuth = new OAuth($this->appid, $this->secret);
        $url = $OAuth->getCodeUrl($redirect_uri, $scope, $state);
        header('Location: '.$url);
        exit();
        // $this->assign('url', $url);
        // $this->assign('redirect_uri', $redirect_uri);
        // return $this->fetch();
    }



    public function callback_base()
    {
        define(CURL_LOG, true);
        $OAuth = new OAuth($this->appid, $this->secret);
        $ret = $OAuth->getToken($_GET['code']);
        if(isset($ret['errcode'])){
            echo '<pre>';
            print_r( $ret );
            exit('</pre>');
        }
        $this->_saveAccess($ret);
        echo '<pre>';
        print_r( $_GET );
        echo '<br/>';
        print_r( $ret );
        exit('</pre>');
    }

    public function callback_userinfo()
    {
        define(CURL_LOG, true);
        $OAuth = new OAuth($this->appid, $this->secret);
        $ret = $OAuth->getToken($_GET['code']);
        if(isset($ret['errcode'])){
            echo '<pre>';
            print_r( $ret );
            exit('</pre>');
        }
        $info = $OAuth->getUserInfo($ret['access_token'], $ret['openid']);
        if(isset($info['errcode'])){
            echo '<pre>';
            print_r( $info );
            exit('</pre>');
        }
        $check = $OAuth->checkToken($ret['access_token'], $ret['openid']);
        // $refresh = $OAuth->refreshToken($ret['refresh_token']);

        $this->_saveAccess($ret);
        $this->_saveUserInfo($info);

        header("Content-type: text/html; charset=utf-8"); 
        echo '<pre>';
        print_r( $_GET );
        echo '<br/>';
        print_r( $ret );
        echo '<br/>';
        print_r( $info );
        echo '<br/>';
        print_r( $check );
        echo '<br/>';
        print_r( $refresh );
        exit('</pre>');
    }

    /**
     * 存在更新，不存在则插入
     * @DateTime 2017-03-25T12:33:06+0800
     *
     * 这个地方建一个联合索引应该可以用replace
     */
    private function _saveAccess($data)
    {
        $check = db('oauth_access')->where(['openid'=>$data['openid'], 'scope'=>$data['scope']])->find();
        if($check){
            $ret = db('oauth_access')->where(['openid'=>$data['openid'], 'scope'=>$data['scope']])->update($data);
            return $ret;
        }else{
            $ret = db('oauth_access')->insert($data);
            return $ret;
        }
    }

    /**
     * 因为这个有唯一主键openid所以， 可以用replace
     */
    // replace into think_oauth_userinfo set openid=123,nickname='xiaobai',sex=1,language='zh_CN',city='朝阳',province='北京',country='',headimgurl='',privilege=null,unionid='213213';
    private function _saveUserInfo($userinfo)
    {
        // $ret = db('oauth_userinfo')->insert($userinfo);
        $sql = 'REPLACE INTO think_oauth_userinfo SET ';
        $str = '';
        foreach ($userinfo as $key => $value) {
            if(is_array($value)){
                $value = json_encode($value);
            }
            $str .= " `$key`='$value',";
        }
        $str = rtrim($str, ',');
        $ret = db('oauth_userinfo')->execute($sql);
        return $ret;
    }

}
