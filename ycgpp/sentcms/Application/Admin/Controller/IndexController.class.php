<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;

class IndexController extends \Common\Controller\AdminController {
	
	public function index(){
		$this->setMeta('系统信息面板');
		$this->display();
	}

	public function clear(){
		$dirs	=	array(RUNTIME_PATH);
		@mkdir(RUNTIME_PATH,0777,true);
		foreach($dirs as $value) {
			$this->rmdirr($value);
		}
		$this->success('已经被删除!缓存清理完毕。',U('Index/index'));
	}

	protected function rmdirr($dirname) {
		if (!file_exists($dirname)) {
			return false;
		}
		if (is_file($dirname) || is_link($dirname)) {
			return unlink($dirname);
		}
		$dir = dir($dirname);
		if($dir){
			while (false !== $entry = $dir->read()) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				$this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
			}
		}
		$dir->close();
		return rmdir($dirname);
	}

}