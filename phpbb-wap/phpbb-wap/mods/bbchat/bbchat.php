<?php
/**
* @package phpBB-WAP
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/*
* MOD名称: 聊天室
* MOD支持地址: http://phpbb-wap.com
* MOD描述: phpbbb-wap聊天室插件
* MOD作者: 爱疯的云
* MOD版本: v6.0
* MOD显示: on
*/
include (ROOT_PATH . 'includes/functions/bbcode.php');
include (ROOT_PATH . 'includes/functions/selects.php');

define('SHOUTBOX_TABLE',$table_prefix.'shout');
define ( 'NUM_SHOUT', 20 );

$mod_name='bbchat';

// 禁止黑名单用户进入
$ban_information = session_userban($user_ip, $userdata ['user_id']);
if ($ban_information) trigger_error('注意：' . $ban_information);

//用户权限
switch($userdata['user_level']){
	case ADMIN :
	case MOD :
		$is_auth ['auth_mod'] = 1;
	default :
		$is_auth ['auth_read'] = 0;
		$is_auth ['auth_view'] = 0;
		if ($userdata ['user_id'] ==ANONYMOUS) {
			$is_auth['auth_delete'] = 0;
			$is_auth['auth_post'] = 0;
		} else {
			$is_auth ['auth_delete'] = 1;
			$is_auth ['auth_post'] = 1;
		}
}
if($is_auth ['auth_read']!=0)
	trigger_error('你没有权限读取聊天室内容</br>点击 <a href=\'loading.php?mod='.$mod_name.'\'>这里</a>返回');

// 是否开启 bbcode
$bbcode_on=1;
$submit=(isset($_POST['submit'])&&isset($_POST['message']))?1:0;
if(isset($_POST['mode'])||isset($_GET['mode'])){
	$mode=(isset($_POST['mode']))?$_POST['mode']:$_GET['mode'];
}else{
	$mode='';
}
if(isset($_POST['start1'])){
	$start1=abs(intval($_POST['start1']));
	$start=(($start1-1)*$board_config['posts_per_page']);
}else{
	$start=(isset($_GET['start']))?intval($_GET['start']):0;
	$start=($start<0)?0:$start;
}

$message = (isset($_POST['message'] ))?trim($_POST['message']):'';
$message .= (isset($_POST['smile_code']))?trim($_POST['smile_code']):'';
$message = htmlspecialchars ( $message );
//提交了发言表单
$info = '';
if((isset($_POST['submit'] )&&isset($_POST['message'] ))&&!empty($message)){
	$current_time = time();
	$where_sql = ($userdata ['user_id'] == ANONYMOUS) ? "shout_ip = '$user_ip'" : 'shout_user_id = ' . $userdata ['user_id'];
	$sql = "SELECT MAX(shout_session_time) AS last_post_time
		FROM " . SHOUTBOX_TABLE . "
		WHERE $where_sql";
	if ($result = $db->sql_query ( $sql )) {
		if ($row = $db->sql_fetchrow ( $result )) {
			if ($row ['last_post_time'] > 0 && ($current_time - $row ['last_post_time']) < $board_config ['flood_interval']) {
				$error = true;
				$info='<span style="color: red">您不能马上发表第二条信息，因为小于发表两条信息所必须最小间隔时间，请稍候重试</span>';
			}
		}
	}
	
	if(isset($_POST['submit'])&&!empty($message)&&$is_auth['auth_post']&&!$error){
		require_once(ROOT_PATH . 'includes/functions/post.php');
		$bbcode_uid = ($bbcode_on) ? make_bbcode_uid () : '';
		$message = prepare_message ( trim ( $message ), $html_on, $bbcode_on, $smilies_on, $bbcode_uid );
		$sql = "INSERT INTO " . SHOUTBOX_TABLE . " (shout_text, shout_session_time, shout_user_id, shout_ip, shout_username, shout_bbcode_uid,enable_bbcode,enable_html,enable_smilies) 
				VALUES ('$message', '" . time () . "', '" . $userdata ['user_id'] . "', '$user_ip', '" . phpbb_clean_username ( $userdata ['username'] ) . "', '" . $bbcode_uid . "',1,0,1)";
		if (! $result = $db->sql_query ( $sql )) {
			trigger_error('Error inserting shout.', E_USER_WARNING);
		} else {
			$URL = 'loading.php?mod='.$mod_name;
			header ( "Location: $URL" );
		}
		
		if ($board_config ['prune_shouts']) {
			$sql = "DELETE FROM " . SHOUTBOX_TABLE . " WHERE shout_session_time<=" . (time () - 86400 * $board_config ['prune_shouts']);
			if (! $result = $db->sql_query ( $sql )) {
				trigger_error('Error autoprune shouts.', E_USER_WARNING);
			}
		}
	}
	//删除操作
}elseif($is_auth['auth_mod']&&$mode=='delete'){
	if(isset($_GET[POST_POST_URL])||isset($_POST[POST_POST_URL])){
		$post_id=(isset($_POST[POST_POST_URL]))?intval($_POST[POST_POST_URL]):intval($_GET[POST_POST_URL]);
	} else {
		trigger_error('Error no shout id specifyed for delete/censor.', E_USER_WARNING);
	}
	
	$sql = "DELETE FROM " . SHOUTBOX_TABLE . " 
				WHERE shout_id = $post_id";
	if (! $result = $db->sql_query ( $sql )) {
		trigger_error('Error removing shout.', E_USER_WARNING);
	} else {
		$URL = 'loading.php?mod=' . $mod_name;
		header ( "Location: $URL" );
	}
}
require_once (ROOT_PATH . 'includes/functions/post.php');

