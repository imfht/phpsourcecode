<?php

namespace app\common;

use \think\Db;

class Upload
{

	/**
	 * 全局上传方法
	 * Upload::index()
	 */

	public static function index($field = 'file', $domain = null, $validate = [])
	{
		if(empty($field)) {
			$field = 'file';
		}
		if(!$domain) {
			$url_path = url('/');
		} else {
			$url_path = url('/', null, null, $domain);
		}
		$php_path = 'uploads/' . gsdate('Y') . '/' . gsdate('m') . '/' . gsdate('d') . '/';
		$file = request()->file($field);
		if(empty($file)) {
			return make_return(0, 'Field not Flound');
		}
		$file_info = $file->getInfo();
		/* md5
		$file_md5 = hash_file('md5', $file_info['tmp_name']);
		$value = model('Uploads')->get_one(['file_md5' => $file_md5, 'agent_id' => 0, 'merchant_id' => 0]);
		if($value) {
			$res_url = $url_path . preg_replace('/[\/\\\\]{1,}/', '/', $value['save_name']);
			$file_info = [
				'name' => $value['file_name'],
				'size' => $value['file_size'],
				'ext' => $value['extension'],
				'FileName' => basename($value['save_name']),
				'SaveName' => preg_replace('/[\/\\\\]{1,}/', '/', $value['save_name']),
			];
			return make_return(1, $res_url, $file_info);
		}
		*/
		if(empty($validate)) {
			$info = $file->rule('\app\common\named_upload')->move(_ROOT_ . $php_path);
		} else {
			$info = $file->rule('\app\common\named_upload')->validate($validate)->move(_ROOT_ . $php_path);
		}
		if(!$info) {
			return make_return(0, $file->getError());
		} else {
			$FileName = $info->getFilename();
			$SaveName = $php_path . preg_replace('/[\/\\\\]{1,}/', '/', $info->getSaveName());
			$res_url = $url_path . $SaveName;
			$file_info = $info->getInfo();
			ksort($file_info);
			unset($file_info['error']);
			unset($file_info['tmp_name']);
			$file_info['ext'] = $info->getExtension();
			$file_info['FileName'] = $FileName;
			$file_info['SaveName'] = $SaveName;
			/* md5
			model('Uploads')->allowField(true)->save([
				'agent_id' => 0,
				'merchant_id' => 0,
				'file_md5' => $file_md5,
				'file_name' => $file_info['name'],
				'save_name' => $SaveName,
				'file_size' => $file_info['size'],
				'file_type' => $file_info['type'],
				'extension' => $info->getExtension(),
				'is_image' => preg_match('/^image\//', $file_info['type']) ? 1 : 0,
				'time_create' => _time()
			]);
			*/
			return make_return(1, $res_url, $file_info);
		}
	}

	/**
	 * 代理上传方法
	 * Upload::agent()
	 */

	public static function agent($agent, $field = 'file', $domain = null, $validate = [])
	{
		if(empty($agent['agent_id']) || empty($agent['agent_no'])) {
			return make_return(1, "缺少参数");
		}
		if(empty($field)) {
			$field = 'file';
		}
		if(!$domain) {
			$url_path = url('/');
		} else {
			$url_path = url('/', null, null, $domain);
		}
		$php_path = 'uploads/agent/' . $agent['agent_no'] . '/' . gsdate('Y') . '/' . gsdate('m') . '/';
		$file = request()->file($field);
		if(empty($file)) {
			return make_return(0, 'Field not Flound');
		}
		$file_info = $file->getInfo();
		/* md5
		$file_md5 = hash_file('md5', $file_info['tmp_name']);
		$value = model('Uploads')->get_one(['file_md5' => $file_md5, 'agent_id' => $agent['agent_id']]);
		if($value) {
			$res_url = $url_path . preg_replace('/[\/\\\\]{1,}/', '/', $value['save_name']);
			$file_info = [
				'name' => $value['file_name'],
				'size' => $value['file_size'],
				'ext' => $value['extension'],
				'FileName' => basename($value['save_name']),
				'SaveName' => preg_replace('/[\/\\\\]{1,}/', '/', $value['save_name']),
			];
			return make_return(1, $res_url, $file_info);
		}
		*/
		if(empty($validate)) {
			$info = $file->rule('\app\common\named_upload_agent')->move(_ROOT_ . $php_path);
		} else {
			$info = $file->rule('\app\common\named_upload_agent')->validate($validate)->move(_ROOT_ . $php_path);
		}
		if(!$info) {
			return make_return(0, $file->getError());
		} else {
			$FileName = $info->getFilename();
			$SaveName = $php_path . preg_replace('/[\/\\\\]{1,}/', '/', $info->getSaveName());
			$res_url = $url_path . $SaveName;
			$file_info = $info->getInfo();
			ksort($file_info);
			unset($file_info['error']);
			unset($file_info['tmp_name']);
			$file_info['ext'] = $info->getExtension();
			$file_info['FileName'] = $FileName;
			$file_info['SaveName'] = $SaveName;
			/* md5
			model('Uploads')->allowField(true)->save([
				'agent_id' => $agent['agent_id'],
				'merchant_id' => 0,
				'file_md5' => $file_md5,
				'file_name' => $file_info['name'],
				'save_name' => $SaveName,
				'file_size' => $file_info['size'],
				'file_type' => $file_info['type'],
				'extension' => $info->getExtension(),
				'is_image' => preg_match('/^image\//', $file_info['type']) ? 1 : 0,
				'time_create' => _time()
			]);
			*/
			return make_return(1, $res_url, $file_info);
		}
	}

