<?php

/**
 * 阿里云OSS附件支持类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Addon
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

use \blog\libs\Common;

class Alioss_Attachment {
	protected $config;
	protected $oss;
	public function __construct($connect = TRUE) {
		$this->config = unserialize(Common::option('attachmentAlioss'));
		if (!class_exists('ALIOSS', FALSE)) {
			$root = str_replace('\\', '/', __DIR__ ) . '/';
			require ($root . 'sdk/sdk.class.php');
		}
		if ($connect) {
			$this->oss = new ALIOSS($this->config['aid'], $this->config['ak']);
		}
	}
	/**
	 * 获取From
	 * @return array
	 */
	public function getForm() {
		return [['show' => 'ACCESS_ID', 'name' => 'aid', 'type' => 'text', 'val' => $this->config['aid']], ['show' => 'ACCESS_KEY', 'name' => 'ak', 'type' => 'text', 'val' => $this->config['ak']], ['show' => 'Bucket', 'name' => 'bucket', 'type' => 'text', 'val' => $this->config['bucket']]];
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
		$response = $this->oss->upload_file_by_file($this->config['bucket'], $remote, $local);
		if (!$response->isOk()) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	/**
	 * 删除文件
	 * @access public
	 * @param string $path 路径
	 * @return boolean
	 */
	public static function delete($path) {
		$response = $this->oss->delete_object($this->config['bucket'], $path);
		return $response->isOk();
	}
}
