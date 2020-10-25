<?php
class indexAction extends backendAction {

	public function _initialize() {
		parent::_initialize();
		$this->_mod = D('menu');
	}

	public function index() {
		
		//左侧菜单
		$left_menu = M("menu")->where(array('pid'=>0,'style'=>'left'))->order('ordid')->select();
		foreach ($left_menu as $key=>$val) {
			$left_menu[$key]['sub'] = $this->_mod->admin_menu($val['id']);
		}
	 	
	 	//顶部菜单
		$top_menu = M("menu")->where(array('pid'=>0,'style'=>'top'))->order('ordid')->select(); 
 
		$this->assign('left_menu', $left_menu);
		$this->assign('top_menu', $top_menu);
		$this->display();
	}

	public function panel() {
		$message = array();
		if (is_dir('./install')) {
			$message[] = array(
                'type' => 'error',
                'content' => "您还没有删除 install 文件夹，出于安全的考虑，我们建议您删除 install 文件夹。",
                'divid' => 'dinstall',
			);
		}
		if (APP_DEBUG == true) {
			$message[] = array(
                'type' => 'error',
                'content' => "您网站的 DEBUG 没有关闭，出于安全考虑，我们建议您关闭程序 DEBUG。",
                'divid' => 'dbug',
			);
		}
		if (!function_exists("curl_getinfo")) {
			$message[] = array(
                'type' => 'error',
                'content' => "系统不支持 CURL。",
                'divid' => 'dcurl',
			);
		}
		$this->assign('message', $message);

		$system_info = array(
            'wkcms_version' => C('WKCMS_RELEASE').'_'.C('WKCMS_VERSION').' [<a href="http://www.qizhixiong.com/" class="blue" target="_blank">查看最新版本</a>]',
            'server_domain' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            'server_os' => PHP_OS,
            'web_server' => $_SERVER["SERVER_SOFTWARE"],
            'php_version' => PHP_VERSION,
            'mysql_version' => mysql_get_server_info(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time') . '秒',
            'safe_mode' => (boolean) ini_get('safe_mode') ?  L('yes') : L('no'),
            'zlib' => function_exists('gzclose') ?  L('yes') : L('no'),
            'curl' => function_exists("curl_getinfo") ? L('yes') : L('no'),
            'timezone' => function_exists("date_default_timezone_get") ? date_default_timezone_get() : L('no')
		);
		$this->assign('system_info', $system_info);
		$this->display();
	}

	public function login() {
		if (IS_POST) {
			$username = trim($_POST['username']);
			$password =trim($_POST['password']);
			$verify_code = $_POST['verify_code'];
			$md5=C('WEB_MD5');

			if(session('verify') != md5($verify_code)){
				$this->error(L('verify_code_error'));
			}
			$roleid=M('user_role')->where(array('isAdmin'=>'1', 'status'=>'1'))->getField('id',true);
			$map['roleid']=array('in',$roleid);
			$map['username']=$username;
			$map['status']=1;
			$admin = M('user')->where($map)->find();
			if (!$admin) {
				$this->error(L('admin_not_exist'));
			}
			if ($admin['password'] != md5($password.$md5)) {
				$this->error(L('password_error'));
			}
			session('admin', array(
                'id' => $admin['uid'],
                'role_id' => $admin['roleid'],
                'username' => $admin['username'],
			));

			$this->success(L('login_success'), U('index/index'));
		} else {
			$this->display();
		}
	}

	public function logout() {
		session('admin', null);
		$this->success(L('logout_success'), U('index/login'));
		exit;
	}

	public function verify_code() {
		Image::buildImageVerify(4,1,'gif','50','24');
	}

	public function left() {
		$this->display();
	}

	public function often_menu() {
		if (isset($_POST['do'])) {
			$id_arr = isset($_POST['id']) && is_array($_POST['id']) ? $_POST['id'] : '';
			$this->_mod->where(array('ofen'=>1))->save(array('often'=>0));
			$id_str = implode(',', $id_arr);
			$this->_mod->where('id IN('.$id_str.')')->save(array('often'=>1));
			$this->success(L('operation_success'));
		} else {
			$r = $this->_mod->admin_menu(0);
			$list = array();
			foreach ($r as $v) {
				$v['sub'] = $this->_mod->admin_menu($v['id']);
				foreach ($v['sub'] as $key=>$sv) {
					$v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
				}
				$list[] = $v;
			}
 			$this->assign('list', $list);
			$this->display();
		}
	}

	public function map() {
		$r = $this->_mod->admin_menu(0);
		$list = array();
		foreach ($r as $v) {
			$v['sub'] = $this->_mod->admin_menu($v['id']);
			foreach ($v['sub'] as $key=>$sv) {
				$v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
			}
			$list[] = $v;
		}
		$this->assign('list', $list);
		$this->display();
	}
}