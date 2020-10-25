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
class Category extends \think\Model{

	protected $name = "Category";
	protected $auto = array('update_time', 'status' => 1);

	protected $type = array(
		'icon' => 'integer',
	);

	protected static function onAfterUpdate($model){
		$data = $model->toArray();
	}

	public static function getCategoryTree($map = []){
		$list = self::where($map)->select();

		if (!empty($list)) {
			$tree = new \sent\tree\Tree();
			$list = $tree->toFormatTree($list->toArray());
		}
		return $list;
	}

	public function info($id, $field = true) {
		return $this->db()->where(array('id' => $id))->field($field)->find();
	}
}