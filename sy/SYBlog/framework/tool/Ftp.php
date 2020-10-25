<?php

/**
 * FTP支持类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Tool
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\tool;
use Sy;
use \sy\base\SYException;

class YFtp {
	protected $config;
	protected $link = NULL;
	/**
	 * 构造函数，自动连接
	 * @access public
	 * @param array $config FTP选项
	 */
	public function __construct($config) {
		if (!function_exists('ftp_connect')) {
			throw new SYException('Ext "FTP" is required', '10023');
		}
		if (!isset($config['port'])) {
			$config['port'] = 21;
		}
		//默认为被动模式
		if (!isset($config['pasv'])) {
			$config['pasv'] = TRUE;
		}
		if (FALSE === ($this->link = ftp_connect($config['host'], $config['port']))) {
			throw new SYException('Can not connect to FTP Server', '10040');
		}
		//登录
		if (isset($config['user'])) {
			if (!ftp_login($this->link, $config['user'], $config['password'])) {
				throw new SYException('Can not login to FTP Server', '10041');
			}
		} else {
			if (!ftp_login($this->link, 'anonymous', '')) {
				throw new SYException('Can not login to FTP Server as anonymous', '10041');
			}
		}
		if ($config['pasv']) {
			ftp_pasv($this->link, TRUE);
		}
		$this->config = $config;
	}
	/**
	 * 切换目录
	 * @access public
	 * @param string $dir
	 * @param boolean $auto_create 是否自动创建
	 * @return boolean
	 */
	public function chdir($dir, $auto_create = FALSE) {
		if (@ftp_chdir($this->link, $dir) === FALSE) {
			if ($auto_create) {
				$this->mkdir($dir);
				if (@ftp_chdir($this->link, $dir) === FALSE) {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		}
		return TRUE;
	}
	/**
	 * 获取当前目录
	 * @access public
	 * @return string
	 */
	public function getdir() {
		return ftp_pwd($this->link);
	}
	/**
	 * 创建文件夹
	 * @access public
	 * @param string $dir
	 * @param string $permissions 权限
	 * @return	boolean
	 */
	public function mkdir($dir, $permissions = '0755') {
		if (empty($dir)) {
			return FALSE;
		}
		if (@ftp_mkdir($this->link, $dir) === FALSE) {
			return FALSE;
		}

		if ($permissions !== NULL) {
			$this->chmod($dir, $permissions);
		}
		return TRUE;
	}
	/**
	 * 上传文件
	 * @access public
	 * @param string $from 本地文件
	 * @param string $to 目标文件
	 * @param string $mode 传输模式
	 * @param string $permissions 权限
	 * @return boolean
	 */
	public function upload($from, $to, $mode = 'auto', $permissions = NULL) {
		if (!is_file($from)) {
			return FALSE;
		}
		//自动模式
		if ($mode === 'auto') {
			$ext = $this->getExt($from);
			$mode = $this->getType($ext);
		}
		$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;
		if (@ftp_put($this->link, $to, $from, $mode) === FALSE) {
			return FALSE;
		}
		if ($permissions !== NULL) {
			$this->chmod($to, $permissions);
		}
		return TRUE;
	}
	/**
	 * 下载文件
	 * @access public
	 * @param string $from 远程文件
	 * @param string $to 本地文件
	 * @param string $mode 传输模式
	 * @return boolean
	 */
	public function download($from, $to, $mode = 'auto') {
		//自动模式
		if ($mode === 'auto') {
			$ext = $this->getExt($from);
			$mode = $this->getType($ext);
		}
		$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;
		if (@ftp_get($this->link, $to, $from, $mode) === FALSE) {
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 重命名/移动一个文件
	 * @access public
	 * @param string $old
	 * @param string $new
	 * @return boolean
	 */
	public function rename($old, $new) {
		if (@ftp_rename($this->link, $old, $new) === FALSE) {
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 删除文件
	 * @access public
	 * @param string $path
	 * @return boolean
	 */
	public function del($path) {
		if (@ftp_delete($this->link, $path) === FALSE) {
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 文件信息
	 * @access public
	 * @param string $path
	 * @return array
	 */
	public function fileinfo($path) {
		return [
			'size' => @ftp_size($this->link, $path),
			'modify' => @ftp_mdtm($this->link, $path)
		];
	}
	/**
	 * 删除文件夹
	 * @access public
	 * @param string $path
	 * @return	boolean
	 */
	public function rmdir($path) {
		$path = rtrim($path, '/') . '/';
		$list = $this->ls($path);
		if (!empty($list)) {
			for ($i = 0, $c = count($list); $i < $c; $i++) {
				if (!preg_match('#/\.\.?$#', $list[$i]) && !ftp_delete($this->link, $list[$i])) {
					$this->rmdir($list[$i]);
				}
			}
		}
		if (@ftp_rmdir($this->link, $filepath) === FALSE) {
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 设置权限
	 * @access public
	 * @param string $path	
	 * @param string $permissions
	 * @return	boolean
	 */
	public function chmod($path, $permissions) {
		if (@ftp_chmod($this->link, $permissions, $path) === FALSE) {
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 列出指定目录的文件
	 * @access public
	 * @param string $path
	 * @return array
	 */
	public function ls($path = '.') {
		return @ftp_nlist($this->link, $path);
	}
	/**
	 * 执行命令
	 * @access public
	 * @param string $command
	 * @return array
	 */
	public function exec($command) {
		return @ftp_raw($this->link, $command);
	}
	/**
	 * 获取文件扩展名
	 * @access protected
	 * @param string $filename
	 * @return	string
	 */
	protected function getExt($filename) {
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		return (empty($ext) ? 'txt' : $ext);
	}
	/**
	 * 获取FTP传输类型
	 * @access protected
	 * @param string $ext
	 * @return	string
	 */
	protected function getType($ext) {
		if (in_array($ext, ['txt', 'text', 'php', 'phps', 'php4', 'js', 'css', 'htm', 'html', 'phtml', 'shtml', 'log', 'xml', 'asp', 'jsp'], TRUE)) {
			return 'ascii';
		} else {
			return 'binary';
		}
	}
	/**
	 * 析构函数，自动断开
	 * @access public
	 */
	public function __destruct() {
		if (is_resource($this->link)) {
			@ftp_close($this->link);
			$this->link = NULL;
		}
	}
}
