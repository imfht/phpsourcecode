<?php 
namespace Ysf;
class Uploader {
	private $error='';
	public function __construct($config=[]) {
		if (empty($config)) {
			$this->config = config('upload');
		}
	}
	
	public function upload($name='')
	{
		if ($name) {
			if (isset($_FILES[$name])) {
				return $this->_upload($name);
			}else{
				return false;
			}
		}else{
			if (isset($_FILES) && !empty($_FILES)) {
				$output = [];
				foreach ($_FILES as $k => $file) {
					$output[$k] = $this->_upload($k);
				}
				return $output;
			}else{
				$this->error = '';
				return false;
			}
		}	
	}

	private function checkExt($ext){
		return in_array(strtolower($ext), $this->config['allow_ext']);
	}

	private function _upload($name){
		if ($_FILES[$name]['size']>$this->config['filesize']) {
			$this->error[$name] = 'too large size';
			return false;
		}
		$ext = pathinfo($_FILES[$name]['name'])['extension'];
		if ($this->checkExt($ext)==false) {
			$this->error[$name] = 'ext is not allow';
			return false;
		}
		$savepath = $this->savepath($_FILES[$name]['tmp_name'],$ext);
		try {
			move_uploaded_file($_FILES[$name]['tmp_name'], PUBLIC_PATH . $savepath);
		} catch (Exception $e) {
			$this->error[$name] = $e->getMessage();
			return false;
		}
		return $savepath;
	}

	public function getError($name=''){
		return empty($name) ? $this->error : $this->error[$name];
	}

	private function savepath($file_name,$ext){
		$savepath = $this->config['savepath'] . '/' . date('Ymd') . '/'. sha1_file($file_name) . '.' .$ext;
		if (!is_dir( PUBLIC_PATH . pathinfo($savepath)['dirname'])) {
			mkdir( PUBLIC_PATH . pathinfo($savepath)['dirname'],750,true);
		}
		return $savepath;
	}
}