	/**
	 * 商户上传方法
	 * Upload::merchant()
	 */

	public static function merchant($merchant, $field = 'file', $domain = null, $validate = [])
	{
		if(empty($merchant['merchant_id']) || empty($merchant['merchant_no'])) {
			return make_return(1, "缺少参数");
		}
		if(empty($field)) {
			$field = 'file';
		}
		if(!$domain) {
			$url_path = url('/');
		} else {
			$url_path = url('/', null, null, $domain);
		}
		$php_path = 'uploads/merchant/' . $merchant['merchant_no'] . '/' . gsdate('Y') . '/' . gsdate('m') . '/';
		$file = request()->file($field);
		if(empty($file)) {
			return make_return(0, 'Field not Flound');
		}
		$file_info = $file->getInfo();
		/* md5
		$file_md5 = hash_file('md5', $file_info['tmp_name']);
		$value = model('Uploads')->get_one(['file_md5' => $file_md5, 'merchant_id' => $merchant['merchant_id']]);
		if($value) {
			$res_url = $url_path . preg_replace('/[\/\\\\]{1,}/', '/', $value['save_name']);
			$file_info = [
				'name' => $value['file_name'],
				'size' => $value['file_size'],
				'ext' => $value['extension'],
				'FileName' => basename($value['save_name']),
				'SaveName' => preg_replace('/[\/\\\\]{1,}/', '/', $value['save_name']),
			];
			return make_return(1, $res_url, $file_info);
		}
		*/
		if(empty($validate)) {
			$info = $file->rule('\app\common\named_upload_merchant')->move(_ROOT_ . $php_path);
		} else {
			$info = $file->rule('\app\common\named_upload_merchant')->validate($validate)->move(_ROOT_ . $php_path);
		}
		if(!$info) {
			return make_return(0, $file->getError());
		} else {
			$FileName = $info->getFilename();
			$SaveName = $php_path . preg_replace('/[\/\\\\]{1,}/', '/', $info->getSaveName());
			$res_url = $url_path . $SaveName;
			$file_info = $info->getInfo();
			ksort($file_info);
			unset($file_info['error']);
			unset($file_info['tmp_name']);
			$file_info['ext'] = $info->getExtension();
			$file_info['FileName'] = $FileName;
			$file_info['SaveName'] = $SaveName;
			/* md5
			model('Uploads')->allowField(true)->save([
				'agent_id' => 0,
				'merchant_id' => $merchant['merchant_id'],
				'file_md5' => $file_md5,
				'file_name' => $file_info['name'],
				'save_name' => $SaveName,
				'file_size' => $file_info['size'],
				'file_type' => $file_info['type'],
				'extension' => $info->getExtension(),
				'is_image' => preg_match('/^image\//', $file_info['type']) ? 1 : 0,
				'time_create' => _time()
			]);
			*/
			return make_return(1, $res_url, $file_info);
		}
	}

}


/**
 * 全局上传命名
 * param string $prefix
 */

function named_upload($prefix = '') {
	list($microtime, $timestamp) = explode(' ', microtime());
	return ToString($prefix . gsdate('His', _time()) . substr($microtime, 2, 6));
}


/**
 * 代理上传命名
 * param string $prefix
 */

function named_upload_agent($prefix = '') {
	$prefix = $prefix ? $prefix : gsdate('d');
	list($microtime, $timestamp) = explode(' ', microtime());
	return ToString($prefix . gsdate('His', _time()) . substr($microtime, 2, 6));
}


/**
 * 商户上传命名
 * param string $prefix
 */

function named_upload_merchant($prefix = '') {
	$prefix = $prefix ? $prefix : gsdate('d');
	list($microtime, $timestamp) = explode(' ', microtime());
	return ToString($prefix . gsdate('His', _time()) . substr($microtime, 2, 6));
}

