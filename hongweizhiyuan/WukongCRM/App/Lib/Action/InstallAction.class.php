<?php 
class InstallAction extends Action {	
	
	private $upgrade_site = "http://upgrade.5kcrm.com/";
	
	public function index(){
		if (file_exists(CONF_PATH . "install.lock")) {
			$this->error(L('PLEASE_DO_NOT_REPEAT_INSTALLATION'));		
		}	
		if (!file_exists(getcwd() . "/Public/sql/5kcrm.sql")) {
			$this->error(L('LACK_THE_NECESSARY_DATABASE_FILES'));		
		}
		if ($_POST['submit']) {
			$db_config['DB_TYPE'] = 'mysql';
			$db_config['DB_HOST'] = $_POST['DB_HOST'];
			$db_config['DB_PORT'] = $_POST['DB_PORT'];
			$db_config['DB_NAME'] = $_POST['DB_NAME'];
			$db_config['DB_USER'] = $_POST['DB_USER'];
			$db_config['DB_PWD'] = $_POST['DB_PWD'];		
			$db_config['DB_PREFIX'] = $_POST['DB_PREFIX'];
			
			$name = $_POST['name'];
			$password = $_POST['password'];
			
			$warnings = array();
			if (empty($db_config['DB_HOST'])) {
				$warnings[] = L('PLEASE_FILL_IN_THE_DATABASE host');
			}			
			if (empty($db_config['DB_PORT'])) {
				$warnings[] = L('PLEASE_FILL_OUT_THE_DATABASE_PORT');
			}
			if (preg_match('/[^0-9]/', $db_config['DB_PORT'])) {
				$warnings[] = L('DATABASE_PORT_ONLY_NUMBERS');
			}
			if (empty($db_config['DB_NAME'])) {
				$warnings[] = L('PLEASE_FILL_IN_THE_DATABASE_NAME');
			}
			if (empty($db_config['DB_USER'])) {
				$warnings[] = L('PLEASE_FILL_IN_THE_DATABASE_USER_NAME');
			}
			if (empty($db_config['DB_PREFIX'])) {
				$warnings[] = L('PLEASE_FILL_IN_THE_TABLE_PREFIX');
			}
			if (preg_match('/[^a-z0-9_]/i', $db_config['DB_PREFIX'])) {
				$warnings[] = L('THE_TABLE_PREFIX_CAN_CONTAIN_ONLY_NUMBERS_LETTERS_AND_UNDERSCORES');
			}
			if (empty($name)) {
				$warnings[] = L('PLEASE_FILL_IN_THE_ADMINISTRATOR_USER_NAME');
			}
			if (empty($password)) {
				$warnings[] = L('PLEASE_FILL_IN_THE_ADMINISTRATOR_PASSWORD');
			}

			if (empty($warnings)) {
				$connect = mysql_connect($db_config['DB_HOST'] . ":" . $db_config['DB_PORT'], $db_config['DB_USER'], $db_config['DB_PWD']);
				if(!$connect) {
					$warnings[] = L('THE_DATABASE_CONNECTION_FAILED_PLEASE_CHECK_THE_CONFIGURATION');
				} else {
					if(!mysql_select_db($db_config['DB_NAME'])) {
						if(!mysql_query("create database ".$db_config['DB_NAME']." DEFAULT CHARACTER SET utf8")) {
							$warnings[] = L('DO_NOT_FIND_YOU_FILL_OUT_THE_DATABASE_NAME_AND_CANNOT_BE_CREATED');
						}
					}
				} 
				if(!check_dir_iswritable(APP_PATH.'Runtime')){
					$warnings[] = L("RUNTIME_FOLDER_REQUIRES_WRITE_ACCES",array(APP_PATH));
				}
				if(!check_dir_iswritable(CONF_PATH)){
					$warnings[] = L("CONF_FOLDER_REQUIRES_WRITE_ACCES",array(APP_PATH));
				}
			}
			if (empty($warnings)) {
				$db_config_str 	 = 	"<?php\r\n";
				$db_config_str	.=	"return array(\r\n";
				foreach($db_config as $k => $v) {
					$db_config_str .= "'" . $k."'=>'".$v."',\r\n";
					C($k,$v);
				}
				$db_config_str.=");";
				if(file_put_contents(CONF_PATH . "db.php", $db_config_str)){
					$db = M();
                    $sql = file_get_contents(getcwd() . "/Public/sql/5kcrm.sql");
                    $sql = str_replace("5kcrm_", C('DB_PREFIX'), $sql); 
                    $sql = str_replace("http://demo.5kcrm.com", __ROOT__, $sql);
					$sql = str_replace("\r\n", "", $sql); 
					$queries = explode(";\n", $sql); 
					foreach ($queries as $val) {
						if(trim($val)) { 
							$db->query($val); 
						} 
					}
					$salt = substr(md5(time()),0,4);
					$password = md5(md5(trim($password)) . $salt);
                    $db->query('insert into ' . C('DB_PREFIX') . 'user (role_id, category_id, status, name, password, salt, reg_ip, reg_time) values (1, 1, 1, "'.$name.'", "'.$password.'", "'.$salt.'", "'.get_client_ip().'", '.time().')'); 
					touch(CONF_PATH . "install.lock");
				}			
				$this->display('install');
			} else {
				$this->assign('warnings', $warnings);
				$this->display();
			}
		} else {
			$this->assign('errors', $this->checkEnv());
			$this->display();	
		}
    }