page_header('聊天室');

$orig_word = array ();
$replacement_word = array ();
obtain_word_list ( $orig_word, $replacement_word );

$sql = "SELECT COUNT(*) as total 
		FROM " . SHOUTBOX_TABLE;
if (! ($result = $db->sql_query ( $sql ))) {
	trigger_error('Could not get shoutbox stat information', E_USER_WARNING);
}
$total_shouts = $db->sql_fetchrow ( $result );
$total_shouts = $total_shouts ['total'];
// KasP DETECTED

$template->set_filenames ( array (
		'body' => 'chat_body.tpl' 
) );

$pagination = generate_pagination ('loading.php?mod=' . $mod_name, $total_shouts, $board_config ['posts_per_page'], $start );

$for_you = '';
if (isset ( $_GET ['id'] )) {
	$for = intval ( abs ( $_GET ['id'] ) );
	
	$sql = "SELECT shout_username 
			FROM " . SHOUTBOX_TABLE . "
			WHERE shout_id = $for";
	
	$result = $db->sql_query ( $sql );
	if (! $result) {
		trigger_error('无法取得结果！', E_USER_WARNING);
	}
	$for_him = $db->sql_fetchrow ( $result );
	if (! empty ( $for_him ['shout_username'] )) {
		$for_you = $for_him ['shout_username'] . ',';
	} else {
		$for_you = '';
	}
}

$sql = "SELECT s.*, u.* FROM " . SHOUTBOX_TABLE . " s, " . USERS_TABLE . " u
		WHERE s.shout_user_id = u.user_id 
		ORDER BY s.shout_session_time DESC 
		LIMIT $start, " . $board_config ['posts_per_page'];
if (! ($result = $db->sql_query ( $sql ))) {
	trigger_error('Could not get shoutbox information', E_USER_WARNING);
}
$i = 0;
while ( $shout_row = $db->sql_fetchrow ( $result ) ) {
	$user_id = $shout_row ['shout_user_id'];
	$shout_username = ($user_id == ANONYMOUS) ? (($shout_row ['shout_username'] == '') ? '匿名用户' : $shout_row ['shout_username']) : '<a href="' . append_sid ( "ucp.php?mode=viewprofile&amp;" . 'u=' . $shout_row ['user_id'] ) . '">' . $shout_row ['username'] . '</a>';
	$shout = make_clickable ( $shout_row ['shout_text'] );
	$shout = smilies_pass ( $shout );
	$shout = bbencode_second_pass ( $shout, $shout_row ['shout_bbcode_uid'] );
	$shout = str_replace ( "\n", "\n<br />\n", $shout );
	//可执行删除操作
	if($is_auth['auth_mod']&&$is_auth['auth_delete']){
		$temp_url=append_sid('loading.php?mod='.$mod_name."&mode=delete&amp;".POST_POST_URL ."=".$shout_row['shout_id']);
		$delshout='<a href="'.$temp_url.'">删除</a>';
	}
	
	if ($userdata ['user_on_off'] == 1) {
		if ($shout_row ['user_session_time'] >= (time () - $board_config ['online_time'])) {
			if ($shout_row ['user_allow_viewonline']) {
				$online_status = '<span style="color: #0fff0f">在线</span>';
			} else if ($is_auth ['auth_mod'] || $userdata ['user_id'] == $poster_id) {
				$online_status = '<span style="color: #888888">隐身</span>';
			} else {
				$online_status = '<span style="color: #b40000">离线</span>';
			}
		} else {
			$online_status = '<span style="color:red;">(离线)</span>';
		}
	} else {
		$online_status = '';
	}

	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars ( 'shoutrow', array (
		'ROW_CLASS'			=> $row_class,
		'SHOUT' => $shout,
		'TIME' => create_date ( $board_config ['default_dateformat'], $shout_row ['shout_session_time'], $board_config ['board_timezone'] ),
		'SHOUT_USERNAME' => $shout_username,
		'POSTER_ONLINE_STATUS' => $online_status,
		'DELETE' => $delshout,
		'U_SHOUT_ID' => $shout_row ['shout_id'],
	) );
	$i ++;
}
//按钮
$update = append_sid ('loading.php?mod=' . $mod_name.'&up' . time ());
$smiles_select = smiles_select ();

$template->assign_vars ( array (
		'U_LOGIN' => login_back('loading.php?mod=bbchat', true),
		'SMILES_SELECT' => $smiles_select,
		'PAGINATION' => $pagination,
		'U_SHOUTBOX' => append_sid ('loading.php?mod=' . $mod_name),
		'OTVET' => $for_you,
		'UPDATE' => $update,
		'INFO' =>$info
) );

$template->pparse ( 'body' );
page_footer();

?>