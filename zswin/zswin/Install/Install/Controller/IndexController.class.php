<?php
namespace Install\Controller;
use Think\Controller;
use Think\Db;
class IndexController extends Controller {
	
	
    public function _initialize() {
    	
    	
		L(include MODULE_PATH. '/Lang/common.php');
	}
	
    public function index(){
    if (is_file('./Data/install.lock')) {
	header('Location: ./index.php');
	exit;
}
    	
    	$this->assign('step_curr', 'eula');
		$jianjie_html = file_get_contents(MODULE_PATH.'/View/public/jianjie.html');
		$this->assign('jianjie_html', $jianjie_html);
		if (IS_POST) {
			$accept = I('post.accept','','intval');
			if (!$accept) {
				$error_msg = L('please_accept');
				$this->assign('error_msg', $error_msg);
				$this->display();
			} else {
				$this->redirect('check');
			}
		} else {
			$this->display();
		}
    }
/**
	 * 环境检测
	 */
	public function check() {

		$flag = true;
		//检测文件夹权限
		$check_file = array(
            './Data',
		    './Runtime',
            './Uploads',//上传目录【含头像】
            
           
           
		);
		$error = array();
		foreach ($check_file as $file) {
			$path_file =  $file;
			if (!file_exists($path_file)) {
				$error[] = $file . L('not_exists');
				$flag = false;
				continue;
			}
			if (!is_writable($path_file)) {
				$error[] = $file . L('not_writable');
				$flag = false;
			}
		}
		if (!function_exists('curl_getinfo')) {
			$error[] = L('no_curl');
			$flag = false;
		}
	    if (!function_exists('mb_strlen')) {
			$error[] = '请确保在php.ini中加载了php_mbstring.dll';
			$flag = false;
		}

		if (!function_exists('gd_info')) {
			$error[] = L('no_gd');
			$flag = false;
		}
		$dir_obj = new \OT\Dir;
		is_dir('./Runtime') && $dir_obj->delDir('./Runtime');

		if (!$flag) {
			$this->assign('error', $error);
			$this->assign('step_curr', 'check');
			$this->display('check');
		} else {
			$this->redirect('setconf');
		}
		
		
	}
/**
	 * 网站配置
	 */
	public function setconf() {
		$this->assign('step_curr', 'setconf');
		if ($_POST) {
			foreach ($_POST as $key => $val) {
				$this->assign($key, $val);
			}
			extract($_POST);
			if (!$db_type ||!$db_host || !$db_port || !$db_name || !$db_user || !$db_prefix || !$admin_user || !$admin_email || !$admin_pass) {
				$this->assign('error_msg', L('please_input_config_info'));
				$this->display();
				exit;
			}
			if (!$this->_is_email($admin_email)) {
				$this->assign('error_msg', L('admin_email_format_incorrect'));
				$this->display();
				exit;
			}
			if ($admin_pass != $admin_pass_confirm) {
				$this->assign('error_msg', L('admin_pass_error'));
				$this->display();
				exit;
			}
			//试着连接数据库
			$conn = @mysql_connect($db_host . ':' . $db_port, $db_user, $db_pass);
			if (!$conn) {
				$this->assign('error_msg', L('connect_mysql_error'));
				$this->display();
				exit;
			}
			$selected_db = @mysql_select_db($db_name);
			if ($selected_db) {
				//如果数据库存在 并且里面安装过   提示是否覆盖
				$query = @mysql_query("SHOW TABLES LIKE '{$db_prefix}%'");
				$was_install = false;
				while ($row = mysql_fetch_assoc($query)) {
					$was_install = true;
					break;
				}
				if ($was_install && !isset($force_install)) {
					$this->assign('database_name_tip', L('db_isset'));
					$this->display();
					exit;
				} else {
					$this->_set_temp($_POST);
					$this->redirect('install');
				}
			} else {
				if (mysql_get_server_info($conn) > '4.1') {
					$charset = C('DEFAULT_CHARSET');
					$sql = "CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET " . str_replace('-', '', $charset);
				} else {
					$sql = "CREATE DATABASE IF NOT EXISTS `{$db_name}`";
				}
				if (!mysql_query($sql, $conn)) {
					$this->assign('error_msg', L('create_db_error'));
					$this->display();
					exit;
				}
				$this->_set_temp($_POST);
				$this->redirect('install');
			}
		} else {
			
			
			$this->assign('database_name_tip', L('database_name_tip'));
			$this->assign('db_host', '127.0.0.1');
           
			$this->assign('db_port', '3306');
			$this->assign('db_user', 'root');
			$this->assign('db_name', 'zswin');
			$this->assign('db_prefix', 'zs_');
			
			$this->assign('admin_user', 'admin');
			$this->assign('db_pass', '');
			$this->assign('admin_pass', '');
			$this->assign('admin_pass_confirm', '');
			$this->assign('admin_email', '');
			$this->display();
		}
	}
	/**
	 * 开始安装
	 */
	public function install() {
		$this->assign('step_curr', 'install');
		$this->display();
	}
/**
	 * 执行安装
	 */
	public function finish_done() {
		$charset = C('DEFAULT_CHARSET');
		header('Content-type:text/html;charset=' . $charset);
		$temp_info = F('temp_data');
		$conn = mysql_connect($temp_info['db_host'] . ':' . $temp_info['db_port'], $temp_info['db_user'], $temp_info['db_pass']);
		$version = mysql_get_server_info();
		$charset = str_replace('-', '', $charset);
		if ($version > '4.1') {
			if ($charset != 'latin1') {
				mysql_query("SET character_set_connection={$charset}, character_set_results={$charset}, character_set_client=binary", $conn);
			}if ($version > '5.0.1') {
				mysql_query("SET sql_mode=''", $conn);
			}
		}
		$selected_db = mysql_select_db($temp_info['db_name'], $conn);
		//开始创建数据表
		$this->_show_process(L('create_table_begin'));
		$sqls = $this->_get_sql(APP_PATH . 'Install/sqldata/create_table.sql');
		foreach ($sqls as $sql) {
			//替换前缀
			$sql = str_replace('`zswin_', '`' . $temp_info['db_prefix'], $sql);
			//获得表名
			$run = mysql_query($sql, $conn);
			if (substr($sql, 0, 12) == 'CREATE TABLE') {
				$table_name = $temp_info['db_prefix'] . preg_replace("/CREATE TABLE `" . $temp_info['db_prefix'] . "([a-z0-9_]+)` .*/is", "\\1", $sql);
				
				$this->_show_process(sprintf(L('create_table_successed'), $table_name));
			}
		}
		//开始导入数据
		$this->_show_process(L('insert_initdate_begin'));
		$sqls = $this->_get_sql(APP_PATH . 'Install/sqldata/initdata.sql');
		
	    $weburl= $_SERVER["HTTP_HOST"];
		foreach ($sqls as $sql) {
			//替换前缀
			$sql = str_replace('`zswin_', '`' . $temp_info['db_prefix'], $sql);
			$sql = str_replace('127.0.0.1', $weburl, $sql);
			$run = mysql_query($sql, $conn);
			//获得表名
			if (substr($sql, 0, 11) == 'INSERT INTO') {
				$table_name = $temp_info['db_prefix'] . preg_replace("/INSERT INTO `" . $temp_info['db_prefix'] . "([a-z0-9_]+)` .*/is", "\\1", $sql);
				
				$this->_show_process(sprintf(L('insert_initdate_successed'), $table_name));
				
			}
		   
			
			
		}
	    $sqls = $this->_get_sql(APP_PATH . 'Install/sqldata/area.sql');
		
	   
		foreach ($sqls as $sql) {
			//替换前缀
			$sql = str_replace('`zswin_', '`' . $temp_info['db_prefix'], $sql);
			$run = mysql_query($sql, $conn);
			//获得表名
			if (substr($sql, 0, 11) == 'INSERT INTO') {
				$table_name = $temp_info['db_prefix'] . preg_replace("/INSERT INTO `" . $temp_info['db_prefix'] . "([a-z0-9_]+)` .*/is", "\\1", $sql);
				$this->_show_process(sprintf(L('insert_initdate_successed'), $table_name));
				
				
				
			}
		}
		
		
		$this->_show_process('注册创始人帐号');
		
		//注册创始人帐号
		//修改配置文件
		$auth  = build_auth_key();
		
		$config_data['DB_TYPE'] = $temp_info['db_type'];
		$config_data['DB_HOST'] = $temp_info['db_host'];
		$config_data['DB_NAME'] = $temp_info['db_name'];
		$config_data['DB_USER'] = $temp_info['db_user'];
		$config_data['DB_PWD'] = $temp_info['db_pass'];
		$config_data['DB_PORT'] = $temp_info['db_port'];
		$config_data['DB_PREFIX'] = $temp_info['db_prefix'];
		$db = Db::getInstance($config_data);
		$config_data['WEB_MD5'] = $auth;
		$conf 	=	write_config($config_data);
		
		
		
		
		register_administrator($db, $temp_info['db_prefix'], $temp_info, $auth);
         
         $this->_show_process('注册创始人帐号成功');
         //锁定安装程序
		touch('./Data/install.lock');
		//$password = hash ( 'md5', $temp_info['admin_pass'] );
		
		//$sqls[] = "INSERT INTO `" . $temp_info['db_prefix'] . "user` VALUES " .
		 //  "('1', '" . $temp_info['admin_user'] . "', '管理员', '" . $password . "', '', '". NOW_TIME."', '".get_client_ip(1)."', 1, 8888, '" . $temp_info['admin_email'] . "', '', '". NOW_TIME."', '". NOW_TIME."', 1, 0, '', 0)";
		//安装完毕
		$this->_show_process(L('install_successed'), 'parent.install_successed();');
	//创建配置文件
		
        return false;
	}
	public function finish() {
		$this->assign('step_curr', 'finish');
		
		$this->display();
	}
	private function _is_email($email) {//检测输入的是否符合邮箱格式
		$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}\$/i";
		if (strpos($email, '@') !== false && strpos($email, '.') !== false) {
			if (preg_match($chars, $email)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
/**
	 * 显示安装进程
	 */
	private function _show_process($msg, $script = '') {
		echo '<script type="text/javascript">parent.show_process(\'<p><span>' . $msg . '</span></p>\');' . $script . '</script>';
		flush();
		ob_flush();
	}

	private function _set_temp($temp_data) {
		F('temp_data', $temp_data);
	}

	private function _get_sql($sql_file) {
		$contents = file_get_contents($sql_file);
		$contents = str_replace("\r\n", "\n", $contents);
		$contents = trim(str_replace("\r", "\n", $contents));
		$return_items = $items = array();
		$items = explode(";\n", $contents);

		foreach ($items as $item) {
			$return_item = '';
			$item = trim($item);
			$lines = explode("\n", $item);
			foreach ($lines as $line) {
				if (isset($line[1]) && $line[0] . $line[1] == '--') {
					continue;
				}
				$return_item .= $line;
			}
			if ($return_item) {
				$return_items[] = $return_item; //.";";
			}
		}
		return $return_items;
	}
	
	
}

