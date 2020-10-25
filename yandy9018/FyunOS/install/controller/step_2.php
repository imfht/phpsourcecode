<?php
class ControllerStep2 extends Controller {
	private $error = array();
	
	public function index() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->redirect(HTTP_SERVER . 'index.php?route=step_3');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';	
		}
		
		$this->data['action'] = HTTP_SERVER . 'index.php?route=step_2';

		$this->data['config_catalog'] = DIR_SHOPILEX . 'config.php';
		$this->data['config_admin'] = DIR_SHOPILEX . 'admin/config.php';
		
		$this->data['cache'] = DIR_SYSTEM . 'cache';
		$this->data['logs'] = DIR_SYSTEM . 'logs';
		$this->data['image'] = DIR_SHOPILEX . 'image';
		$this->data['image_cache'] = DIR_SHOPILEX . 'image/cache';
		$this->data['image_data'] = DIR_SHOPILEX . 'image/data';
		$this->data['download'] = DIR_SHOPILEX . 'download';
		
		$this->template = 'step_2.tpl';

		$this->children = array(
			'header',
			'footer'
		);		
		$this->response->setOutput($this->render(TRUE));
	}
	
	private function validate() {
		if (phpversion() < '5.0') {
			$this->error['warning'] = '警告: 运作Shopilex需要 PHP5 以上!';
		}

		if (!ini_get('file_uploads')) {
			$this->error['warning'] = '警告: 文件上传支持需要打开!';
		}
	
		if (ini_get('session.auto_start')) {
			$this->error['warning'] = '警告: 运作Shopilex需要设置 session.auto_start 为 enabled!';
		}

		if (!extension_loaded('mysql')) {
			$this->error['warning'] = '警告: 需要安装MYSQL的扩展才能运行Shopilex!';
		}

		if (!extension_loaded('gd')) {
			$this->error['warning'] = '警告: 需要安装GD的扩展,才能运行Shopilex!';
		}

		if (!extension_loaded('zlib')) {
			$this->error['warning'] = '警告: 需要安装ZLIB的扩展,才能运行Shopilex!';
		}
	
		if (!is_writable(DIR_SHOPILEX . 'config.php')) {
			$this->error['warning'] = '警告: config.php 需要写入权限!';
		}
				
		if (!is_writable(DIR_SHOPILEX . 'admin/config.php')) {
			$this->error['warning'] = '警告: admin/config.php 需要写入权限!';
		}

		if (!is_writable(DIR_SYSTEM . 'cache')) {
			$this->error['warning'] = '警告: Cache 目录需要写入权限!';
		}
		
		if (!is_writable(DIR_SYSTEM . 'logs')) {
			$this->error['warning'] = '警告: Logs 需要写入权限!';
		}
		
		

	
		
		
		
    	if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}
	}
}
?>