<?php

/**
 * FTP附件支持类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Addon
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

use \sy\tool\YFtp;
use \blog\libs\Common;

class Ftp_Attachment {
	protected $config;
	protected $ftp;
	public function __construct($connect = TRUE) {
		$this->config = unserialize(Common::option('attachmentFtp'));
		$this->config['pasv'] = ($this->config['mode'] == 1 ? TRUE : FALSE);
		if ($connect) {
			$this->ftp = new YFtp($this->config);
		}
	}
	/**
	 * 获取From
	 * @return array
	 */
	public function getForm() {
		$config = unserialize(Common::option('attachmentQiniu'));
		return [
			['show' => '地址', 'name' => 'host', 'type' => 'text', 'val' => $this->config['host']],
			['show' => '端口', 'name' => 'port', 'type' => 'text', 'val' => $this->config['port']],
			['show' => '用户名', 'name' => 'user', 'type' => 'text', 'val' => $this->config['user']],
			['show' => '密码', 'name' => 'password', 'type' => 'text', 'val' => $this->config['password']],
			['show' => '根目录', 'name' => 'basedir', 'placeholder' => '默认目录请填“.”', 'type' => 'text', 'val' => $this->config['basedir']],
			['show' => '被动模式', 'name' => 'pasv', 'type' => 'checkbox', 'checked' => ($this->config['mode'] == 1 ? TRUE : FALSE), 'val' => 1]
		];
	}
	/**
	 * 设置
	 * @access public
	 */
	public function config($config) {
		$config['mode'] = isset($config['pasv']) ? 1 : 0;
		unset($config['pasv']);
		Common::option('attachmentFtp', serialize($config));
	}
	/**
	 * 删除
	 * @access public
	 * @param string $path 路径
	 * @return boolean
	 */
	public function delete($path) {
		return $this->ftp->del($path);
	}
	/**
	 * 上传
	 * @access public
	 * @param string $local 本地文件
	 * @param string $remote 远程路径
	 * @return boolean
	 */
	public function upload($local, $remote) {
		$remote = rtrim($this->config['basedir'], '/') . '/' . $remote;
		$info = pathinfo($remote);
		$this->ftp->chdir($info['dirname'], TRUE);
		return $this->ftp->upload($local, $info['basename']);
	}
}
