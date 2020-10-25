<?php

namespace app\merchant\controller;

use \think\Db;
use \think\Session;

require_once EXTEND_PATH . 'sdk/JSON.php';

class Bfeditor
{

	public $merchant;
	public $php_path;
	public $url_path;
	public $order;
	public $ext_arr;

	public function __construct($domain = false)
	{
		$root_path = 'uploads/merchant/';
		$this->merchant = model('Merchant')->getLoginMerchant();
		$this->php_path = '';
		$this->url_path = '';
		if($this->merchant) {
			$this->php_path = _ROOT_ . $root_path . $this->merchant['merchant_no'];
		}
		if(!$domain) {
			$this->url_path = url('/');
		} else {
			$this->url_path = url('/', null, null, $domain);
		}
		if($this->merchant) {
			$this->url_path = $this->url_path . $root_path . $this->merchant['merchant_no'] . '/';
		}
		if($this->merchant && !is_dir($this->php_path)) {
			mkdir($this->php_path, 0777, TRUE);
		}
		$this->order = empty(input('param.order')) ? 'name' : strtolower(input('param.order'));
		$this->ext_arr = array(
			'image' => array('png', 'gif', 'jpg', 'jpeg'),
			'file' => array('zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'),
		);
	}

	public function upload()
	{
		if(!$this->merchant) {
			return make_json(0, '登录超时');
		}
		$res = \app\common\Upload::merchant($this->merchant, 'imgFile');
		$json = new \Services_JSON();
		if($res['status'] == 1) {
			echo $json->encode(['error' => 0, 'url' => $res['message']]);
		} else {
			echo $json->encode(['error' => 1, 'message' => $res['message']]);
		}
	}

	public function manager()
	{
		if(!$this->merchant) {
			return make_json(0, '登录超时');
		}
		$dir = empty(input('param.dir')) ? '' : input('param.dir');
		$path = empty(input('param.path')) ? '' : input('param.path');
		if(empty($path)) {
			$current_path = '/';
			$current_url = $this->url_path . '';
			$current_dir_path = '';
			$moveup_dir_path = '';
		} else {
			$current_path = '/' . $path;
			$current_url = $this->url_path . $path;
			$current_dir_path = $path;
			$moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
		}
		if(preg_match('/\.\./', $current_path)) {
			return make_json(0, '参数无效');
		}
		if(!preg_match('/\/$/', $current_path)) {
			return make_json(0, '参数无效');
		}
		if(!is_dir($this->php_path . $current_path)) {
			return make_json(0, '目录不存在');
		}
		$list = _dir($this->php_path . $current_path);
		$file_list = array();
		$i = 0;
		foreach($list as $key => $val) {
			$filename = basename($val['name']);
			if($val['type'] == 'dir') {
				$file_list[$i]['is_dir'] = true;
				if(_dir($val['name'])) {
					$file_list[$i]['has_file'] = true;
				} else {
					$file_list[$i]['has_file'] = false;
				}
				$file_list[$i]['dir_path'] = '';
				$file_list[$i]['filesize'] = 0;
				$file_list[$i]['is_photo'] = false;
				$file_list[$i]['filetype'] = '';
				$file_list[$i]['filename'] = $filename;
				$file_list[$i]['datetime'] = gsdate('Y-m-d H:i:s', filemtime($val['name']));
				$i++;
			} else {
				$file_ext = get_ext($val['name']);
				if(empty($dir) || in_array($file_ext, $this->ext_arr[$dir])) {
					$file_list[$i]['is_dir'] = false;
					$file_list[$i]['has_file'] = false;
					$file_list[$i]['dir_path'] = '';
					$file_list[$i]['filesize'] = filesize($val['name']);
					$file_list[$i]['is_photo'] = in_array($file_ext, $this->ext_arr['image']);
					$file_list[$i]['filetype'] = $file_ext;
					$file_list[$i]['filename'] = $filename;
					$file_list[$i]['datetime'] = gsdate('Y-m-d H:i:s', filemtime($val['name']));
					$i++;
				}
			}
		}
		usort($file_list, 'self::cmp_func');
		$result = [];
		$result['moveup_dir_path'] = $moveup_dir_path;
		$result['current_dir_path'] = $current_dir_path;
		$result['current_url'] = $current_url;
		$result['total_count'] = count($file_list);
		$result['file_list'] = $file_list;
		$json = new \Services_JSON();
		echo $json->encode($result);
	}

	public static function cmp_func($a, $b) {
		global $order;
		if($a['is_dir'] && !$b['is_dir']) {
			return -1;
		} else if(!$a['is_dir'] && $b['is_dir']) {
			return 1;
		} else {
			if($order == 'size') {
				if($a['filesize'] > $b['filesize']) {
					return 1;
				} else if($a['filesize'] < $b['filesize']) {
					return -1;
				} else {
					return 0;
				}
			} else if($order == 'type') {
				return strcmp($a['filetype'], $b['filetype']);
			} else {
				return strcmp($a['filename'], $b['filename']);
			}
		}
	}

}

