<?php
namespace Org\Convert;

class Core
{
	private $handle;
	public function __construct()
	{
		$type = C('wkcms_convert_type');
		global $userinfo;
		$config = array();
		$config['uid'] = $userinfo['uid'];
		$config['site'] = C('wkcms_convert_site_' . $type);
		$config['appid'] = C('wkcms_convert_appid_' . $type);
		$config['appsecret'] = C('wkcms_convert_appsecret_' . $type);

		switch ($type) {
			case 2:
				import("@.ORG.convert.Baidu");
				$this->handle = new Baidu($config);
				break;
			
			default:
				import("@.ORG.convert.Dever");
				$this->handle = new Dever($config);
				break;
		}
	}

	/**
	 * 上传文件并转换文档
	 */
	public function upload($data)
	{
		if ($data['score'] > 0) {
			$file_type = 1;
		} else {
			$file_type = 2;
		}
		$id = $data['id'];
		$file = C('wkcms_site_url') . 'data/upload/doc_con/' . $data['fileurl'];
		$local = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . C('wkcms_attach_path') . 'doc_con/' . $data['fileurl'];
        $file = $dir . $file;
		$title = $data['title'];
		$format = $data['ext'];
		return $this->handle->upload($file, $id, $file_type, $title, $format, $local);
	}

	/**
	 * 文件购买授权，用户购买后，可以调用这个方法，授权给文档转换工具
	 */
	public function auth($data)
	{
		if (!$data['convert_key']) {
			return '';
		}
		$key = $data['convert_key'];
		$id = $data['id'];
		return $this->handle->auth($key, $id);
	}

	/**
	 * 下载文件
	 */
	public function download($data)
	{
		if (!$data['convert_key']) {
			return '';
		}
		$key = $data['convert_key'];
		$id = $data['id'];
		return $this->handle->download($key, $id);
	}

	/**
	 * 预览文件
	 */
	public function img($url)
	{
        return $this->handle->img($url);
	}


	/**
	 * 查看文件
	 */
	public function get($data, $return_url = false)
	{
		if (!$data['convert_key']) {
			return '';
		}
		if ($data['score'] > 0) {
			$file_type = 1;
		} else {
			$file_type = 2;
		}
		$host = $_SERVER['HTTP_HOST'];
		$key = $data['convert_key'];
		$url = $data['viewurl'];
		$img = $data['imgurl'];
		$id = $data['id'];
		return $this->handle->get($key, $id, $url, $img, $host, $file_type, $return_url);
	}

	public function signature($appid, $appsecret, $timestamp, $nonce, $file, $file_id, $uid)
	{
		return $this->handle->signature($appid, $appsecret, $timestamp, $nonce, $file, $file_id, $uid);
	}
}