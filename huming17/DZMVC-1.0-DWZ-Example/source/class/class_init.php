<?php

if(!defined('IN_SITE')) {
	exit('Access Denied');
}

class site_init extends core_base{


	var $mem = null;

	var $session = null;

	var $config = array();

	var $var = array();

	var $cachelist = array();

	var $init_db = true;
	var $init_setting = true;
	var $init_user = true;
	var $init_session = true;
	var $init_cron = true;
	var $init_misc = true;
	var $init_mobile = true;

	var $initated = false;

        var $init_position = true;

	var $superglobal = array(
		'GLOBALS' => 1,
		'_GET' => 1,
		'_POST' => 1,
		'_REQUEST' => 1,
		'_COOKIE' => 1,
		'_SERVER' => 1,
		'_ENV' => 1,
		'_FILES' => 1,
	);

	static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new self();
		}
		return $object;
	}

	public function __construct() {
		$this->_init_env();
		$this->_init_config();
		$this->_init_input();
		$this->_init_output();
	}

	public function init() {
		if(!$this->initated) {
			$this->_init_db();
			$this->_init_setting();
			$this->_init_user();
			//$this->_init_session();
			//$this->_init_mobile();
			//$this->_init_cron();
			//$this->_init_misc();
                        $this->_init_position();
		}
		$this->initated = true;
	}

	private function _init_env() {

		error_reporting(E_ERROR);
		if(PHP_VERSION < '5.3.0') {
			set_magic_quotes_runtime(0);
		}

		define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
		define('ICONV_ENABLE', function_exists('iconv'));
		define('MB_ENABLE', function_exists('mb_convert_encoding'));
		define('EXT_OBGZIP', function_exists('ob_gzhandler'));

		define('TIMESTAMP', time());
		$this->timezone_set();

		if(!defined('DZF_CORE_FUNCTION') && !@include(DZF_ROOT.'./source/function/function_core.php')) {
			exit('function_core.php is missing');
		}
                
		if(!@include(SITE_ROOT.'./source/system_version.php')) {
			exit('system_version.php is missing');
		}

		if(function_exists('ini_get')) {
			$memorylimit = @ini_get('memory_limit');
			if($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
				ini_set('memory_limit', '128m');
			}
		}

		define('IS_ROBOT', checkrobot());

		foreach ($GLOBALS as $key => $value) {
			if (!isset($this->superglobal[$key])) {
				$GLOBALS[$key] = null; unset($GLOBALS[$key]);
			}
		}

		global $_G;
		$_G = array(
			'user_id' => 0,
			'user_name' => '',
			'user_realname' => '',
                        'role_name' => '',
			'user_group_id' => '',
			'user_role_id' => 1,
			'user_level_id' => '',
			'user_detail_id' => '',
                        'd_id'=>'', //用户所属区域
                        'd_name'=> '', //用户所属区域名称
			'user_score' => 0,
			'sid' => '',
			'formhash' => '',
			'connectguest' => 0,
			'timestamp' => TIMESTAMP,
			'starttime' => microtime(true),
			'clientip' => $this->_get_client_ip(),
			'referer' => '',
			'charset' => '',
			'gzipcompress' => '',
			'authkey' => '',
			'timenow' => array(),
			'widthauto' => 0,
			'disabledwidthauto' => 0,

			'PHP_SELF' => '',
			'siteurl' => '',
			'siteroot' => '',
			'siteport' => '',

			'config' => array(),
			'setting' => array(),
			'member' => array(),
			'cookie' => array(),
			'style' => array(),
			'cache' => array(),
			'session' => array(),
			'lang' => array(),
			'my_app' => array(),
			'my_userapp' => array(),
			'rssauth' => '',
			'home' => array(),
			'space' => array(),
			'block' => array(),
			'article' => array(),
			'action' => array(
				'action' => APPTYPEID
			),
			'mobile' => ''
		);

		$_G['PHP_SELF'] = dhtmlspecialchars($this->_get_script_url());
		$_G['basescript'] = CURSCRIPT;
		$_G['basefilename'] = basename($_G['PHP_SELF']);
		$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
		if(defined('IN_API')) {
			$sitepath = preg_replace("/\/api\/?.*?$/i", '', $sitepath);
		} elseif(defined('IN_ARCHIVER')) {
			$sitepath = preg_replace("/\/archiver/i", '', $sitepath);
		}
		$_G['siteurl'] = dhtmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');

		$url = parse_url($_G['siteurl']);
		$_G['siteroot'] = isset($url['path']) ? $url['path'] : '';
		$_G['siteport'] = empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];

		if(defined('SUB_DIR')) {
			$_G['siteurl'] = str_replace(SUB_DIR, '/', $_G['siteurl']);
			$_G['siteroot'] = str_replace(SUB_DIR, '/', $_G['siteroot']);
		}

		$this->var = & $_G;

	}

	private function _get_script_url() {
		if(!isset($this->var['PHP_SELF'])){
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if(basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
			} else if(basename($_SERVER['PHP_SELF']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['PHP_SELF'];
			} else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['ORIG_SCRIPT_NAME'];
			} else if(($pos = strpos($_SERVER['PHP_SELF'],'/'.$scriptName)) !== false) {
				$this->var['PHP_SELF'] = substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			} else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT']) === 0) {
				$this->var['PHP_SELF'] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
				$this->var['PHP_SELF'][0] != '/' && $this->var['PHP_SELF'] = '/'.$this->var['PHP_SELF'];
			} else {
				system_error('request_tainting');
			}
		}
		return $this->var['PHP_SELF'];
	}

	private function _init_input() {
		if (isset($_GET['GLOBALS']) ||isset($_POST['GLOBALS']) ||  isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
			system_error('request_tainting');
		}

		if(MAGIC_QUOTES_GPC) {
			$_GET = dstripslashes($_GET);
			$_POST = dstripslashes($_POST);
			$_COOKIE = dstripslashes($_COOKIE);
		}

		$prelength = strlen($this->config['cookie']['cookiepre']);
		foreach($_COOKIE as $key => $val) {
			if(substr($key, 0, $prelength) == $this->config['cookie']['cookiepre']) {
				$this->var['cookie'][substr($key, $prelength)] = $val;
			}
		}


		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
			$_GET = array_merge($_GET, $_POST);
		}

		if(isset($_GET['page'])) {
			$_GET['page'] = rawurlencode($_GET['page']);
		}

		if(!(!empty($_GET['handlekey']) && preg_match('/^\w+$/', $_GET['handlekey']))) {
			unset($_GET['handlekey']);
		}

		if(!empty($this->var['config']['input']['compatible'])) {
			foreach($_GET as $k => $v) {
				$this->var['gp_'.$k] = daddslashes($v);
			}
		}

		$this->var['mod'] = empty($_GET['mod']) ? '' : dhtmlspecialchars($_GET['mod']);
		$this->var['action'] = empty($_GET['action']) ? '' : dhtmlspecialchars($_GET['action']);
		$this->var['do'] = empty($_GET['do']) ? '' : dhtmlspecialchars($_GET['do']);
		$this->var['inajax'] = empty($_GET['inajax']) ? 0 : (empty($this->var['config']['output']['ajaxvalidate']) ? 1 : ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || $_SERVER['REQUEST_METHOD'] == 'POST' ? 1 : 0));
		$this->var['page'] = empty($_GET['page']) ? 1 : max(1, intval($_GET['page']));
		$this->var['sid'] = $this->var['cookie']['sid'] = isset($this->var['cookie']['sid']) ? dhtmlspecialchars($this->var['cookie']['sid']) : '';

		if(empty($this->var['cookie']['saltkey'])) {
			$this->var['cookie']['saltkey'] = random(8);
			dsetcookie('saltkey', $this->var['cookie']['saltkey'], 86400 * 30, 1, 1);
		}
		$this->var['authkey'] = md5($this->var['config']['security']['authkey'].$this->var['cookie']['saltkey']);

		//DEBUG Multi-Lingual Support start
		//set default
		$default_lang = strtolower($this->var['config']['output']['language']);
		$lng = '';
		if($this->var['config']['enable_multilingual']) {
			// Adjust language names with language titles
			foreach($this->var['config']['languages'] AS $k=>$v) {
				if(empty($v['name'])) {
					$this->var['config']['languages'][$k]['name'] = $v['title'];
				}
			}
			// set language from cookies
			if($this->var['cookie']['language']) {
				$lng = strtolower($this->var['cookie']['language']);
			}
			// check if the language from GET is valid
			if(isset($this->var['gp_language'])){
				$tmp = strtolower($this->var['gp_language']);
				if(isset($this->var['config']['languages'][$tmp])) {
					// set from GET
					$lng = $tmp;
				}
				// set new language to cookie
				dsetcookie('language', $lng);
				$url = $_SERVER['REQUEST_URI'];
				$url = preg_replace("~[\?\&]language\=\w*~i",'',$url);
				dheader('Location: '.$url);
				exit;
			}
			//Check for language auto-detection
			if(!$lng) {
				$detect = (boolean) $this->var['config']['detect_language'];
				if($detect) {
					$lng = detect_language($this->var['config']['languages'],$default_lang);
				}
			}
		}
		// Set language to default if no language detected
		if(!$lng) {
			$lng = $default_lang;
		}
		//DEBUG
		$this->var['oldlanguage'] = $lng; // Store Old Language Value for compare
		// define site languge const
        define('SITE_LANG', $lng);
		// set new language to cookie
		dsetcookie('language', $lng);
		// set new language variables
		$this->var['language']  = $lng;
		$this->var['langpath']  = DZF_ROOT . 'source/language/'.$lng . '/';
		$this->var['langurl']   = $this->var['siteroot'] . 'source/language/'.$lng . '/';
		$this->var['langicon']  = $this->var['config']['languages'][$lng]['icon'];
		$this->var['langname'] = $this->var['config']['languages'][$lng]['name'];
		$this->var['langtitle'] = $this->var['config']['languages'][$lng]['title'];
		$this->var['langdir']   = strtolower($this->var['config']['languages'][$lng]['dir']);
		// define LANGUAGE RTL Suffix
		define('RTLSUFFIX', $this->var['langdir'] == 'rtl' ? '_rtl' : '');
		// set jspath (for include *.js) TODO
		//$this->var['setting']['jspath'] = $this->var['siteroot'] . 'static/js/';
		//DEBUG Multi-Lingual Support STOP
	}

	private function _init_config() {

		$_config = array();
		@include SITE_ROOT.'./config/config_global.php';
    	@include SITE_ROOT.'./source/system_locale.php';
		if(empty($_config)) {
			if(!file_exists(DZF_ROOT.'./data/install.lock')) {
				header('location: install');
				exit;
			} else {
				system_error('config_notfound');
			}
		}

		if(empty($_config['security']['authkey'])) {
			$_config['security']['authkey'] = md5($_config['cookie']['cookiepre'].$_config['db'][1]['dbname']);
		}

		if(empty($_config['debug']) || !file_exists(libfile('function/debug'))) {
			define('DZF_DEBUG', false);
			error_reporting(0);
		} elseif($_config['debug'] === 1 || $_config['debug'] === 2 || !empty($_REQUEST['debug']) && $_REQUEST['debug'] === $_config['debug']) {
			define('DZF_DEBUG', true);
			error_reporting(E_ERROR);
			if($_config['debug'] === 2) {
				error_reporting(E_ALL);
			}
		} else {
			define('DZF_DEBUG', false);
			error_reporting(0);
		}

		define('STATICURL', !empty($_config['output']['staticurl']) ? $_config['output']['staticurl'] : 'static/');
		$this->var['staticurl'] = STATICURL;

		$this->config = & $_config;
		$this->var['config'] = & $_config;

		if(substr($_config['cookie']['cookiepath'], 0, 1) != '/') {
			$this->var['config']['cookie']['cookiepath'] = '/'.$this->var['config']['cookie']['cookiepath'];
		}
		$this->var['config']['cookie']['cookiepre'] = $this->var['config']['cookie']['cookiepre'].substr(md5($this->var['config']['cookie']['cookiepath'].'|'.$this->var['config']['cookie']['cookiedomain']), 0, 4).'_';


	}

	private function _init_output() {

		if($this->config['security']['urlxssdefend'] && $_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_SERVER['REQUEST_URI'])) {
			$this->_xss_check();
		}

		if($this->config['security']['attackevasive'] && (!defined('CURSCRIPT') || !in_array($this->var['mod'], array('seccode', 'secqaa', 'swfupload')) && !defined('DISABLEDEFENSE'))) {
			require_once libfile('misc/security', 'include');
		}

		if(!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false) {
			$this->config['output']['gzip'] = false;
		}

		$allowgzip = $this->config['output']['gzip'] && empty($this->var['inajax']) && $this->var['mod'] != 'attachment' && EXT_OBGZIP;
		setglobal('gzipcompress', $allowgzip);
		ob_start($allowgzip ? 'ob_gzhandler' : null);

		setglobal('charset', $this->config['output']['charset']);
		define('CHARSET', $this->config['output']['charset']);
		if($this->config['output']['forceheader']) {
			@header('Content-Type: text/html; charset='.CHARSET);
		}

	}

	public function reject_robot() {
		if(IS_ROBOT) {
			exit(header("HTTP/1.1 403 Forbidden"));
		}
	}

	private function _xss_check() {
		$temp = strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
		if(strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false) {
			system_error('request_tainting');
		}
		return true;
	}

	private function _get_client_ip() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
			foreach ($matches[0] AS $xip) {
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
					$ip = $xip;
					break;
				}
			}
		}
		return $ip;
	}

	private function _init_db() {
		if($this->init_db) {
			$driver = 'db_driver_mysql';
			if(count(getglobal('config/db/slave'))) {
				$driver = 'db_driver_mysql_slave';
			}
			DB::init($driver, $this->config['db']);
		}
	}

	private function _init_session() {

		$sessionclose = !empty($this->var['setting']['sessionclose']);
		$this->session = $sessionclose ? new core_session_close() : new core_session();

		if($this->init_session)	{
			$this->session->init($this->var['cookie']['sid'], $this->var['clientip'], $this->var['uid']);
			$this->var['sid'] = $this->session->sid;
			$this->var['session'] = $this->session->var;

			if(!empty($this->var['sid']) && $this->var['sid'] != $this->var['cookie']['sid']) {
				dsetcookie('sid', $this->var['sid'], 86400);
			}
			/* 
			//DEBUG 用户当前状态表 有业务逻辑需求可以在此增加
			if($this->var['uid'] && !$sessionclose && ($this->session->isnew || ($this->session->get('lastactivity') + 600) < TIMESTAMP)) {
				$this->session->set('lastactivity', TIMESTAMP);
				if($this->session->isnew) {
					C::t('common_member_status')->update($this->var['uid'], array('lastip' => $this->var['clientip'], 'lastvisit' => TIMESTAMP));
				}
			}*/
		}
	}
	//TODO
	private function _init_user() {
		if($this->init_user) {
			if($auth = getglobal('auth', 'cookie')) {
				$auth = daddslashes(explode("\t", authcode($auth, 'DECODE')));
			}
			list($core_pw, $core_user_id) = empty($auth) || count($auth) < 2 ? array('', '') : $auth;

			if($core_user_id) {
				$user = getuserbyuid($core_user_id, 1);
			}

			if(!empty($user) && $user['password'] == $core_pw) {
				$this->var['member'] = $user;
			} else {
				$user = array();
				$this->_init_guest();
			}
			
		} else {
			$this->_init_guest();
		}

		if(empty($this->var['cookie']['lastvisit'])) {
			$this->var['member']['lastvisit'] = TIMESTAMP - 3600;
			dsetcookie('lastvisit', TIMESTAMP - 3600, 86400 * 30);
		} else {
			$this->var['member']['lastvisit'] = $this->var['cookie']['lastvisit'];
		}
		//setglobal('uid', getglobal('user_id', 'member'));
		setglobal('user_id', $core_user_id);
		setglobal('user_name', getglobal('user_name', 'member'));
		setglobal('user_realname', getglobal('user_realname', 'member'));
		setglobal('user_group_id', getglobal('user_group_id', 'member'));
                setglobal('d_id', getglobal('d_id', 'member'));
		if(getglobal('user_role_id', 'member')){
			setglobal('user_role_id', getglobal('user_role_id', 'member'));
		}else{
			setglobal('user_role_id', 1);
		}
                //DEBUG  初始化角色名称
                if(getglobal('user_role_id', 'member')){
                    setglobal('role_name', ext::role_name(getglobal('user_role_id', 'member')));
                }
		setglobal('user_level_id', getglobal('user_level_id', 'member'));
                //初始化所属区域与学校
                if(getglobal('d_id', 'member')){
                    setglobal('d_name', ext::d_name(getglobal('d_id', 'member')));
                }
		/*
		if($core_user_id) {
			//TODO user_access 暂未有业务逻辑需求
			//$user_access = ext::getuseraccessbyuid($core_user_id);
			//setglobal('user_access', $user_access);
		}
		*/
		//DEBUG 初始化用户菜单
		$login_user_menu = array();
		$user_menu = $this->var['setting']['user_role_menu']['user_menu'];
		$role_menu = $this->var['setting']['user_role_menu']['role_menu'];
		$user_role_id = getglobal('user_role_id');
		if(!empty($user_menu[$core_user_id])){
			$login_user_menu = $user_menu[$core_user_id]['menu_url_tree'];
			$login_user_menu_url_md5 = $user_menu[$core_user_id]['menu_url_md5'];
		}elseif(!empty($role_menu[$user_role_id])){
			$login_user_menu = $role_menu[$user_role_id]['menu_url_tree'];
			$login_user_menu_url_md5 = $role_menu[$user_role_id]['menu_url_md5'];
		}
		setglobal('setting/user_role_menu', '');
		setglobal('login_user_menu', $login_user_menu);
		setglobal('login_user_menu_url_md5', $login_user_menu_url_md5);
	}

	private function _init_guest() {
		$username = '';
		if(!empty($this->var['cookie']['con_auth_hash']) && ($openid = authcode($this->var['cookie']['con_auth_hash']))) {
			$this->var['connectguest'] = 1;
			$username = 'QQ_'.substr($openid, -6);
			$this->var['setting']['cacheindexlife'] = 0;
			$this->var['setting']['cachethreadlife'] = 0;
			$groupid = $this->var['setting']['connect']['guest_groupid'] ? $this->var['setting']['connect']['guest_groupid'] : $this->var['setting']['newusergroupid'];
		}
		setglobal('member', array( 'uid' => 0, 'timeoffset' => 9999));
	}

	private function _init_cron() {
		$ext = empty($this->config['remote']['on']) || empty($this->config['remote']['cron']) || APPTYPEID == 200;
		if($this->init_cron && $this->init_setting && $ext) {
			if($this->var['cache']['cronnextrun'] <= TIMESTAMP) {
				core_cron::run();
			}
		}
	}

	private function _init_misc() {
		if(!$this->init_misc) {
			return false;
		}
		lang('core');

		if($this->init_setting && $this->init_user) {
			if(!isset($this->var['member']['timeoffset']) || $this->var['member']['timeoffset'] == 9999 || $this->var['member']['timeoffset'] === '') {
				$this->var['member']['timeoffset'] = $this->var['setting']['timeoffset'];
			}
		}

		$timeoffset = $this->init_setting ? $this->var['member']['timeoffset'] : $this->var['setting']['timeoffset'];
		$this->var['timenow'] = array(
			'time' => dgmdate(TIMESTAMP),
			'offset' => $timeoffset >= 0 ? ($timeoffset == 0 ? '' : '+'.$timeoffset) : $timeoffset
		);
		$this->timezone_set($timeoffset);

		$this->var['formhash'] = formhash();
		define('FORMHASH', $this->var['formhash']);

		if($this->init_user) {
			$allowvisitflag = in_array(CURSCRIPT, array('member')) || defined('ALLOWGUEST') && ALLOWGUEST;
			if($this->var['group'] && isset($this->var['group']['allowvisit']) && !$this->var['group']['allowvisit']) {
				if($this->var['uid'] && !$allowvisitflag) {
					showmessage('user_banned');
				} elseif((!defined('ALLOWGUEST') || !ALLOWGUEST) && !in_array(CURSCRIPT, array('member', 'api')) && !$this->var['inajax']) {
					dheader('location: member.php?mod=logging&action=login&referer='.rawurlencode($this->var['siteurl'].$this->var['basefilename'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '')));
				}
			}
			if(isset($this->var['member']['status']) && $this->var['member']['status'] == -1 && !$allowvisitflag) {
				showmessage('user_banned');
			}
		}

		if($this->var['setting']['ipaccess'] && !ipaccess($this->var['clientip'], $this->var['setting']['ipaccess'])) {
			showmessage('user_banned');
		}

		if($this->var['setting']['bbclosed']) {
			if($this->var['uid'] && ($this->var['group']['allowvisit'] == 2 || $this->var['groupid'] == 1)) {
			} elseif(in_array(CURSCRIPT, array('admin', 'member', 'api')) || defined('ALLOWGUEST') && ALLOWGUEST) {
			} else {
				$closedreason = C::t('common_setting')->fetch('closedreason');
				$closedreason = str_replace(':', '&#58;', $closedreason);
				showmessage($closedreason ? $closedreason : 'board_closed', NULL, array('adminemail' => $this->var['setting']['adminemail']), array('login' => 1));
			}
		}

		if(CURSCRIPT != 'admin' && !(in_array($this->var['mod'], array('logging', 'seccode')))) {
			periodscheck('visitbanperiods');
		}

		if(defined('IN_MOBILE')) {
			$this->var['tpp'] = $this->var['setting']['mobile']['mobiletopicperpage'] ? intval($this->var['setting']['mobile']['mobiletopicperpage']) : 20;
			$this->var['ppp'] = $this->var['setting']['mobile']['mobilepostperpage'] ? intval($this->var['setting']['mobile']['mobilepostperpage']) : 5;
		} else {
			$this->var['tpp'] = $this->var['setting']['topicperpage'] ? intval($this->var['setting']['topicperpage']) : 20;
			$this->var['ppp'] = $this->var['setting']['postperpage'] ? intval($this->var['setting']['postperpage']) : 10;
		}

		if($this->var['setting']['nocacheheaders']) {
			@header("Expires: -1");
			@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
			@header("Pragma: no-cache");
		}

		if($this->session->isnew && $this->var['uid']) {
			updatecreditbyaction('daylogin', $this->var['uid']);

			include_once libfile('function/stat');
			updatestat('login', 1);
			if(defined('IN_MOBILE')) {
				updatestat('mobilelogin', 1);
			}
			if($this->var['setting']['connect']['allow'] && $this->var['member']['conisbind']) {
				updatestat('connectlogin', 1);
			}
		}
		if(isset($this->var['member']['conisbind']) && $this->var['member']['conisbind'] && $this->var['setting'] && $this->var['setting']['connect']['newbiespan'] !== '') {
			$this->var['setting']['newbiespan'] = $this->var['setting']['connect']['newbiespan'];
		}

		$lastact = TIMESTAMP."\t".dhtmlspecialchars(basename($this->var['PHP_SELF']))."\t".dhtmlspecialchars($this->var['mod']);
		dsetcookie('lastact', $lastact, 86400);
		setglobal('currenturl_encode', base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));

		if((!empty($_GET['fromuid']) || !empty($_GET['fromuser'])) && ($this->var['setting']['creditspolicy']['promotion_visit'] || $this->var['setting']['creditspolicy']['promotion_register'])) {
			require_once libfile('misc/promotion', 'include');
		}

		$this->var['seokeywords'] = !empty($this->var['setting']['seokeywords'][CURSCRIPT]) ? $this->var['setting']['seokeywords'][CURSCRIPT] : '';
		$this->var['seodescription'] = !empty($this->var['setting']['seodescription'][CURSCRIPT]) ? $this->var['setting']['seodescription'][CURSCRIPT] : '';
	}

	private function _init_setting() {
		if($this->init_setting) {
			if(empty($this->var['setting'])) {
				$this->cachelist[] = 'setting';
			}
//			if(empty($this->var['style'])) {
//				$this->cachelist[] = 'style_default';
//			}
//			if(!isset($this->var['cache']['cronnextrun'])) {
//				$this->cachelist[] = 'cronnextrun';
//			}
		}
		!empty($this->cachelist) && loadcache($this->cachelist);
		if(!is_array($this->var['setting'])) {
			$this->var['setting'] = array();
		}
	}

        private function _init_position(){
                $login_user_menu = getglobal('login_user_menu');
                //DEBUG 通过mod 查询当前mod下的子菜单树形数组
                if($this->init_position && $this->var['mod'] && !empty($login_user_menu)) {
                    foreach($login_user_menu[1]["submenu"][4]["submenu"] AS $key => $value){
                        if(!empty($value['position']) && $value['position']==$this->var['mod']){
                            $current_mod = array(
                                'name_var' => $value['name_var'],
                                'left_menu' => $value['submenu']
                            );
                        }
                    }
		}
		setglobal('current_mod', $current_mod);
                //DEBUG 设置用户当前路径 breadcrumb, 取第一维数组为轨迹路径
                $current_position = $this->var['mod'].'_'.$this->var['action'].'_'.$this->var['do'];
                $treepath = array();
                $treepath = get_treepath($current_mod['left_menu'],0,$current_position);
                unset($treepath['stop']);
                setglobal('breadcrumb', $treepath);
        }

	public function _init_style() {
		$styleid = !empty($this->var['cookie']['styleid']) ? $this->var['cookie']['styleid'] : 0;
		if(intval(!empty($this->var['forum']['styleid']))) {
			$this->var['cache']['style_default']['styleid'] = $styleid = $this->var['forum']['styleid'];
		} elseif(intval(!empty($this->var['category']['styleid']))) {
			$this->var['cache']['style_default']['styleid'] = $styleid = $this->var['category']['styleid'];
		}

		$styleid = intval($styleid);

		if($styleid && $styleid != $this->var['setting']['styleid']) {
			loadcache('style_'.$styleid);
			if($this->var['cache']['style_'.$styleid]) {
				$this->var['style'] = $this->var['cache']['style_'.$styleid];
			}
		}

		define('IMGDIR', $this->var['style']['imgdir']);
		define('STYLEID', $this->var['style']['styleid']);
		define('VERHASH', $this->var['style']['verhash']);
    //DEBUG 自定义模版路径 未采取与DZF一致
		//define('TPLDIR', $this->var['style']['tpldir']);
    define('TPLDIR', './template/'.$this->var['setting']['tpldir']);
		define('TEMPLATEID', $this->var['style']['templateid']);
	}

	private function _init_mobile() {
		if(!$this->init_mobile) {
			return false;
		}
		if($this->var['inajax']) {
			return false;
		}

		if(!$this->var['setting'] || !$this->var['setting']['mobile']['allowmobile'] || !is_array($this->var['setting']['mobile']) || IS_ROBOT) {
			$nomobile = true;
			$unallowmobile = true;
		}

		if(getgpc('mobile') === 'no') {
			dsetcookie('mobile', 'no', 3600);
			$nomobile = true;
		} elseif($this->var['cookie']['mobile'] == 'no' && getgpc('mobile') === 'yes') {
			dsetcookie('mobile', '');
		} elseif($this->var['cookie']['mobile'] == 'no') {
			$nomobile = true;
		} elseif(!checkmobile()) {
			$nomobile = true;
		}

		if(!$this->var['mobile'] && !$unallowmobile) {
			if(getgpc('mobile') === 'yes') {
				dheader("Location:misc.php?mod=mobile");
			}
		}

		if($nomobile || (!$this->var['setting']['mobile']['mobileforward'] && getgpc('mobile') !== 'yes')) {
			if($_SERVER['HTTP_HOST'] == $this->var['setting']['domain']['app']['mobile'] && $this->var['setting']['domain']['app']['default']) {
				dheader("Location:http://".$this->var['setting']['domain']['app']['default'].$_SERVER['REQUEST_URI']);
				return false;
			} else {
				return false;
			}
		}

		if(strpos($this->var['setting']['domain']['defaultindex'], CURSCRIPT) !== false && CURSCRIPT != 'forum' && !$_GET['mod']) {
			if($this->var['setting']['domain']['app']['mobile']) {
				$mobileurl = 'http://'.$this->var['setting']['domain']['app']['mobile'];
			} else {
				if($this->var['setting']['domain']['app']['forum']) {
					$mobileurl = 'http://'.$this->var['setting']['domain']['app']['forum'].'?mobile=yes';
				} else {
					$mobileurl = $this->var['siteurl'].'forum.php?mobile=yes';
				}
			}
			dheader("location:$mobileurl");
		}
		define('IN_MOBILE', true);
		setglobal('gzipcompress', 0);

		$arr = array(strstr($_SERVER['QUERY_STRING'], '&simpletype'), strstr($_SERVER['QUERY_STRING'], 'simpletype'), '&mobile=yes', 'mobile=yes');
		$query_sting_tmp = str_replace($arr, '', $_SERVER['QUERY_STRING']);
		$this->var['setting']['mobile']['nomobileurl'] = ($this->var['setting']['domain']['app']['forum'] ? 'http://'.$this->var['setting']['domain']['app']['forum'].'/' : $this->var['siteurl']).$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=no';

		$this->var['setting']['lazyload'] = 0;

		if('utf-8' != CHARSET) {
			if(strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
				foreach($_POST AS $pk => $pv) {
					if(!is_numeric($pv)) {
						$_GET[$pk] = $_POST[$pk] = $this->mobile_iconv_recurrence($pv);
						if(!empty($this->var['config']['input']['compatible'])) {
							$this->var['gp_'.$pk] = daddslashes($_GET[$pk]);
						}
					}
				}
			}
		}

		if($_GET['simpletype']) {
			if($_GET['simpletype'] == 'yes') {
				$this->var['setting']['mobile']['mobilesimpletype'] = 1;
				dsetcookie('simpletype', 1, 86400);
			} else {
				$this->var['setting']['mobile']['mobilesimpletype'] = 0;
				dsetcookie('simpletype', 0, 86400);
			}
		} elseif($this->var['cookie']['simpletype']) {
			$this->var['setting']['mobile']['mobilesimpletype'] = $this->var['cookie']['simpletype'] == 1 ? 1 : 0 ;
		}

		if(!$this->var['setting']['mobile']['mobilesimpletype']) {
			$this->var['setting']['imagemaxwidth'] = 224;
		}

		$this->var['setting']['regstatus'] = $this->var['setting']['mobile']['mobileregister'] ? $this->var['setting']['regstatus'] : 0 ;
		if(!$this->var['setting']['mobile']['mobileseccode']) {
			$this->var['setting']['seccodestatus'] = 0;
		}

		$this->var['setting']['seccodedata']['type'] = 99;
		$this->var['setting']['thumbquality'] = 50;


		$this->var['setting']['mobile']['simpletypeurl'] = array();
		$this->var['setting']['mobile']['simpletypeurl'][0] = $this->var['siteurl'].$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=yes&simpletype=no';
		$this->var['setting']['mobile']['simpletypeurl'][1] =  $this->var['siteurl'].$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=yes&simpletype=yes';
		unset($query_sting_tmp);
		ob_start();
	}

	public function timezone_set($timeoffset = 8) {
		if(function_exists('date_default_timezone_set')) {
			@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}
	}

  public function mobile_iconv_recurrence($value) {
		if(is_array($value)) {
			foreach($value AS $key => $val) {
				$value[$key] = $this->mobile_iconv_recurrence($val);
			}
		} else {
			$value = diconv($value, 'utf-8', CHARSET);
		}
		return $value;
	}
}

?>