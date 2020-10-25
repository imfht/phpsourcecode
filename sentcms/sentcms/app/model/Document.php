<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace app\model;

use think\facade\Db;

/**
 * 文档模型
 */
class Document {

	public static function getDocumentList($model, $category_id, $limit = 20, $order = "id desc", $field = "*"){
		$map = [];
		if (!$model) {
			return [];
		}
		//判断model是否为内容模型
		$info = Model::where('name', $model)->where('status', 1)->findOrEmpty();

		if ($info->isEmpty()) {
			return [];
		}
		$list = Db::name($model)->where($map)->limit($limit)->order($order)->field($field)->select();

		return $list;	
	}
}