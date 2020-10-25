<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 设置模型
 */
class Channel extends \think\Model {

	protected $type = array(
		'id' => 'integer',
	);

	protected function setStatusAttr($value){
		return ($value !== '') ? $value : 1;
	}

	protected function getStatusTextAttr($value, $data){
		$status = [0 => '禁用', 1 => '启用'];
		return isset($status[$data['status']]) ? $status[$data['status']] : '禁用';
	}

	public static function getChannelList($type, $pid = '', $tree = false){
		$map = [];
		$map[] = ['status', '=', 1];
		if ($pid !== '') {
			$map[] = ['pid', '=', $pid];
		}
		if($type !== ''){
			$map[] = ['type', '=', $type];
		}
		$list = self::where($map)->order('sort asc, id desc')->select()->each(function($item){
			if(strpos($item['url'], "?")){
				$url = parse_url($item['url']);
				$param = [];
				parse_str($url['query'], $param);
				$item['url'] = url($url['path'], $param);
			}else{
				$item['url'] = url($item['url']);
			}
			return $item;
		})->toArray();
		if ($tree) {
			$list = (new \sent\tree\Tree())->listToTree($list);
		}
		return $list;
	}
}