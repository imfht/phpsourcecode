<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

class FileController extends \Common\Controller\AdminController {
	
	public function upload() {
		$filename = I('get.filename', 'images', 'trim');
		
		$this->$filename();
	}
	
	public function attach() {
		$return = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload($_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));
		/* 记录附件信息 */
		if ($info) {
			$return['data'] = think_encrypt(json_encode($info['file']));
			$return['info'] = $info['file'];
		} 
		else {
			$return['status'] = 0;
			$return['info'] = $File->getError();
		}
		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}
	
	public function images() {
		/* 返回标准数据 */
		$return = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$Picture = D('Picture');
		$pic_driver = C('PICTURE_UPLOAD_DRIVER');
		$info = $Picture->upload($_FILES, C('PICTURE_UPLOAD'), C('PICTURE_UPLOAD_DRIVER'), C("UPLOAD_{$pic_driver}_CONFIG")); //TODO:上传到远程服务器
		
		/* 记录图片信息 */
		if ($info) {
			$return['status'] = 1;
			$return['info'] = $info['file'];
		} 
		else {
			$return['status'] = 0;
			$return['info'] = $Picture->getError();
		}
		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}
	
	public function delete() {
		$return = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}
	//ueditor编辑器上传图片处理
	public function ueditor() {
		$data = new \OT\Ueditor(UID);
		echo $data->output();
	}
}
