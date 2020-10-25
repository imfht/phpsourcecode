<?php
require_once('uploader.php');
class ControllerCommonUpload extends Controller {
	public function index() {
		//Upload
		$allowedExtensions = array();
		// max file size in bytes
		$sizeLimit = 1024*1024*10;
		$uploader = new qqFileUploader($this->registry,$allowedExtensions, $sizeLimit);
	
		$directory = rtrim(DIR_IMAGE . 'data/'.$this->request->get['directory'].'/');
	
		$result = $uploader->handleUpload($directory);
		
		$filename=$uploader->filename;
		$code=array(
			'id' => 'data/'.$filename,
			'filename' => $filename,
		);
		$this->load->library('json');
		$this->response->setOutput(Json::encode(array_merge($code,$result)));

	}
}
?>