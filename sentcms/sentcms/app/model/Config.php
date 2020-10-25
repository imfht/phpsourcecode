<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\model;

use think\facade\Cache;
use think\Model;

class Config extends Model {

	public function getValuesAttr($value, $data) {
		return self::parse($data['type'], $data['value']);
	}

	public static function getConfigList($request) {
		$map[] = ['status', '=', 1];
		$data = self::where($map)->field('type,name,value')->select();

		$config = array();
		if ($data) {
			foreach ($data->toArray() as $value) {
				$config[$value['name']] = self::parse($value['type'], $value['value']);
			}
		}
		return $config;
	}

	public function getConfig($request) {
		$map[] = ['status', '=', 1];
		$data = self::where($map)->select()->append(['values']);
		return $data;
	}

	public function getConfigTree($request) {
		$map[] = ['status', '=', 1];
		$data = self::where($map)->select();

		$group = Cache::get('config')['config_group_list'];
		$config = [];
		foreach ($data->toArray() as $value) {
			if (isset($group[$value['group']])) {
				$config[$group[$value['group']]][] = $value;
			}
		}
		return $config;
	}

	/**
	 * 根据配置类型解析配置
	 * @param  integer $type  配置类型
	 * @param  string  $value 配置值
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	private static function parse($type, $value) {
		$data = [];
		switch ($type) {
			case 'textarea': //解析数组
				$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
				if (strpos($value, ':')) {
					foreach ($array as $val) {
						$list = explode(':', $val);
						if (isset($list[2])) {
							$data[] = ['key' => is_numeric($list[0]) ? (int) $list[0] : $list[0], 'value' => $list[1], 'label' => $list[1], 'other' => $list[2]];
						} else {
							$data[] = ['key' => is_numeric($list[0]) ? (int) $list[0] : $list[0], 'value' => $list[1], 'label' => $list[1]];
						}
					}
				} else {
					foreach ($array as $key => $val) {
						$data[] = ['key' => $key, 'value' => $val, 'label' => $val];
					}
				}
				break;
			default:
				return $value;
				break;
		}
		return $data;
	}

	public function getThemesList($request){
		return [
			'pc'  => $this->getList('pc'),
			'mobile' => $this->getList('mobile')
		];
	}

	protected function getList($type){
		$tempPath = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR;

		$file  = opendir($tempPath);
		$list = [];
		while (false !== ($filename = readdir($file))) {
			if (!in_array($filename, array('.', '..'))) {
				$files = $tempPath . $filename . '/info.php';
				if (is_file($files)) {
					$info = include($files);
					if (isset($info['type']) && $info['type'] == $type) {
						$info['id']  = $filename;
						$preview = '/template/' . $filename . '/' . $info['preview'];
						$info['preview'] = is_file($tempPath . $preview) ? $preview : '/static/common/images/default.png';
						$list[] = $info;
					}else{
						continue;
					}
				}
			}
		}
		return $list;
	}
}