<?php
class oauthAction extends frontendAction {
    /**
     * 第三方帐号登陆和绑定
     */
    public function index() {
    	$mod = $this->_get('mod', 'trim');
    	$type = $this->_get('type', 'trim', 'login');
    	!$mod && $this->_404();
        if ('unbind' == $type) {
            !$this->visitor->is_login && $this->redirect('user/login');
            
            M('user_bind')->where(array('uid'=>$this->visitor->info['uid'], 'type'=>$mod))->delete();
            $this->redirect('ucenter/bangding');
        }
        $oauth = new oauth($mod);
        session('callback_type', $type);
        $refer = $this->_request('refer','trim');
        session('refer', $refer);
        
        
        return $oauth->authorize();
    }

    /**
     * 登陆回调页面
     */
    function callback() {
        $mod = $this->_get('mod', 'trim');
        !$mod && $this->_404();
        $callback_type = session('callback_type');
        $oauth = new oauth($mod);
        $rk = $oauth->NeedRequest();
        $request_args = array();
        foreach ($rk as $v) {
            $request_args[$v] = $this->_get($v);
        }
        switch ($callback_type) {
            case 'login':
                $url = $oauth->callbackLogin($request_args);
                break;
            case 'bind':
                $url = $oauth->callbackbind($request_args);
                break;
            default:
                $url = U('index/index');
                break;
        }
        session('callback_type', null);
        redirect($url);
    }
}