<?php

/**
 * 七牛附件支持类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Addon
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

use \blog\libs\Common;

class Qiniu_Attachment {
	protected $ak;
	protected $sk;
	protected $bucket;
	protected $root;
	public function __construct($connect = TRUE) {
		$config = unserialize(Common::option('attachmentQiniu'));
		$this->ak = $config['ak'];
		$this->sk = $config['sk'];
		$this->bucket = $config['bucket'];
		$this->root = str_replace('\\', '/', __DIR__) . '/';
		if (!function_exists('Qiniu_Put')) {
			require($this->root . 'sdk/io.php');
			require($this->root . 'sdk/rs.php');
		}
		if ($connect) {
			Qiniu_SetKeys($this->ak, $this->sk);
		}
	}
	/**
	 * 获取From
	 * @return array
	 */
	public function getForm() {
		return [
			['show' => 'AccessKey', 'name' => 'ak', 'type' => 'text', 'val' => $this->ak],
			['show' => 'SecretKey', 'name' => 'sk', 'type' => 'text', 'val' => $this->sk],
			['show' => 'Bucket', 'name' => 'bucket', 'type' => 'text', 'val' => $this->bucket]
		];
	}
	/**
	 * 设置
	 * @access public
	 */
	public function config($config) {
		Common::option('attachmentQiniu', serialize($config));
	}
	/**
	 * 删除
	 * @access public
	 * @param string $path 路径
	 * @return boolean
	 */
	public function delete($path) {
		$client = new Qiniu_MacHttpClient(NULL);
		return Qiniu_RS_Delete($client, $this->bucket, $path);
	}
	/**
	 * 上传
	 * @access public
	 * @param string $local 本地文件
	 * @param string $remote 远程路径
	 * @return boolean
	 */
	public function upload($local, $remote) {
		$remote = trim($remote, '/');
		// 上传凭证
		$policy = new Qiniu_RS_PutPolicy($this->bucket);
		$token = $policy->Token(null);
		$extra = new Qiniu_PutExtra();
		$extra->Crc32 = 1;
		// 上传
		list($result, $error) = Qiniu_PutFile($token, $remote, $local, $extra);
		if ($error == NULL) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
