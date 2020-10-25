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
 * 伪静态
 */
class Rewrite extends \think\Model {

	protected $autoWriteTimestamp = true;

	public static $keyList = array(
		array('name' => 'id', 'title' => '标识', 'type' => 'hidden'),
		array('name' => 'rule', 'title' => '规则名称', 'type' => 'text', 'is_must'=> true, 'option' => '', 'help' => '规则名称，方便记忆'),
		array('name' => 'url', 'title' => '规则地址', 'type' => 'text', 'is_must'=> true, 'option' => '', 'help' => '规则地址'),
	);

	/**
	 * 数据修改
	 * @return [bool] [是否成功]
	 */
	public function change() {
		$data = \think\Request::instance()->post();
		if (isset($data['id']) && $data['id']) {
			return $this->validate(true)->save($data, array('id' => $data['id']));
		} else {
			return $this->validate(true)->save($data);
		}
	}
}