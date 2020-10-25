<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 分类模型
 */
class ActionLog {

	protected function getModelIdAttr($value, $data) {
		$value = get_document_field($data['model'], "name", "id");
		return $value ? $value : 0;
	}
}