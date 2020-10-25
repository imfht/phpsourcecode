<?php
class ControllerStep3 extends Controller {
	private $error = array();
	
	public function index() {		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('install');
			
			$this->model_install->mysql($this->request->post);
			
			$output  = '<?php' . "\n";
			$output .= '// HTTP' . "\n";
			$output .= 'define(\'HTTP_SERVER\', \'' . HTTP_SHOPILEX . '\');' . "\n";
			$output .= 'define(\'HTTP_IMAGE\', \'' . HTTP_SHOPILEX . 'image/\');' . "\n";			
			$output .= 'define(\'HTTP_ADMIN\', \'' . HTTP_SHOPILEX . 'admin/\');' . "\n\n";

			$output .= '// HTTPS' . "\n";
			$output .= 'define(\'HTTPS_SERVER\', \'' . HTTP_SHOPILEX . '\');' . "\n";
			$output .= 'define(\'HTTPS_IMAGE\', \'' . HTTP_SHOPILEX . 'image/\');' . "\n\n";
						
			$output .= '// DIR' . "\n";
			$output .= 'define(\'DIR_APPLICATION\', \'' . DIR_SHOPILEX . 'catalog/\');' . "\n";
			$output .= 'define(\'DIR_SYSTEM\', \'' . DIR_SHOPILEX. 'system/\');' . "\n";
			$output .= 'define(\'DIR_DATABASE\', \'' . DIR_SHOPILEX . 'system/database/\');' . "\n";
			$output .= 'define(\'DIR_LANGUAGE\', \'' . DIR_SHOPILEX . 'catalog/language/\');' . "\n";
			$output .= 'define(\'DIR_TEMPLATE\', \'' . DIR_SHOPILEX . 'catalog/view/theme/\');' . "\n";
			$output .= 'define(\'DIR_CONFIG\', \'' . DIR_SHOPILEX . 'system/config/\');' . "\n";
			$output .= 'define(\'DIR_IMAGE\', \'' . DIR_SHOPILEX . 'image/\');' . "\n";
			$output .= 'define(\'DIR_CACHE\', \'' . DIR_SHOPILEX . 'system/cache/\');' . "\n";
			$output .= 'define(\'DIR_DOWNLOAD\', \'' . DIR_SHOPILEX . 'download/\');' . "\n";
			$output .= 'define(\'DIR_LOGS\', \'' . DIR_SHOPILEX . 'system/logs/\');' . "\n\n";
		
			$output .= '// DB' . "\n";
			$output .= 'define(\'DB_DRIVER\', \'mysql\');' . "\n";
			$output .= 'define(\'DB_HOSTNAME\', \'' . addslashes($this->request->post['db_host']) . '\');' . "\n";
			$output .= 'define(\'DB_USERNAME\', \'' . addslashes($this->request->post['db_user']) . '\');' . "\n";
			$output .= 'define(\'DB_PASSWORD\', \'' . addslashes($this->request->post['db_password']) . '\');' . "\n";
			$output .= 'define(\'DB_DATABASE\', \'' . addslashes($this->request->post['db_name']) . '\');' . "\n";
			$output .= 'define(\'DB_PREFIX\', \'' . addslashes(strtolower($this->request->post['db_prefix'])) . '\');' . "\n";
			$output .= '?>';				
		
			$file = fopen(DIR_SHOPILEX . 'config.php', 'w');
		
			fwrite($file, $output);

			fclose($file);
	 
			$output  = '<?php' . "\n";
			$output .= '// HTTP' . "\n";
			$output .= 'define(\'HTTP_SERVER\', \'' . HTTP_SHOPILEX . 'admin/\');' . "\n";
			$output .= 'define(\'HTTP_CATALOG\', \'' . HTTP_SHOPILEX . '\');' . "\n";
			$output .= 'define(\'HTTP_IMAGE\', \'' . HTTP_SHOPILEX . 'image/\');' . "\n\n";

			$output .= '// HTTPS' . "\n";
			$output .= 'define(\'HTTPS_SERVER\', \'' . HTTP_SHOPILEX . 'admin/\');' . "\n";
			$output .= 'define(\'HTTPS_IMAGE\', \'' . HTTP_SHOPILEX . 'image/\');' . "\n\n";

			$output .= '// DIR' . "\n";
		
			$output .= 'define(\'DIR_APPLICATION\', \'' . DIR_SHOPILEX . 'admin/\');' . "\n";
			$output .= 'define(\'DIR_SYSTEM\', \'' . DIR_SHOPILEX . 'system/\');' . "\n";
			$output .= 'define(\'DIR_DATABASE\', \'' . DIR_SHOPILEX . 'system/database/\');' . "\n";
			$output .= 'define(\'DIR_LANGUAGE\', \'' . DIR_SHOPILEX . 'admin/language/\');' . "\n";
			$output .= 'define(\'DIR_TEMPLATE\', \'' . DIR_SHOPILEX . 'admin/view/template/\');' . "\n";
			$output .= 'define(\'DIR_CONFIG\', \'' . DIR_SHOPILEX . 'system/config/\');' . "\n";
			$output .= 'define(\'DIR_IMAGE\', \'' . DIR_SHOPILEX . 'image/\');' . "\n";
			$output .= 'define(\'DIR_CACHE\', \'' . DIR_SHOPILEX . 'system/cache/\');' . "\n";
			$output .= 'define(\'DIR_DOWNLOAD\', \'' . DIR_SHOPILEX . 'download/\');' . "\n";
			$output .= 'define(\'DIR_LOGS\', \'' . DIR_SHOPILEX . 'system/logs/\');' . "\n";
			$output .= 'define(\'DIR_CATALOG\', \'' . DIR_SHOPILEX . 'catalog/\');' . "\n\n";

			$output .= '// DB' . "\n";
			$output .= 'define(\'DB_DRIVER\', \'mysql\');' . "\n";
			$output .= 'define(\'DB_HOSTNAME\', \'' . addslashes($this->request->post['db_host']) . '\');' . "\n";
			$output .= 'define(\'DB_USERNAME\', \'' . addslashes($this->request->post['db_user']) . '\');' . "\n";
			$output .= 'define(\'DB_PASSWORD\', \'' . addslashes($this->request->post['db_password']) . '\');' . "\n";
			$output .= 'define(\'DB_DATABASE\', \'' . addslashes($this->request->post['db_name']) . '\');' . "\n";
			$output .= 'define(\'DB_PREFIX\', \'' . addslashes(strtolower($this->request->post['db_prefix'])) . '\');' . "\n";
			$output .= '?>';	

			$file = fopen(DIR_SHOPILEX . 'admin/config.php', 'w');
		
			fwrite($file, $output);

			fclose($file);
			
			$this->redirect(HTTP_SERVER . 'index.php?route=step_4');
		}
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['error_db_host'])) {
			$this->data['error_db_host'] = $this->error['db_host'];
		} else {
			$this->data['error_db_host'] = '';
		}
		
		if (isset($this->error['db_user'])) {
			$this->data['error_db_user'] = $this->error['db_user'];
		} else {
			$this->data['error_db_user'] = '';
		}
		
		if (isset($this->error['db_name'])) {
			$this->data['error_db_name'] = $this->error['db_name'];
		} else {
			$this->data['error_db_name'] = '';
		}

		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}
		
		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}		
		
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}	
		
		$this->data['action'] = HTTP_SERVER . 'index.php?route=step_3';
		
		if (isset($this->request->post['db_host'])) {
			$this->data['db_host'] = $this->request->post['db_host'];
		} else {
			$this->data['db_host'] = 'localhost';
		}
		
		if (isset($this->request->post['db_user'])) {
			$this->data['db_user'] = html_entity_decode($this->request->post['db_user']);
		} else {
			$this->data['db_user'] = '';
		}
		
		if (isset($this->request->post['db_password'])) {
			$this->data['db_password'] = html_entity_decode($this->request->post['db_password']);
		} else {
			$this->data['db_password'] = '';
		}

		if (isset($this->request->post['db_name'])) {
			$this->data['db_name'] = html_entity_decode($this->request->post['db_name']);
		} else {
			$this->data['db_name'] = '';
		}
		
		if (isset($this->request->post['db_prefix'])) {
			$this->data['db_prefix'] = html_entity_decode($this->request->post['db_prefix']);
		} else {
			$this->data['db_prefix'] = '';
		}
		
		if (isset($this->request->post['username'])) {
			$this->data['username'] = $this->request->post['username'];
		} else {
			$this->data['username'] = 'admin';
		}

		if (isset($this->request->post['password'])) {
			$this->data['password'] = $this->request->post['password'];
		} else {
			$this->data['password'] = '';
		}
		
		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} else {
			$this->data['email'] = '';
		}
		
		$this->template = 'step_3.tpl';
		$this->children = array(
			'header',
			'footer'
		);
		
		$this->response->setOutput($this->render(TRUE));		
	}
	
	private function validate() {
		if (!$this->request->post['db_host']) {
			$this->error['db_host'] = '数据库主机必填!';
		}

		if (!$this->request->post['db_user']) {
			$this->error['db_user'] = '数据库用户名必填!';
		}

		if (!$this->request->post['db_name']) {
			$this->error['db_name'] = '数据库名字必填!';
		}
		
		if (!$this->request->post['username']) {
			$this->error['username'] = '用户名必填!';
		}

		if (!$this->request->post['password']) {
			$this->error['password'] = '密码必填!';
		}

		if ((strlen(utf8_decode($this->request->post['email'])) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
			$this->error['email'] = '邮箱地址格式不对!';
		}

		if (!$connection = @mysql_connect($this->request->post['db_host'], $this->request->post['db_user'], $this->request->post['db_password'])) {
			$this->error['warning'] = '错误: 连接不上数据库，请确认你的数据库信息或者用户/密码是否正确!';
		} else {
			if (!@mysql_select_db($this->request->post['db_name'], $connection)) {
				$this->error['warning'] = '错误: 数据库不存在!';
			}
			
			mysql_close($connection);
		}
		
		if (!is_writable(DIR_SHOPILEX . 'config.php')) {
			$this->error['warning'] = '错误: config.php 没有写入权限: ' . DIR_SHOPILEX . 'config.php!';
		}
	
		if (!is_writable(DIR_SHOPILEX . 'admin/config.php')) {
			$this->error['warning'] = '错误: config.php 没有写入权限: ' . DIR_SHOPILEX . 'admin/config.php!';
		}	
		
    	if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}		
	}
}
?>