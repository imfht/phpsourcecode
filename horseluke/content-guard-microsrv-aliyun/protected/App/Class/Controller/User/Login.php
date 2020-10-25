<?php

namespace Controller\User;

use SCH60\Kernel\App;
use SCH60\Kernel\BaseController;
use SCH60\Kernel\StrHelper;

class Login extends BaseController{
    
    protected $layout = "layout_default";
    
    public function actionIndex(){
        $viewData = array('title' => '登录', 'parentTitle' => '用户登录');
        return $this->render(null, $viewData);
    }
    
    public function actionLogout(){
        App::$app->response->setCookie('sessionauth', "", -1);
        $this->request->session_get();
        $_SESSION = array();
        $this->response->msg('退出成功！', StrHelper::url('user/login/index'));
    }
    
    
    public function actionBytaobaooauth(){
        
        $client = \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('TaobaoOAuthClient');
        
        $param = array();
        $param['state'] = md5("asdf567ua". mt_rand(). __FILE__);
        $this->response->session_set('state_for_verify', $param['state']);
        /*
         * 如果你确定redirect_uri一直保持不变，
         * 且在构建TaobaoOAuthClient时候指定了redirect_uri，那么就不需要传递redirect_uri；
         * 否则，getAccessToken()需要每次都传入redirect_uri
         *
         */
        $param['redirect_uri'] = StrHelper::url("user/login/bytaobaooauthcallback");
        
        $finalUrl = $client->getAuthUrl($param);
        
        $this->response->redirect($finalUrl);
        
    }
    
    public function actionBytaobaooauthcallback(){
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        $state = isset($_GET['state']) ? $_GET['state'] : '';
        
        if(empty($code)){
            $this->response->error("code不存在", 403, StrHelper::url('user/login/index'));
        }
        
        $session_state_for_verify = $this->request->session_get('state_for_verify');
        $this->response->session_set('state_for_verify', "");  //请注意这里！务必unset！
        
        if(empty($session_state_for_verify) || $state !== $session_state_for_verify){
            $this->response->error("state不正确，请重新登录。", 403, StrHelper::url('user/login/index'));
        }
        
        $client = \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('TaobaoOAuthClient');
        
        $param = array();
        $param['code'] = $code;
        $param['state'] = $state;
        /*
         * 如果你确定redirect_uri一直保持不变，
         * 且在构建TaobaoOAuthClient时候指定了redirect_uri，那么就不需要传递redirect_uri；
         * 否则，getAccessToken()需要每次都传入redirect_uri
         *
         */
        $param['redirect_uri'] = StrHelper::url("user/login/bytaobaooauthcallback");
        
        $token = $client->getAccessToken($param);
        
        if(isset($token['error'])){
            $this->response->error("getAccessToken出错：".  $token['error']. " / ". $token['error_description'], 403, StrHelper::url('user/login/index'));
        }

        $this->response->session_set("taobao_access_token_data", $token);
        $this->response->session_set("taobao_access_token", $token['access_token']);
        
        //本demo系统特别使用
        $this->response->session_set('username', $token['taobao_user_nick']);
        $this->response->session_set("isLoginFinal", true);
        $this->response->session_set('isLoginFinal_until', $token['expires_in'] + time());
        
        $this->response->msg("通过淘宝帐号登录成功！", StrHelper::url('index/index/index'));
    }
    
}