<?php
/** 自动设置评论回复数量 */
add_hook('comment.get.set_reply_counts', function($data){
	$comment = database::open(0)->select('comment', 'COUNT(*) AS count', "`comment_parent` = {$data['comment_id']}");
	$count = $comment ? $comment->fetchObject()->count : 0; //实际回复数量
	if($count != $data['comment_replies']){
		$data['comment_replies'] = $count;
		//更新数据库记录
		database::update('comment', "`comment_replies` = $count", "`comment_id` = {$data['comment_id']}");
	}
	return $data;
}, false);