<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\model;

use think\Model;
use think\facade\Db;

class Dictionary extends Model {
	
	protected $type = [
		'pid'   => 'integer',
		'code'   => 'integer'
	];
	
	public function getTitleAttr($value, $data){
		return $data['name'];
	}

	public function getDictionList($request){
		$param = $request->param();
		$map = [];
		$order = "id asc";

		if (isset($param['title']) && $param['title'] != '') {
			$map[] = ['title', 'LIKE', '%'.$param['title'].'%'];
		}
		if (isset($param['status']) && $param['status'] != '') {
			$map[] = ['status', '=', $param['status']];
		}
		if (isset($param['type']) && $param['type'] != '') {
			$map[] = ['model', '=', $param['type']];
		}

		return self::where($map)->field('id, name, pid, code, model')->order($order)->select();
	}
	
}