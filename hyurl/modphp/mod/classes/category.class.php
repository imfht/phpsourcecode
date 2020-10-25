<?php
/** 分类目录模块，包含获取分类目录树的方法 */
final class category extends mod{
	const TABLE = 'category'; //表名
	const PRIMKEY = 'category_id'; //主键

	/**
	 * getTree() 获取分类目录树形结构数据
	 * @static
	 * @param  array  $arg  [可选]请求参数，可以提供下面这些字段中的一个：
	 *                      [category_id] => 目录 ID
	 *                      [category_parent] => 父目录 ID
	 * @return array        分类目录结构
	 */
	static function getTree($arg = array()){
		$default = array(
			'category_id'=>0, //目录 ID
			'category_parent'=>0, //父目录 ID
			);
		$arg = is_array($arg) ? array_merge($default, $arg) : $default;
		do_hooks('category.get.before', $arg); //执行回调函数
		if($arg['category_id']) //通过 ID 查询
			$where['category_id'] = $arg['category_id'];
		else //通过父目录 ID 查询
			$where['category_parent'] = $arg['category_parent'];
		$categories = array();
		$result = database::open(0)->select('category', '*', $where, 0); //从数据库获取数据
		while ($result && $category = $result->fetch()) { //迭代获取数据
			self::handler($category, 'get');
			do_hooks('category.get', $category); //执行挂钩函数
			if(error()) return error();
			unset($arg['category_id']);
			$arg['category_parent'] = $category['category_id'];
			$categoryChildren = self::getTree($arg); //递归获取子目录
			if($categoryChildren['success'])
				$category['category_children'] = $categoryChildren['data']; //应用子目录数据
			$categories[] = $category;
			error(null); //递归中需要将错误信息置空
		}
		return $categories ? success($categories) : error(lang('mod.noData', lang('category.label')));
	}
}