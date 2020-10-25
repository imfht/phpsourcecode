<?php
class adminController extends controller{
	protected $appID = 'admin';

	public function __construct(){
		if( !isset( $_SESSION )) session_start();
		$appID = config('appID');
		$this->appID = empty($appID) ? $this->appID : $appID;
		$this->checkLogin();
		$this->checkPPinfo();//T-Team公众账户信息检测
		parent::__construct();
	}
		
	protected function checkLogin(){
		//不需要登录验证的页面
		$noLogin = array(
						'index'=>array('login','verify'),
				);
		
		//如果当前访问是无需登录验证，则直接返回		
		if( isset($noLogin[CONTROLLER_NAME]) && in_array(ACTION_NAME, $noLogin[CONTROLLER_NAME]) ){
			return true;
		}
		
		//没有登录,则跳转到登录页面
		if( !$this->isLogin() ){
			$this->redirect( url('admin/index/login') );
		}
		return true;
	}
	
	//判断是否登录
	protected function isLogin(){
		$info_session = get_session('userinfo');
		$this->ppinfo = get_session('ppinfo');
		if( empty( $info_session ) ){
			return false;
		}else{
			$this->userinfo = $info_session;
			return true;
		}
	}
	
	//T-Team公共账户检测
	protected function checkPPinfo(){
		//不需要验证的应用
		$noCheck = array(
						'appname'=>array('admin','appmanage','ppacount','adminmanage'),
				);
		
		$in_ppid = $this->ppinfo['id'];
		
		//如果当前访问是无需验证ppid，则直接返回		
		if( in_array(APP_NAME, $noCheck['appname']) ){
			$this->ppid = $in_ppid;
			return true;
		}
		
		if( !empty( $in_ppid ) ){
			$this->ppid = $in_ppid;
			return true;
		}else{
			$this->alert('还没有选择公众号',url('admin/index/index'),true);
		}
		
		return true;
	}
	
	//设置登录
	protected function setLogin( $userinfo ){
		set_session('userinfo',$userinfo);
	}
	
	//T-Team设置公众账号信息
	protected function setPPinfo( $ppinfo ){
		set_session('ppinfo',$ppinfo);
	}
	
	//退出登录
	protected function clearLogin( $url='' ){
		session_destroy();
		if( !empty($url) ){
			$this->redirect( $url );
		}
		return true;
	}
	
	//T-Team模板admin权限下的显示含公共
    protected function show($tpl = '', $app = '')
    {
        $content = $this->display($tpl, true, true, $app);
        $layout = $this->display('common', true, true, 'admin',true);
        echo str_replace('<!--common-->', $content, $layout);
    }
	
    //T-Team权限检测
    protected function checkPurview()
    {
        if ($this->userinfo['user_id'] == 1 || $this->userinfo['group_id'] == 1) {
            return true;
        }
        $userGroup = api('admin', 'getGroupInfo', $this->userinfo['group_id']);
        $basePurview = unserialize($userGroup['base_purview']);
        $purviewInfo = api(APP_NAME, 'apiAdminPurview');
        if (empty($purviewInfo)) {
            return true;
        }
        $controller = $purviewInfo[CONTROLLER_NAME];
        if (empty($controller['auth'])) {
            return true;
        }
        $action = $controller['auth'][ACTION_NAME];
        if (empty($action)) {
            return true;
        }
        $current = APP_NAME . '_' . CONTROLLER_NAME;
        if (!in_array($current, (array) $basePurview)) {
            $this->msg('您没有权限访问此功能！', 0);
        }
        $current = APP_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
        if (!in_array($current, (array) $basePurview)) {
            $this->msg('您没有权限访问此功能！', 0);
        }
        return true;
    }
}