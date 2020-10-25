<?php

/**
 * 阿里顽兔附件支持类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Addon
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

use \blog\libs\Common;

class Aliwantu_Attachment {
	protected $config;
	protected $wantu;
	public function __construct($connect = TRUE) {
		$this->config = unserialize(Common::option('attachmentAliwantu'));
		if (!class_exists('\\AlibabaImage')) {
			require(__DIR__ . '/sdk/alimage.class.php');
		}
		if ($connect) {
			$this->wantu = new \AlibabaImage($this->config['ak'], $this->config['sk']);
		}
	}
	/**
	 * 获取From
	 * @return array
	 */
	public function getForm() {
		return [
			['show' => 'AccessKey', 'name' => 'ak', 'type' => 'text', 'val' => $this->config['ak']],
			['show' => 'SecretKey', 'name' => 'sk', 'type' => 'text', 'val' => $this->config['sk']],
			['show' => 'namespace', 'name' => 'bucket', 'type' => 'text', 'val' => $this->config['bucket']]
		];
	}
	/**
	 * 设置
	 * @access public
	 */
	public function config($config) {
		Common::option('attachmentAliwantu', serialize($config));
	}
	/**
	 * 建立文件夹
	 * @access protected
	 */
	protected function mkdirs($dir) {
		if ($this->wantu->existsFolder($this->config['bucket'], $dir)) {
			return;
		}
		$updir = substr($dir, 0, strrpos($dir, '/'));
		if (!$this->wantu->existsFolder($this->config['bucket'], $updir)) {
			$this->mkdirs($updir);
		}
		$this->wantu->createDir($this->config['bucket'], $dir);
	}
	/**
	 * 删除
	 * @access public
	 * @param string $path 路径
	 * @return boolean
	 */
	public function delete($path) {
		if (substr($path, 0, 1) !== '/') {
			$path = '/' . $path;
		}
		$info = pathinfo($path);
		//删除
		$rs = $this->wantu->deleteFile($this->config['bucket'], $info['dirname'], $info['basename']);
		if ($rs['isSuccess']) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/**
	 * 上传
	 * @access public
	 * @param string $local 本地文件
	 * @param string $remote 远程路径
	 * @return boolean
	 */
	public function upload($local, $remote) {
		if (substr($remote, 0, 1) !== '/') {
			$remote = '/' . $remote;
		}
		$info = pathinfo($remote);
		$this->mkdirs($info['dirname']);
		$uploadPolicy = new \UploadPolicy($this->config['bucket']);
		$uploadOption = new \UploadOption();
		$uploadOption->dir = $info['dirname'];
		$uploadOption->name = $info['basename'];
		$rs = $this->wantu->upload($local, $uploadPolicy, $uploadOption);
		if ($rs['isSuccess']) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
