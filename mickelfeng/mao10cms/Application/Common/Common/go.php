<?php 
//给用户粉丝发送动态
function mc_add_fans_trend($id=false) {
	if($id) {
		$user_id = $id;
	} else {
		$user_id = mc_user_id();
	};
	$fans_array = M('action')->where("page_id='$user_id' AND action_key='perform' AND action_value ='guanzhu'")->order('id desc')->getField('user_id',true);
	if($fans_array) : foreach($fans_array as $fans) :
		$trend = mc_user_trend_count($fans);
		M('action')->where("page_id='$fans' AND user_id='$fans' AND action_key='trend'")->delete();
		if($fans!=NULL) :
			$action['page_id'] = $fans;
			$action['user_id'] = $fans;
			$action['action_key'] = 'trend';
			$action['action_value'] = $trend+1;
			$action['date'] = strtotime("now");
			$result = M('action')->data($action)->add();
		endif; 
	endforeach; endif;
};
//给用户发送动态
function mc_add_user_trend($user_id) {
	$trend = mc_user_trend_count($user_id);
	if($user_id!=NULL) :
		M('action')->where("page_id='$user_id' AND user_id='$user_id' AND action_key='trend'")->delete();
		$action['page_id'] = $user_id;
		$action['user_id'] = $user_id;
		$action['action_key'] = 'trend';
		$action['action_value'] = $trend+1;
		$action['date'] = strtotime("now");
		$result = M('action')->data($action)->add();
	endif;
};

//用户发布文章时像关注TA的用户发送动态
function mc_publish_post_end($page_id) {
	$user_id = mc_user_id();
	$action['page_id'] = $page_id;
	$action['user_id'] = $user_id;
	$action['action_key'] = 'publish';
	$action['action_value'] = '';
	$action['date'] = strtotime("now");
	$result = M('action')->data($action)->add();
};
add_go('publish_post_end','mc_publish_post_end');

function mc_publish_baobei_end($page_id) {
	$user_id = mc_user_id();
	$action['page_id'] = $page_id;
	$action['user_id'] = $user_id;
	$action['action_key'] = 'publish';
	$action['action_value'] = '';
	$action['date'] = strtotime("now");
	$result = M('action')->data($action)->add();
};
add_go('publish_baobei_end','mc_publish_baobei_end');

function mc_publish_article_end($page_id) {
	$user_id = mc_user_id();
	$action['page_id'] = $page_id;
	$action['user_id'] = $user_id;
	$action['action_key'] = 'publish';
	$action['action_value'] = '';
	$action['date'] = strtotime("now");
	$result = M('action')->data($action)->add();
};
add_go('publish_article_end','mc_publish_article_end');

function mc_publish_group_end($page_id) {
	$user_id = mc_user_id();
	$action['page_id'] = $page_id;
	$action['user_id'] = $user_id;
	$action['action_key'] = 'publish';
	$action['action_value'] = '';
	$action['date'] = strtotime("now");
	$result = M('action')->data($action)->add();
};
add_go('publish_group_end','mc_publish_group_end');

function mc_publish_pro_end($page_id) {
	$user_id = mc_user_id();
	$action['page_id'] = $page_id;
	$action['user_id'] = $user_id;
	$action['action_key'] = 'publish';
	$action['action_value'] = '';
	$action['date'] = strtotime("now");
	$result = M('action')->data($action)->add();
};
add_go('publish_pro_end','mc_publish_pro_end');

function mc_user_home_end() {
	$user_id = mc_user_id();
	M('action')->where("page_id='$user_id' AND user_id='$user_id' AND action_key='trend'")->delete();
	$action['page_id'] = $user_id;
	$action['user_id'] = $user_id;
	$action['action_key'] = 'trend';
	$action['action_value'] = '0';
	$action['date'] = strtotime("now");
	$result = M('action')->data($action)->add();
};
add_go('user_home_end','mc_user_home_end');

//用户发布评论时，向文章作者和自己的粉丝发送动态
function mc_publish_comment_end($user_id) {
	mc_add_user_trend($user_id);
	mc_add_fans_trend();
};
add_go('publish_comment_end','mc_publish_comment_end');

//AT 用户时，给该用户发送动态
function mc_publish_at_end($user_id) {
	mc_add_user_trend($user_id);
};
add_go('publish_at_end','mc_publish_at_end');

//用户喜欢某篇文章时，向文章作者和自己的粉丝发送动态
function mc_add_xihuan_end($user_id) {
	mc_add_user_trend($user_id);
	mc_add_fans_trend();
};
add_go('add_xihuan_end','mc_add_xihuan_end');

//用户收藏文章时，向文章作者和自己的粉丝发送动态
function mc_add_shoucang_end($user_id) {
	mc_add_user_trend($user_id);
	mc_add_fans_trend();
};
add_go('add_shoucang_end','mc_add_shoucang_end');

//用户取消收藏文章时，向文章作者和自己的粉丝发送动态
function mc_remove_shoucang_end($user_id) {
	mc_add_user_trend($user_id);
	mc_add_fans_trend();
};
add_go('remove_shoucang_end','mc_remove_shoucang_end');

//用户关注其他人时，向被关注者和自己的粉丝发送动态
function mc_add_guanzhu_end($user_id) {
	mc_add_user_trend($user_id);
	mc_add_fans_trend();
};
add_go('add_guanzhu_end','mc_add_guanzhu_end');

//用户取消关注其他人时，向被关注者和自己的粉丝发送动态
function mc_remove_guanzhu_end($user_id) {
	mc_add_user_trend($user_id);
	mc_add_fans_trend();
};
add_go('remove_guanzhu_end','mc_remove_guanzhu_end');

?>