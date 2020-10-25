<?php

define('WEB_ROOT', '/alidata/www/default/');
define('USER_ID', 6);

require_once(WEB_ROOT.'inc/config.php');
require_once(WEB_ROOT.'inc/dt.php');		//数据库模块
require_once(WEB_ROOT.'inc/func.php');		//通用函数

$auth = array('id' => USER_ID, 'name' => dt_query_one("SELECT name FROM user_info WHERE id = ".USER_ID)['name']);
set_time_limit(3600);

//执行发贴
function do_topic_add($forum_id, $title, $content, $orders = null, $icon_url = null) {
	global $auth;
	$title = get_substr(filter_var($title, FILTER_SANITIZE_STRING), 40);
	$content = preg_replace('/衡阳/', '<a href="http://www.hiici.com">衡阳搜索HIICI</a>', filter_content($content));
	$icon_url = @filter_var($icon_url, FILTER_SANITIZE_STRING);

	if (dt_query_one("SELECT id FROM forum_topic WHERE forum_id = '$forum_id' AND title = '$title'")) { echo '重复贴！^_^'; return false; } //标题重复则直接返回

	$forum = dt_query_one("SELECT name, auto_bg_url, auto_intro, topic_limit, city, ext FROM forum WHERE id = $forum_id");
	if (!$forum) return false;

	$rs = dt_query("INSERT INTO forum_topic (forum_id, title, icon_url, content, user_id, user_name, l_r_user_id, l_r_user_name, l_r_at, top_at, city, orders, c_at) 
		VALUES ('$forum_id', '$title', '$icon_url', '$content', ".$auth['id'].", '".$auth['name']."', ".$auth['id'].", '".$auth['name']."', ".time().", ".time().", ".$forum['city'].", ".((empty($orders)) ? '0' : '1').", ".time().")");
	if (!$rs) return false;

	//更新论坛图标
	$f_bg_url = (empty($icon_url)) ? get_img_url($content) : $icon_url;
	$bg_url_cond = (empty($forum['auto_bg_url']) || empty($f_bg_url)) ? '' : ", background_url = '$f_bg_url'";
	//更新论坛简介为帖子标题
	$intro_cond = (empty($forum['auto_intro'])) ? '' : ", intro = '$title'";

	//统计forum数据
	$rs = dt_query("UPDATE forum SET topic_c = topic_c + 1, reply_c = reply_c + 1 $bg_url_cond $intro_cond WHERE id = $forum_id");
	if (!$rs) { echo '更新forum数据失败！'; return false; } 

	if (0 < dt_count('forum', "WHERE id = $forum_id AND today = ".date('Ymd', time()))) {
		$rs = dt_query("UPDATE forum SET today_reply_c = today_reply_c + 1 WHERE id = $forum_id");
	} else {
		$rs = dt_query("UPDATE forum SET today = ".date('Ymd', time()).", today_reply_c = 1, today_up_c = 0 WHERE id = $forum_id");
	}
	if (!$rs) { echo '更新today_reply_c数据失败！'; return false; } 

	return true;
}
function do_topic_add_ext_1($forum_id, $title, $content, $icon_url, $price, $price_org, $phone) {
	if (!do_topic_add($forum_id, $title, $content, null, $icon_url)) return false;

	$price = doubleval($price);
	$price_org = doubleval($price_org);
	$phone = @filter_var($phone, FILTER_SANITIZE_STRING);

	$rs = dt_query("INSERT INTO forum_topic_ext_1 (id, price, price_org, phone) VALUES (last_insert_id(), '$price', '$price_org', '$phone')");
	if (!$rs) { echo '新建forum_topic_ext_1数据失败！^_^'; return false; }

	return true;
}
function get_topic_by_order_l_n($order_l_n, $forum_id) {
	return dt_query_one("SELECT id, start_t_s, l_r_at FROM forum_topic WHERE order_l_n = '$order_l_n' AND forum_id = '$forum_id' LIMIT 1");
}
function check_topic_live($topic) {
	return (time() < @$topic['start_t_s'] || time()-24*3600 < $topic['l_r_at']) ?  true : false;
}
function p_p_topic($forum_id, $s_t_z = null) {
	$cond = "WHERE forum_id = '$forum_id' AND start_t_s < ".time();
	dt_query("UPDATE forum_topic_ext_1 SET price = price_org WHERE price != price_org AND id IN (SELECT id FROM forum_topic $cond)");
	if (!empty($s_t_z)) dt_query("UPDATE forum_topic SET start_t = 0 $cond");
}

function do_topic_add_x($forum_id, $title, $content, $start_t_s, $out_s_u, $order_l_n, $topic_id = null, $icon_url = null) {
	if ($topic_id) {
		$icon_url = @filter_var($icon_url, FILTER_SANITIZE_STRING);
		$content = filter_content($content);
		dt_query("UPDATE forum_topic SET title = '".get_substr($title, 40)."', icon_url = '$icon_url', content = '$content', ".((empty($start_t_s)) ? '' : "start_t = 2, start_t_s = '$start_t_s',")." ".((empty($out_s_u)) ? '' : "out_s_u = '$out_s_u',")." l_r_at = ".time()." WHERE id = '$topic_id'");
	} else {
		if (do_topic_add($forum_id, $title, $content, null, $icon_url)) 
			dt_query("UPDATE forum_topic SET orders = 1, ".((empty($start_t_s)) ? '' : "start_t = 2, start_t_s = '$start_t_s',")." ".((empty($out_s_u)) ? '' : "out_s = 1, out_s_u = '$out_s_u',")." order_l_n = '$order_l_n' WHERE id = last_insert_id()");
	}

	return true;
}

function do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, $start_t_s, $out_s_u, $order_l_n, $topic_id = null) {
	if ($topic_id) {
		if (dt_query("UPDATE forum_topic_ext_1 SET price = '$price', price_org = '$price_org', phone = '$phone' WHERE id = '$topic_id'"))
			$content = filter_content($content);
		dt_query("UPDATE forum_topic SET title = '".get_substr($title, 40)."', icon_url = '$icon_url', content = '$content', ".((empty($start_t_s)) ? '' : "start_t = 2, start_t_s = '$start_t_s',")." ".((empty($out_s_u)) ? '' : "out_s_u = '$out_s_u',")." l_r_at = ".time()." WHERE id = '$topic_id'");
	} else {
		if (do_topic_add_ext_1($forum_id, $title, $content, $icon_url, $price, $price_org, $phone)) 
			dt_query("UPDATE forum_topic SET orders = 1, ".((empty($start_t_s)) ? '' : "start_t = 2, start_t_s = '$start_t_s',")." ".((empty($out_s_u)) ? '' : "out_s = 1, out_s_u = '$out_s_u',")." order_l_n = '$order_l_n' WHERE id = last_insert_id()");
	}

	return true;
}
function do_geo_x($geo, $topic_id = null) {
	$geo = @filter_var($geo, FILTER_SANITIZE_STRING);
	if (!empty($geo)) {
		$geo = split(',', $geo);

		require_once(WEB_ROOT.'inc/lib/geohash.class.php');
		$geohash = new Geohash;
		$geo = substr($geohash->encode($geo[0], $geo[1]), 0, 6);

		$t_id = ($topic_id) ? "'$topic_id'" : "last_insert_id()";
		dt_query("UPDATE forum_topic SET geo = '$geo' WHERE id = $t_id");
	}
}
function filter_content($content) {
	return preg_replace('/div/', 'e', preg_replace('/\'/', '', $content));
}