	public function upgradeProcess() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$dir = getcwd() . "/Public/sql/";
		$upgrade_list = array();
		if(is_dir($dir)){  
			if( $dir_handle = opendir($dir) ){
				while (false !== ( $file_name = readdir($dir_handle)) ) {
					if($file_name=='.' or $file_name =='..'){
						continue;
					} elseif ($file_name != "5kcrm.sql" && strpos($file_name,'.sql')){
						$upgrade_list[] = $file_name;
					}
				}
			}
		}
		$db = M();
		foreach($upgrade_list as $upgrade){
			$sql .= file_get_contents($dir.$upgrade);
		}
        $sql = str_replace("5kcrm_", C('DB_PREFIX'), $sql); 
		$sql = str_replace("http://demo.5kcrm.com", __ROOT__, $sql);
		$sql = str_replace("\r\n", "\n", $sql); 
		$queries = explode(";\n", $sql); 
		
		$sum = sizeof($queries);
		if ($id < $sum) {
			if(trim($queries[$id])) { 
				$db->query($queries[$id]); 
			} 
		}
		$id++;
		if($id >= $sum){
			foreach($upgrade_list as $upgrade){
				@unlink($dir.$upgrade);
			}
		}
		$this->ajaxReturn($id, floor($id*100/$sum) . "%", 1);
	}
	public function upgrade() {
		$dir = getcwd() . "/Public/sql/";
		$upgrade_list = array();
		if(is_dir($dir)){  
			if( $dir_handle = opendir($dir) ){
				while (false !== ( $file_name = readdir($dir_handle)) ) {
					if($file_name=='.' or $file_name =='..'){
						continue;
					} elseif ($file_name != "5kcrm.sql" && strpos($file_name,'.sql')){
						$upgrade_list[] = $file_name;
					}
				}
			}
		}
		if (!empty($upgrade_list)) {
			sort($upgrade_list);
			if(file_exists(RUNTIME_FILE)){
				@unlink(RUNTIME_FILE);
			}
			$cachedir=RUNTIME_PATH."/Cache/";
			$cachefieldsdir=RUNTIME_PATH."/Data/_fields/";
			$cachetempdir=RUNTIME_PATH."/Temp/";
            if(is_dir($cachedir)){
				$cd = opendir($cachedir);
				while (($file = readdir($cd)) !== false) {
					if($file=='.' or $file =='..'){
						@unlink($cachedir.$file);
					}
				}
				closedir($cd);
			}
            
			if(is_dir($cachefieldsdir)){
				$cfd = opendir($cachefieldsdir);
				while (($file = readdir($cfd)) !== false) {
					if($file=='.' or $file =='..'){
						@unlink($cachefieldsdir.$file);
					}
				}
				closedir($cfd);
			}
            
            if(is_dir($cachefieldsdir)){
				$ctd = opendir($cachetempdir);
				while (($file = readdir($ctd)) !== false) {
					if($file=='.' or $file =='..'){
						@unlink($cachetempdir.$file);
					}
				}
				closedir($ctd);
            }
			
			$this->upgrade_list = $upgrade_list;
			$this->display();
		} else {
			$this->error(L('NO_CHECK_TO_UPGRADE_FILE'));	
		}			
	}
		
	private function checkEnv() {
		$errors = array();
		
		if(substr(PHP_VERSION, 0, 1) < 5) {
    		$errors[] = L('PLEASE_UPGRADE_YOUR_SERVER');
    	}
		
		if(!extension_loaded('gd')) {
    		$errors[] = L('PLEASE_OPEN_THE_GD_LIBRARY');
    	}
		
		if(!function_exists("curl_init")) {
    		$errors[] = L('PLEASE_OPEN_THE_CURL_EXTENSIONS');
    	}
		
		if (!function_exists('gzopen')) {
			$this->error(L('PLEASE_OPEN_THE_ZLIB_EXTENSIONS'));
		}
		if(!function_exists("mb_strlen")) {
    		$errors[] = L('PLEASE_OPEN_THE_MB_STRING_FUNCTION_LIBRARY');
    	}
		
		if(!is_writable(RUNTIME_PATH)) {
    		$errors[] = L('DIRECTORY_CANNOT_WRITE',array(RUNTIME_PATH));
    	}
		if(!is_writable(CONF_PATH)) {
    		$errors[] = L('DIRECTORY_CANNOT_WRITE',array(CONF_PATH));
    	}
		if(!is_writable(DATA_PATH)) {
    		$errors[] = L('DIRECTORY_CANNOT_WRITE1',array(DATA_PATH));
    	}
		if(!is_writable(CACHE_PATH)) {
    		$errors[] = L('DIRECTORY_CANNOT_WRITE2',array(CACHE_PATH));
    	}
		if(!is_writable(TEMP_PATH)) {
    		$errors[] = L('DIRECTORY_CANNOT_WRITE3',array(TEMP_PATH));
    	}
		
		return $errors;
	}
	
	public function checkVersion(){	
		$params = array('version'=>C('VERSION'), 'release'=>C('RELEASE'));
		$info = sendRequest($this->upgrade_site . 'index.php?m=index&a=checkVersion', $params);
		if ($info){
			$this->ajaxReturn($info);
		} else {
			$this->ajaxReturn(0, L('CHECK_THE_NEW_VERSION '), 0);
		}
	}
}