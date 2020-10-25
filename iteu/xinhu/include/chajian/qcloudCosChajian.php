<?php 
/**
*	腾讯云上文件存储上传管理
*/
include_once(ROOT_PATH.'/\include/cos-php-sdk-v4-master/include.php');
use qcloudcos\Cosapi;
class qcloudCosChajian extends Chajian{
	
	
	
	protected function initChajian()
	{
		Cosapi::initConf(); //初始设置
	}
	
	/**
	*	上传文件
	*	filepath 要上传的文件全路径
	*	updir 上传到哪个目录
	*	upname 上传后保存文件名
	*/
	public function upload($filepath, $updir='', $upname='')
	{
		if(!file_exists($filepath))return false;
		$filea 	= explode('/', $filepath);
		if($upname=='')$upname = $filea[count($filea)-1];
		if($updir=='')$updir = 'rockxinhuweb';
		$ret = Cosapi::upload('xinhu', $filepath, ''.$updir.'/'.$upname.'');
		return $ret;
	}
	
	/**
	*	创建文件夹
	*/
	public function createFolder($folder)
	{
		$ret = Cosapi::createFolder($bucket, $folder);
		return $ret;
	}
}