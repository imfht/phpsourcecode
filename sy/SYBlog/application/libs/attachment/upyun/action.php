<?php

/**
 * Typecho又拍云文件管理
 * 
 * @package UpyunFile
 * @author codesee
 * @version 0.6.0
 * @link http://pengzhiyong.com
 * @date 2014-01-15
 */

use \blog\libs\Common;

class Upyun_Attachment {
	protected $config;
	protected $upyun;
	public function __construct($connect = TRUE) {
		$this->config = unserialize(Common::option('attachmentUpyun'));
		if (!class_exists('UpYun', FALSE)) {
			$root = str_replace('\\', '/', __DIR__) . '/';
			require($root . 'upyun.class.php');
		}
		if ($connect) {
			$this->upyun = new UpYun($this->config['host'], $this->config['user'], $this->config['password']);
		}
	}
	/**
	 * 获取From
	 * @return array
	 */
	public function getForm() {
		return [
			['show' => '空间名', 'name' => 'host', 'type' => 'text', 'val' => $this->config['host']],
			['show' => '用户名', 'name' => 'user', 'type' => 'text', 'val' => $this->config['user']],
			['show' => '密码', 'name' => 'password', 'type' => 'text', 'val' => $this->config['password']]
		];
	}
	/**
	 * 设置
	 * @access public
	 */
	public function config($config) {
		Common::option('attachmentUpyun', serialize($config));
	}
	/**
	 * 上传
	 * @access public
	 * @param string $local 本地文件
	 * @param string $remote 远程路径
	 * @return boolean
	 */
	public function upload($local, $remote) {
		//上传文件
		$fh = fopen($local, 'r');
		if (substr($remote, 0, 1) !== '/') {
			$remote = '/' . $remote;
		}
		$this->upyun->writeFile($remote, $fh, TRUE);
		fclose($fh);
	}
	/**
	 * 删除文件
	 * @access public
	 * @param string $path 路径
	 * @return boolean
	 */
	public function delete($path) {
		return $this->upyun->delete($path);
	}
}
