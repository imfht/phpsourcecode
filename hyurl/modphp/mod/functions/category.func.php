<?php
/** 
 * category_tree() 获取分类目录树
 * @param  array  $arg [可选]请求参数
 * @return array       获取的目录树
 */
function category_tree($arg = array()){
	static $tree = false;
	static $sid = '';
	if(is_numeric($arg)) $arg = array('category_id'=>$arg);
	if(!$tree || $arg || $sid != session_id()){
		$sid = session_id();
		$tree = category::getTree($arg); //获取目录树
		error(null);
	}
	return $tree['success'] ? $tree['data'] : false;
}

/** 
 * is_category() 判断当前页面是否为分类目录页面
 * @param  mixed   $key [可选]如果为整数，则判断是否为 ID 是否是 $key 的分类目录页
 *                      如果为字符串，则判断则判断是否为 ID 是否是 $key 的分类目录页
 *                      如果为数组，则按数组内容逐一判断
 *                      如果不设置，则判断仅是否为分类目录页
 * @return boolean      成功返回 true, 失败返回 false
 */
function is_category($key = 0){
	if(is_template(config('category.template'))){
		if($key && is_numeric($key)){
				return category_id() == $key;
		}elseif($key && is_string($key)){
				return category_name() == $key;
		}elseif(is_array($key)){
			foreach($key as $k => $v) {
				if(the_category($k) != $v) return false; //逐一判断
			}
		}
		return true;
	}
	return false;
}

/** 在添加、更新、删除分类目录时检查编辑或管理员权限 */
add_hook(array(
	'category.add.check_permission', 
	'category.update.check_permission', 
	'category.delete.check_permission'
	), function(){
	if(!is_logined()) return error(lang('user.notLoggedIn'));
	if(!is_editor() && !is_admin()) return error(lang('mod.permissionDenied'));
}, false);

/** 在添加分类目录时检查名称可用性 */
add_hook('category.add.check_name', function($arg){
	if(!empty($arg['category_name']) && get_category(array('category_name'=>$arg['category_name']))){
		return error(lang('category.invalidName'));
	}
}, false);

/** 在更新分类目录时检查名称可用性 */
add_hook('category.update.check_name', function($arg){
	$_arg = array('category_name'=>$arg['category_name']);
	if(!empty($arg['category_id']) && !empty($arg['category_name']) && get_category($_arg)){
		if(category_id() != $arg['category_id'])
			return error(lang('category.invalidName'));
	}
}, false);

/** 自动设置子目录数量 */
add_hook('category.get.set_children_counts', function($data){
	$category = database::open(0)->select('category', 'COUNT(*) AS count', "`category_parent` = {$data['category_id']}");
	$count = $category ? $category->fetchObject()->count : 0; //获取子目录实际数量
	if(is_array($data['category_children']))
		$_count = count($data['category_children']);
	else
		$_count = $data['category_children'];
	if($count != $_count){
		if(!is_array($data['category_children'])) $data['category_children'] = $count;
		//更新数据库记录
		database::update('category', "`category_children` = $count", "`category_id` = {$data['category_id']}");
	}
	return $data;
}, false);

/** 自动设置分类目录所属文章数量 */
add_hook('category.get.set_post_counts', function($data){
	$post = database::open(0)->select('post', 'COUNT(*) AS count', "`category_id` = {$data['category_id']}");
	$count = $post ? $post->fetchObject()->count : 0; //获取文章实际数量
	if($count != $data['category_posts']){
		$data['category_posts'] = $count;
		//更新数据库记录
		database::update('category', "`category_posts` = $count", "`category_id` = {$data['category_id']}");
	}
	return $data;
}, false);

/** 删除分类目录后将子分类目录设置为顶级分类目录 */
add_hook('category.delete.complete.set_children_as_top', function($arg){
	database::open(0)->update('category', '`category_parent` = 0', "`category_parent` = {$arg['category_id']}");
}, false);