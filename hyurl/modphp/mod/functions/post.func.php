<?php
/** 
 * is_single() 判断当前页面是否为文章详情页
 * @param  mixed   $key [可选]如果为整数，则判断是否是 ID 为 $key 的文章详情页
 *                      如果为字符串，则判断是否为标题是 $key 的文章详情页
 *                      如果为数组，则按数组内容逐一判断
 *                      如果不设置，则仅判断是否是文章详情页
 * @return boolean      成功返回 true, 失败返回 false
 */
function is_single($key = 0){
	if(is_template(config('post.template'))) {
		if($key && is_numeric($key)){
			return post_id() == $key;
		}elseif($key && is_string($key)){
			return post_title() == $key;
		}elseif(is_array($key)){
			foreach($key as $k => $v) {
				if(the_post($k) != $v) return false;
			}
		}
		return true;
	}
	return false;
}

/** 自动设置文章评论数量 */
add_hook('post.get.set_comment_counts', function($data){
	$comment = database::open(0)->select('comment', 'COUNT(*) AS count', "`post_id` = {$data['post_id']}");
	$count = $comment ? $comment->fetchObject()->count : 0; //获取实际评论数
	if($count != $data['post_comments']){
		$data['post_comments'] = $count;
		database::update('post', array('post_comments'=>$count), "`post_id` = {$data['post_id']}"); //更新记录
	}
	return $data;
}, false);

/** 分割搜索字符串 */
add_hook('post.get.before.split_keyword', function($arg){
	if(!empty($arg['keyword'])){
		if(strpos($arg['keyword'], '，')){
			$sep = '，'; //以中文逗号分割
		}elseif(strpos($arg['keyword'], ',')){
			$sep = ','; //以英文逗号分割
		}else{
			$sep = ' '; //以空格分割
		}
		$arg['keyword'] = explode($sep, $arg['keyword']);
		return $arg;
	}
}, false);