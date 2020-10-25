<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

 /**
 * 函数：get_db_stat(模式)
 * 说明：获取一些统计信息
 * 参数：
 *		（字符串）模式
 *			usercount 		统计用户 
 *			newestuser		统计新注册用户
 *			postcount		统计帖子
 *			topiccount		统计主题帖
 *			attachcount		统计附件
 * 返回：整型或布尔型
 **/
function get_db_stat($mode)
{
	global $db;

	switch( $mode )
	{
		case 'usercount':
			$sql = "SELECT COUNT(user_id) AS total
				FROM " . USERS_TABLE . "
				WHERE user_id <> " . ANONYMOUS;
			break;

		case 'newestuser':
			$sql = "SELECT user_id, username
				FROM " . USERS_TABLE . "
				WHERE user_id <> " . ANONYMOUS . "
				ORDER BY user_id DESC
				LIMIT 1";
			break;

		case 'postcount':
		case 'topiccount':
			$sql = "SELECT SUM(forum_topics) AS topic_total, SUM(forum_posts) AS post_total
				FROM " . FORUMS_TABLE;
			break;
		case 'attachcount':
			$sql = "SELECT count(*) AS total FROM " . ATTACHMENTS_DESC_TABLE;
			break;
	}
	if ( !($result = $db->sql_query($sql)) )
	{
		return false;
	}

	$row = $db->sql_fetchrow($result);

	switch ( $mode )
	{
		case 'usercount':
			return $row['total'];
			break;
		case 'newestuser':
			return $row;
			break;
		case 'postcount':
			return $row['post_total'];
			break;
		case 'topiccount':
			return $row['topic_total'];
			break;
		case 'attachcount':
			return $row['total'];
			break;
	}

	return false;
}

 /**
 * 函数：phpbb_clean_username(用户名)
 * 说明：规范用户名
 * 参数：
 *		（字符串）用户名
 * 返回：字符串 
 **/
function phpbb_clean_username($username)
{
	$username = substr(htmlspecialchars(str_replace("\'", "'", trim($username))), 0, 25);
	$username = phpbb_rtrim($username, "\\");
	$username = str_replace("'", "\'", $username);//添加反斜杠

	return $username;
}

 /**
 * 函数：phpbb_clean_username(邮件地址)
 * 说明：规范电子邮件
 * 参数：
 *		（字符串）
 * 返回：字符串
 **/
function phpbb_clean_email($email)
{
   $email = substr(htmlspecialchars(str_replace("\'", "'", trim($email))), 0, 255);
   $email = phpbb_rtrim($email, "\\");
   $email = str_replace("'", "\'", $email);

   return $email;
} 

/**
* phpbb_ltrim(被处理的字符串, 待删除的字符串)
* 说明：函数从字符串左侧删除空格或其他预定义字符。
* 参数：
*		待删除的字符串		当该值为空是默认为false
* 返回：字符串
**/
function phpbb_ltrim($str, $charlist = false)
{
	if ($charlist === false)
	{
		return ltrim($str);// ltrim() 函数从字符串左侧删除空格或其他预定义字符。
	}

	$str = ltrim($str, $charlist);

	return $str;
}

/**
* 函数：phpbb_ltrim(被处理的字符串, 待删除的字符串)
* 函数从字符串右端删除字符，用法同于phpbb_ltrim()函数，区别于左右
**/
function phpbb_rtrim($str, $charlist = false)
{
	if ($charlist === false)
	{
		return rtrim($str);
	}
	
	$str = rtrim($str, $charlist);
	
	return $str;
}

/**
* 函数：dss_rand()
* 作用：产生随机值
**/ 
function dss_rand()
{
	global $db, $board_config, $dss_seeded, $cache;

	$val = $board_config['rand_seed'] . microtime();
	$val = md5($val);
	$board_config['rand_seed'] = md5($board_config['rand_seed'] . $val . 'a');
   
	if($dss_seeded !== true)
	{
		set_config('rand_seed', $board_config['rand_seed']);
		$cache->clear('global_config');
		$dss_seeded = true;
	}

	return substr($val, 4, 16);
}

/**
* get_userdata(用户, 强制用户名模式)
* 说明：通过用户名或用户ID获取用户信息
* 参数：
*		强制模式 == ture	把“用户”参数的值强制定义为用户名
*		强制模式			默认为false  
**/
function get_userdata($user, $force_str = false)
{
	global $db;

	if (!is_numeric($user) || $force_str)
	{
		$user = phpbb_clean_username($user);
	}
	else
	{
		$user = intval($user);
	}

	$sql = "SELECT *
		FROM " . USERS_TABLE . " 
		WHERE ";
	$sql .= ( ( is_int($user) ) ? "user_id = $user" : "username = '" .  str_replace("\'", "''", $user) . "'" ) . " AND user_id <> " . ANONYMOUS;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法查询用户信息', E_USER_WARNING);
	}

	return ( $row = $db->sql_fetchrow($result) ) ? $row : false;
}

/**
* 	初始化$userdata和borad_config
*/
function init_userprefs($userdata, $mods_path = false)
{
	global $board_config, $template, $db, $style, $images;
	
	if ( $userdata['user_id'] != ANONYMOUS )
	{

		if ( !empty($userdata['user_dateformat']) )
		{
			$board_config['default_dateformat'] = $userdata['user_dateformat'];
		}
		if ( isset($userdata['user_timezone']) )
		{
			$board_config['board_timezone'] = $userdata['user_timezone'];
		}
		if ( isset($userdata['user_topics_per_page']) )
		{
			$board_config['topics_per_page'] = $userdata['user_topics_per_page'];
		}
		if ( isset($userdata['user_posts_per_page']) )
		{
			$board_config['posts_per_page'] = $userdata['user_posts_per_page'];
		}
	}

	require_once ROOT_PATH . 'includes/class/style.php';
		
	$style = new Style();
	
	if (!$mods_path)
	{
		$style->Setup();
	}
	else
	{
		$template = new Template(ROOT_PATH . "mods/$mods_path/template/");
	}

	$images = obtain_images_data();
}

/**
* 函数：encode_ip(ip地址)
* 说明：对ip进行十六进制编码
**/
function encode_ip($dotquad_ip)
{
    //检测IPv6的localhost
	if ($dotquad_ip == "::1")
	{
		$dotquad_ip = '127.0.0.1';
	}
	
	if( !preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/", $dotquad_ip) )
	{
		return '';
	}
	
	$ip_sep = explode('.', $dotquad_ip);
	
	return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}

/**
* 函数：decode_ip(ip地址)
* 说明：把十六进制的ip转换成十进制
**/
function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

/**
* 创建日期
**/
function create_date($format, $gmepoch, $tz)
{
	global $board_config;
	static $translate;

	if ( empty($translate) )
	{
		$datetime = array(
		'Sunday' => '星期日','Monday' => '星期一','Tuesday' => '星期二','Wednesday' => '星期三','Thursday' => '星期四','Friday' => '星期五','Saturday' => '星期六',

		'Sun' => '日','Mon' => '一', 'Tue' => '二','Wed' => '三', 'Thu' => '四','Fri' => '五', 'Sat' => '六',
		
		'January' => '1', 'February' => '2', 'March' => '3', 'April' => '4', 'Mays' => '5', 'June' => '6', 'July' => '7', 'August' => '8', 'September' => '9', 'October' => '10', 'November' => '11', 'December' => '12',

		'Jan' => '1', 'Feb' => '2', 'Mar' => '3', 'Apr' => '4', 'May' => '5', 'Jun' => '6', 'Jul' => '7', 'Aug' => '8', 'Sep' => '9', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12',
		);
		
		@reset($datetime);
		foreach($datetime as $match => $replace)
		{
			$translate[$match] = $replace;
		}
	}
	return ( !empty($translate) ) ? strtr(@gmdate($format, $gmepoch + (3600 * $tz)), $translate) : @gmdate($format, $gmepoch + (3600 * $tz));
}

/**
* 创建分页处理
**/
function generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = TRUE)
{
	// ceil() 函数向上舍入为最接近的整数
	$total_pages = ceil($num_items/$per_page);

	if ( $total_pages <= 1 )
	{
		return '';
	}
	// floor() 函数向下舍入为最接近的整数
	$on_page = floor($start_item / $per_page) + 1;

	$page_string = '<div class="pagination"><form action="' . append_sid($base_url) . '" method="post">';
	
	if ( $on_page == 1 )
	{
		// append_sid() 为phpBB-WAP内建函数，用于创建 SID 用
		$page_string .= '<span>首页</span>';
	}
	elseif( ($on_page > 1  && $on_page < $total_pages) || $on_page == $total_pages)
	{
		$page_string .= '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $on_page - 2 ) * $per_page ) ) . '">上页</a>';
	}

	if ( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;

		for($i = 1; $i < $init_page_max + 1; $i++)
		{
			$page_string .= ( $i == $on_page ) ? '<span>' . $i . '</span>' : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
			if ( $i <  $init_page_max )
			{
				$page_string .= "";
			}
		}

		if ( $total_pages > 3 )
		{
			if ( $on_page > 1  && $on_page < $total_pages )
			{
				$page_string .= ( $on_page > 5 ) ? '<span>...</span>' : '';

				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++)
				{
					$page_string .= ($i == $on_page) ? '<span>' . $i . '</span>' : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
					if ( $i <  $init_page_max + 1 )
					{
						$page_string .= '';
					}
				}

				$page_string .= ( $on_page < $total_pages - 4 ) ? '<span>...</span>' : '';
			}
			else
			{
				$page_string .= '<span>...</span>';
			}

			for($i = $total_pages - 2; $i < $total_pages + 1; $i++)
			{
				$page_string .= ( $i == $on_page ) ? '<span>' . $i . '</span>'  : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
				if( $i <  $total_pages )
				{
					$page_string .= '';
				}
			}
		}
	}
	else
	{
		for($i = 1; $i < $total_pages + 1; $i++)
		{
			$page_string .= ( $i == $on_page ) ? '<span>' . $i . '</span>' : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
			if ( $i <  $total_pages )
			{
				$page_string .= '';
			}
		}
	}

	if ( $total_pages > 3 )
	{	
		$page_string .= '<span><input type="text" name="start1" size="1" value="' . $on_page . '" /> / ' . ceil( $num_items / intval($per_page) ) . '</span><input type="submit"/>';
	}

	if ( $on_page == 1 || ($on_page > 1  && $on_page < $total_pages))
	{
		// append_sid() 为phpBB-WAP内建函数，用于创建 SID 用
		$page_string .= '<a href="' . append_sid($base_url . "&amp;start=" . ( $on_page * $per_page ) ) . '">下页</a>';
	}
	elseif ( $on_page == $total_pages )
	{
		$page_string .= '<span>尾页</span>';
	}

	$page_string .= '</form></div><div class="clear"></div>';

	return $page_string;
}

function phpbb_preg_quote($str, $delimiter)
{
	$text = preg_quote($str);
	$text = str_replace($delimiter, '\\' . $delimiter, $text);
	
	return $text;
}

/**
* 取得 word_list
**/
function obtain_word_list(&$orig_word, &$replacement_word)
{
	global $db;

	$sql = "SELECT word, replacement
		FROM  " . WORDS_TABLE;
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法获取敏感词信息', E_USER_WARNING);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		do 
		{
			
			$orig_word[] = $row['word'];
			$replacement_word[] = $row['replacement'];
		}
		while ( $row = $db->sql_fetchrow($result) );
	}

	return true;
}

/*
*	信息的输出或用于错误处理
*	@参数 整数 $errno 错误号
*	@参数 字符串 $errstr 错误提示文字
*	@参数 字符串 $errfile 错误文件
*	@参数 整数 $errline 错误的行数
*/
function error_message($errno, $errstr, $errfile, $errline)
{
	global $template, $db;

	if (error_reporting() == 0 && $errno != E_USER_ERROR && $errno != E_USER_WARNING && $errno != E_USER_NOTICE) return;

	switch ($errno)
	{
		case E_NOTICE:
		case E_WARNING:
			if (($errno & ((defined('DEBUG')) ? E_ALL : error_reporting())) == 0) return;
			break;
		// 严重的错误
		case E_USER_WARNING:
			
			ob_clean();
			ob_end_flush();
			
			$sql_error = $db->sql_error();

			echo '<!DOCTYPE HTML>';
			echo '<html>';
			echo '<head>';
			echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
			echo '<title>提示</title>';
			echo '<style type="text/css">@charset "utf-8";*{margin:0;padding:0;}body{margin:0 auto;max-width:640px;font-family:"Century Gothic","Microsoft yahei";background-color:#F9F9F9;}#wrap{background-color:#FFF;width:640px;}.error{padding:20px;margin:0;border-style:solid;border-width:1px;border-color:#000;}.main{padding:115px 0 6px 0;}</style>';
			echo '</head>';
			echo '<body>';
			echo '	<div id="wrap">';
			echo '		<div class="main">';
			echo '			<div class="error">';
			echo '				<p>' . $errstr . '</p>';
			if ( $sql_error['message'] != '' && DEBUG) {
				echo '				<p>SQL Error: ' . $sql_error['message'] . '</p>';
			}
			if ( $sql_error['sql'] != '' && DEBUG) {
				echo '				<p>SQL Code：' . $sql_error['sql'] . '</p>';
			}
			if (DEBUG) {
				echo '				<p>Error File: ' . basename($errfile) . '</p>';
				echo '				<p>Error Line: ' . $errline . '</p>';
			}
			echo '			</div>';
			echo '		</div>';
			echo '	<div>';
			echo '</body>';
			echo '</html>';
			//add_log(LOG_TYPE_ERROR, $errstr);
			exit;
			break;
		// 用于数据库出错
		case E_USER_ERROR:
		case E_USER_NOTICE:
			page_header('提示');
			$template->assign_vars(array(
				'MESSAGE_TITLE' => '提示',
				'MESSAGE_TEXT'	=> $errstr)
			);
			$template->set_filenames(array(
				'message' => 'message_body.tpl')
			);
			$template->pparse('message');
			page_footer();
			break;
	}
}

/*
*	返回链接
*/
function back_link($url = false)
{

	$back_str = '';

	if ($url)
	{
		$back_str .= '<br />点击 <a href="' . $url . '">这里</a> 返回上一页面';
	}
	
	if (defined('IN_ADMIN'))
	{
		$back_str .= '<br />点击 <a href="' . append_sid(ROOT_PATH . 'admin/index.php') . '">这里</a> 返回管理面板首页';	
	}
	$back_str .= '<br />点击 <a href="' . append_sid(ROOT_PATH . 'index.php') . '">这里</a> 返回首页';

	return $back_str;
}

/*
*	用户界面页面的头部
*	@参数 字符串 $page_title 网页的标题
*/
function page_header($page_title = '')
{
	global $template, $db, $modules, $board_config, $userdata, $user_ip;

	if (!defined('HEADER_INC'))
	{
		
		define('HEADER_INC', true);
		
		ob_start();
		
		$page_id = (isset($_GET['page'])) ? abs(intval($_GET['page'])) : 0;
		
		require_once(ROOT_PATH . 'includes/class/module.php');

		$modules = new Module($page_id, $page_title);

		if (!defined('IN_ADMIN'))
		{
			$modules->display(MODULE_HEAD);	// 显示<head></head>标签内的内容		

			$modules->display(MODULE_HEADER); // 显示全局顶部
			
			$display_hide = '';

			$display = ($board_config['display_login'] && $modules->page_id == 0) ? true : false;
		}
		else
		{
			if ($modules->page_id == 0)
			{
				$display = true;
				$display_hide = $board_config['display_login'] ? '【<a href="' . append_sid(ROOT_PATH . 'admin/admin_module.php?mode=login') . '">隐藏</a>】' : '【<a href="' . append_sid(ROOT_PATH .'admin/admin_module.php?mode=login') . '">显示</a>】';
				$template->assign_var('DISPLAY_HIDE', $display_hide);
			}
			else
			{
				$display = false;
			}
		}

		if ($userdata['session_logged_in'])
		{
			if ($display)
			{			
				$template->assign_block_vars('switch_user_logged_in', array());	
			}
			

			if ( $userdata['user_new_privmsg'] )
			{
				$unread_privmsgs	= $userdata['user_new_privmsg'];

				if ( $userdata['user_last_privmsg'] > $userdata['user_lastvisit'] )
				{
					$sql = 'UPDATE ' . USERS_TABLE . '
						SET user_last_privmsg = ' . $userdata['user_lastvisit'] . '
						WHERE user_id = ' . $userdata['user_id'];
					if ( !$db->sql_query($sql) )
					{
						trigger_error('无法更新用户的私人信息', E_USER_WARNING);
					}
				}
			}
			else
			{
				$unread_privmsgs = 0;
			}
		}
		else
		{
			$unread_privmsgs = 0;
			if ($display)
			{
				$template->assign_block_vars('switch_user_logged_out', array());
			}
		}

		if ($board_config['use_tpl_css'])
		{
			$template->assign_block_vars('use_tpl_css', array());
		}

		//加载黑名单提示信息
		$ban_information = session_userban($user_ip, $userdata['user_id']);
		if ($ban_information)
		{
			$template->assign_block_vars('show_ban_info', array(
				'BAN_INFORMATION' => $ban_information)
			);
		}

		if ($modules->page_id == 0)
		{
			if (defined('THIS_INDEX'))
			{
				$page_title = $board_config['sitename'];
			}
			else
			{
				$page_title = $page_title;
			}
		}
		else
		{
			$page_title = ($page_title == '') ? $modules->page_title : $page_title;
		}

		$template->assign_vars(array(	
			'PAGE_TITLE' 					=> ( !defined('THIS_INDEX') ) ? $page_title . '_' . $board_config['sitename'] : $page_title,
			'SITENAME'						=> $board_config['sitename'],
			'SITE_DESC'						=> $board_config['site_desc'],
			'U_INDEX'						=> append_sid(ROOT_PATH . 'index.php'),
			'U_ADMIN'						=> append_sid(ROOT_PATH . 'admin/index.php'),
			'U_ADMIN_INDEX'					=> append_sid(ROOT_PATH . 'admin/index.php?pane=left'),
			'UNREAD_PM'						=> $unread_privmsgs,
			'ROOT_PATH'						=> $template->root)
		);

		// IE 浏览器不支持 application/xhtml+xml
		header('Content-type: text/html; charset=UTF-8');
		header('Cache-Control: private, no-cache="set-cookie"');
		header('Expires: 0');
		header('Pragma: no-cache');

		
		if(defined('IN_ADMIN'))
		{
			$template->set_filenames(array(
				'admin_header'		=> 'admin/page_header.tpl') 
			);

			$template->pparse('admin_header');
		}
		else
		{
			$template->set_filenames(array(
				'overall_header' 	=> 'overall_header.tpl') 
			);
			$template->pparse('overall_header');
		}
	}
}

/*
*	用户界面的底部
*/
function page_footer()
{
	global $db, $template, $board_config, $userdata, $modules, $starttime;

	if (!defined('FOOTER_INC'))
	{

		define('FOOTER_INC', true);

		if(defined('IN_ADMIN'))
		{
			$template->set_filenames(array(
				'admin_footer'		=> 'admin/page_footer.tpl')
			);
			$template->pparse('admin_footer');
		}
		else
		{
			$template->set_filenames(array(
				'overall_footer' 	=> 'overall_footer.tpl')
			);

			$template->assign_var('RUNTIME', spent_runtime($starttime));

			$modules->display(MODULE_FOOTER);
			$template->pparse('overall_footer');
		}
		
		if ($board_config['open_rewrite'])
		{
			$ob_contents = ob_get_contents();
			ob_end_clean();
			echo phpbb_replace_mod_rewrite($ob_contents); 
		}

		$db->sql_close(); 
		
		exit;
	}

}

/**
* 返回绝对路径
**/
function phpbb_realpath($path)
{
	// function_exists() 用来检查指定的函数是否已经定义
	return (!@function_exists('realpath') || !@realpath(ROOT_PATH . 'includes/functions/common.php')) ? $path : @realpath($path);
}

/**
* 测试一个文件和目录是否可写
*
* 当PHP本地函数 is_writable() 可以使用优先使用
*
* @参数 字符串 $file 文件路径
* @return 当可写时返回 true，否则返回 false.
*/
function phpbb_is_writable($file)
{
	if (strtolower(substr(PHP_OS, 0, 3)) === 'win' || !function_exists('is_writable'))
	{
		if (file_exists($file))
		{
			$file = phpbb_realpath($file);

			if (is_dir($file))
			{
				$result = @tempnam($file, 'i_w');

				if (is_string($result) && file_exists($result))
				{
					unlink($result);
					return (strpos($result, $file) === 0) ? true : false;
				}
			}
			else
			{
				$handle = @fopen($file, 'r+');

				if (is_resource($handle))
				{
					fclose($handle);
					return true;
				}
			}
		}
		else
		{
			$dir = dirname($file);

			if (file_exists($dir) && is_dir($dir) && phpbb_is_writable($dir))
			{
				return true;
			}
		}

		return false;
	}
	else
	{
		return is_writable($file);
	}
}

/**
* URL 重定向
* 旧的函数会因后台的域名、路径信息填写不正确而造成重定向失败
* 虽然这样做不是很理想，但是这是对一些新站长的一些照顾
**/
function redirect($url)
{
	global $db;

	//global $board_config;

	// 重定向之前先关闭数据库链接
	if (!empty($db))
	{
		$db->sql_close();
	}
	
	if (strstr(urldecode($url), "\n") || strstr(urldecode($url), "\r") || strstr(urldecode($url), ';url'))
	{
		trigger_error('由于该 URL 潜在安全隐患，因此您不能指向此 URL', E_USER_WARNING);
	}

	/**
	//$server_protocol 	= ($board_config['cookie_secure']) ? 'https://' : 'http://';
	//$server_name 		= preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['server_name']));
	//$server_port 		= ($board_config['server_port'] <> 80) ? ':' . trim($board_config['server_port']) : '';
	//$script_name 		= preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['script_path']));
	//$script_name 		= ($script_name == '') ? $script_name : '/' . $script_name;
	//$url 				= preg_replace('#^\/?(.*?)\/?$#', '/\1', trim($url));
	**/
	
	header('Location: ' . ROOT_PATH . $url);
	exit;
}

/**
* 检查时间是否正确
**/
function mkrealdate($day, $month, $birth_year)
{
	if ($month < 1 || $month > 12)
	{
		return 'error';
	}
	
	switch ($month)
	{
		case 1:
			if ($day > 31)
			{
				return "error";
			}
			$epoch = 0;
			break;
		case 2:
			if ($day > 29)
			{
				return "error";
			}
			$epoch = 31;
			break;
		case 3:
			if ($day > 31)
			{
				return "error";
			}
			$epoch = 59;
			break;
		case 4:
			if ($day > 30)
			{
				return "error" ;
			}
			$epoch = 90;
			break;
		case 5:
			if ($day > 31)
			{
				return "error";
			}
			$epoch = 120;
			break;
		case 6:
			if ($day > 30)
			{
				return "error";
			}
			$epoch = 151;
			break;
		case 7:
			if ($day > 31)
			{
				return "error";
			}
			$epoch = 181;
			break;
		case 8:
			if ($day > 31)
			{
				return "error";
			}
			$epoch = 212;
			break;
		case 9:
			if ($day > 30)
			{
				return "error";
			}
			$epoch = 243;
			break;
		case 10:
			if ($day > 31)
			{
				return "error";
			}
			$epoch = 273;
			break;
		case 11:
			if ($day > 30)
			{
				return "error";
			}
			$epoch = 304;
			break;
		case 12:
			if ($day > 31)
			{
				return "error";
			}
			$epoch = 334;
			break;
	}
	$epoch 		= $epoch + $day;
	$epoch_Y 	= sqrt(($birth_year - 1970) * ($birth_year - 1970));// sqrt() 计算平方根
	$leapyear	=round((($epoch_Y+2) / 4)-.5);// round() 函数对浮点数进行四舍五入
	
	if (($epoch_Y + 2)%4 == 0)
	{
		$leapyear--;
		if ($birth_year > 1970 && $month>=3)
		{
			$epoch = $epoch + 1;
		}
		if ($birth_year < 1970 && $month < 3)
		{	
			$epoch=$epoch-1;
		}
	} 
	else if ($month==2 && $day>28)
	{	
		return "error";
	}
	if ($birth_year > 1970)
	{
		$epoch = $epoch + $epoch_Y * 365 - 1 + $leapyear;
	}
	else
	{
		$epoch =$epoch - $epoch_Y * 365 - 1 - $leapyear;
	}
	return $epoch;
}

/**
* 取得实际时间
**/
function realdate($date_syntax="Ymd",$date=0)
{
	$i = 2;
	if ($date >= 0)
	{
	 	return create_date($date_syntax, $date * 86400 + 1, 0);
	}
	else
	{
		$year= -(date%1461);
		$days = $date + $year*1461;
		while ($days<0)
		{
			$year--;
			$days+=365;
			if ($i++==3)
			{
				$i=0;
				$days++;
			}
		}
	}
	$leap_year = ($i==0) ? TRUE : FALSE;
	$months_array = ($i==0) ?
		array (0,31,60,91,121,152,182,213,244,274,305,335,366) :
		array (0,31,59,90,120,151,181,212,243,273,304,334,365);
	for ($month=1;$month<12;$month++)
	{
		if ($days<$months_array[$month])
		{
			break;//结束循环
		}
	}

	$day=$days-$months_array[$month-1]+1;
	$date_str = array(
		'day_short' => array(
			'datetime' => array(
				'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
			)
		),
		'month_long' => array(
			'datetime' => array(
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
			),
		),
		'day_long' => array(
			'datetime' => array(
				'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
			),
		),
		'month_short' => array(
			'datetime' => array(
				'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
			),
		)
	);
	return strtr ($date_syntax, array(
		'a' => '',
		'A' => '',
		'\\d' => 'd',
		'd' => ($day>9) ? $day : '0'.$day,
		'\\D' => 'D',
		'D' => $date_str['day_short'][($date-3)%7],
		'\\F' => 'F',
		'F' => $date_str['month_long'][$month-1],
		'g' => '',
		'G' => '',
		'H' => '',
		'h' => '',
		'i' => '',
		'I' => '',
		'\\j' => 'j',
		'j' => $day,
		'\\l' => 'l',
		'l' => $date_str['day_long'][($date-3)%7],
		'\\L' => 'L',
		'L' => $leap_year,
		'\\m' => 'm',
		'm' => ($month>9) ? $month : '0'.$month,
		'\\M' => 'M',
		'M' => $date_str['month_short'][$month-1],
		'\\n' => 'n',
		'n' => $month,
		'O' => '',
		's' => '',
		'S' => '',
		'\\t' => 't',
		't' => $months_array[$month]-$months_array[$month-1],
		'w' => '',
		'\\y' => 'y',
		'y' => ($year>29) ? $year-30 : $year+70,
		'\\Y' => 'Y',
		'Y' => $year+1970,
		'\\z' => 'z',
		'z' => $days,
		'\\W' => '',
		'W' => '') );
}

 /**
 * 函数：get_pagination_start(每页显示记录)
 * 说明：初始化分页的一些参数
 * 返回：整型
 **/
function get_pagination_start($per)
{
	if ( isset($_POST['start1']) )
	{
		$start1 = abs(intval($_POST['start1']));
		$start1 = ($start1 < 1) ? 1 : $start1;
		$start = (($start1 - 1) * $per);
	}
	else
	{
		$start = ( isset($_GET['start']) ) ? intval($_GET['start']) : 0;
		$start = ($start < 0) ? 0 : $start;
	}
	
	return $start;
}

/**
* 设置文件目录的权限
*
* This function determines owner and group whom the file belongs to and user and group of PHP and then set safest possible file permissions.
* The function determines owner and group from common.php file and sets the same to the provided file.
* The function uses bit fields to build the permissions.
* The function sets the appropiate execute bit on directories.
*
* 支持常量代表位字段:
*
* CHMOD_ALL - 所有权限 (7)
* CHMOD_READ - 读取权限 (4)
* CHMOD_WRITE - 写入权限 (2)
* CHMOD_EXECUTE - 执行权限 (1)
*
* 注意: 该功能使用了 POSIX 扩展和 fileowner()、filegroup()函数. 如果其中任何被禁用, 此功能需要建立适当的权限, 通过调用 is_readable() 和 is_writable() 函数.
*
* @参数 字符串	$filename	文件或目录
* @参数 整数	$perms		权限设置
*
* @返回 布尔值	true 为成功, 否则 false
* @作者 faw, phpBB Group
*/
function phpbb_chmod($filename, $perms = CHMOD_READ)
{
	static $_chmod_info;

	// 如果不存在，直接返回 false
	if (!file_exists($filename))
	{
		return false;
	}

	// Determine some common vars
	if (empty($_chmod_info))
	{
		if (!function_exists('fileowner') || !function_exists('filegroup'))
		{
			// No need to further determine owner/group - it is unknown
			$_chmod_info['process'] = false;
		}
		else
		{

			// Determine owner/group of common.php file and the filename we want to change here
			$common_php_owner = @fileowner(ROOT_PATH . 'common.php');
			$common_php_group = @filegroup(ROOT_PATH . 'common.php');

			// And the owner and the groups PHP is running under.
			$php_uid = (function_exists('posix_getuid')) ? @posix_getuid() : false;
			$php_gids = (function_exists('posix_getgroups')) ? @posix_getgroups() : false;

			// If we are unable to get owner/group, then do not try to set them by guessing
			if (!$php_uid || empty($php_gids) || !$common_php_owner || !$common_php_group)
			{
				$_chmod_info['process'] = false;
			}
			else
			{
				$_chmod_info = array(
					'process'		=> true,
					'common_owner'	=> $common_php_owner,
					'common_group'	=> $common_php_group,
					'php_uid'		=> $php_uid,
					'php_gids'		=> $php_gids,
				);
			}
		}
	}

	if ($_chmod_info['process'])
	{
		$file_uid = @fileowner($filename);
		$file_gid = @filegroup($filename);

		// Change owner
		if (@chown($filename, $_chmod_info['common_owner']))
		{
			clearstatcache();
			$file_uid = @fileowner($filename);
		}

		// Change group
		if (@chgrp($filename, $_chmod_info['common_group']))
		{
			clearstatcache();
			$file_gid = @filegroup($filename);
		}

		// If the file_uid/gid now match the one from common.php we can process further, else we are not able to change something
		if ($file_uid != $_chmod_info['common_owner'] || $file_gid != $_chmod_info['common_group'])
		{
			$_chmod_info['process'] = false;
		}
	}

	// Still able to process?
	if ($_chmod_info['process'])
	{
		if ($file_uid == $_chmod_info['php_uid'])
		{
			$php = 'owner';
		}
		else if (in_array($file_gid, $_chmod_info['php_gids']))
		{
			$php = 'group';
		}
		else
		{
			// Since we are setting the everyone bit anyway, no need to do expensive operations
			$_chmod_info['process'] = false;
		}
	}

	// We are not able to determine or change something
	if (!$_chmod_info['process'])
	{
		$php = 'other';
	}

	// Owner always has read/write permission
	$owner = CHMOD_READ | CHMOD_WRITE;
	if (is_dir($filename))
	{
		$owner |= CHMOD_EXECUTE;

		// Only add execute bit to the permission if the dir needs to be readable
		if ($perms & CHMOD_READ)
		{
			$perms |= CHMOD_EXECUTE;
		}
	}

	switch ($php)
	{
		case 'owner':
			$result = @chmod($filename, ($owner << 6) + (0 << 3) + (0 << 0));

			clearstatcache();

			if (is_readable($filename) && phpbb_is_writable($filename))
			{
				break;
			}

		case 'group':
			$result = @chmod($filename, ($owner << 6) + ($perms << 3) + (0 << 0));

			clearstatcache();

			if ((!($perms & CHMOD_READ) || is_readable($filename)) && (!($perms & CHMOD_WRITE) || phpbb_is_writable($filename)))
			{
				break;
			}

		case 'other':
			$result = @chmod($filename, ($owner << 6) + ($perms << 3) + ($perms << 0));

			clearstatcache();

			if ((!($perms & CHMOD_READ) || is_readable($filename)) && (!($perms & CHMOD_WRITE) || phpbb_is_writable($filename)))
			{
				break;
			}

		default:
			return false;
		break;
	}

	return $result;
}

/**
* 删除文件或目录
* @参数 字符串 $dir_name 目录
* @返回 删除成功返回 true 否则 false
**/
function phpbb_deldir($dir_name)
{
	
	$dir = opendir($dir_name); 	
	
	while ( $file = readdir($dir) )
	{ 
		if( ($file != '.') && ($file != '..') )
		{ 
			$fullpath = $dir_name . '/' . $file; 
			if( !is_dir($fullpath) )
			{ 
				unlink($fullpath); 
			}
			else
			{ 
				phpbb_deldir($fullpath); 
			}	 
		} 
	} 
	
	closedir($dir);
	
	if( rmdir($dir_name) )
	{ 
		return true;
	}
	else
	{
		return false;
	}
}

function _set_var(&$result, $var, $type, $multibyte = false)
{
	settype($var, $type);
	$result = $var;

	if ($type == 'string')
	{
		$result = trim(htmlspecialchars(str_replace(array("\r\n", "\r", '\xFF'), array("\n", "\n", ' '), $result)));
		$result = stripslashes($result);
		if ($multibyte)
		{
			$result = preg_replace('#&amp;(\#[0-9]+;)#', '&\1', $result);
		}
	}
}

/**
* 取得 GET 或 POST 的值
* @参数 字符串 $var_name GET 或 POST 的变量名
* @参数 所有 $default 默认值
* @参数 布尔值 $multibyte 同时获得多个值，默认false，true为开启 
**/
function get_var($var_name, $default, $multibyte = false)
{
	global $_POST, $_GET;

	$request_var = (isset($_POST[$var_name])) ? $_POST : $_GET;

	if (!isset($request_var[$var_name]) || (is_array($request_var[$var_name]) && !is_array($default)) || (is_array($default) && !is_array($request_var[$var_name])))
	{
		return (is_array($default)) ? array() : $default;
	}

	$var = $request_var[$var_name];

	if (!is_array($default))
	{
		$type = gettype($default);
	}
	else
	{
		list($key_type, $type) = each($default);
		$type = gettype($type);
		$key_type = gettype($key_type);
	}

	if (is_array($var))
	{
		$_var = $var;
		$var = array();

		foreach ($_var as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $_k => $_v)
				{
					_set_var($k, $k, $key_type);
					_set_var($_k, $_k, $key_type);
					_set_var($var[$k][$_k], $_v, $type, $multibyte);
				}
			}
			else
			{
				_set_var($k, $k, $key_type);
				_set_var($var[$k], $v, $type, $multibyte);
			}
		}
	}
	else
	{
		_set_var($var, $var, $type, $multibyte);
	}
		
	return $var;
}

/*
* 伪静态的替换规则
*/
function phpbb_replace_mod_rewrite(&$ob_contents) 
{ 
	$urlin = array(
		"'viewtopic.php\?p=([1-9][0-9]{0,7})#([1-9][0-9]{0,7})(?!\&amp;)'", 
		"'viewtopic.php\?p=([1-9][0-9]{0,7})(?!\&amp;)'",
		"'viewtopic.php\?t=([1-9][0-9]{0,7})(?!\&amp;)'",
		"'viewforum.php\?f=([1-9][0-9]{0,7})(?!\&amp;)'",
		"'forum.php?c=([1-9][0-9]{0,7})(?!\&amp;)'",
	); 
	$urlout = array( 
		"viewpost-\\1-\\2.html",
		"viewposts-\\1.html",
		"topic-\\1.html",
		"forum-\\1.html",
		"forum-cat-\\1.html"
	); 
	
	$new_ob_contents = preg_replace($urlin, $urlout, $ob_contents); 
	
	return $new_ob_contents; 
}

/*
* @功能
* 虽然这样会增加数据库的负担，但是这样不会出现部分链接到用户中心时提示用户不存在
*/
function phpbb_message_at_link($message)
{
	preg_match_all("!(@|＠)([\\x{4e00}-\\x{9fa5}A-Za-z0-9_\\-]{1,12})(\x20|&nbsp;|<|\xC2\xA0|\r|\n|\x03|\t|,|\\?|\\!|:|;|，|。|？|！|：|；|、|…|$)!ue", $message, $matches);
	
	if (is_array($matches[2]))
	{
		foreach ($matches[2] as $username)
		{
			if ( !$row = get_userdata($username, true))
			{
				continue;
			}
			
			$message = str_replace('@' . $row['username'], '@<a href="' . append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $row['user_id']) . '">' . $row['username'] . '</a>', $message);
			
		}
	}
	
	return $message;
}

function phpbb_message_at($message)
{
	global $db, $userdata, $user_ip;
	
	$at_url = basename($_SERVER['PHP_SELF']);
	
	if ($_POST[POST_TOPIC_URL] != '')
	{
		$at_url = 'viewtopic.php?t=' . abs(intval($_POST[POST_TOPIC_URL]));
	}
	else
	{
		$at_p = abs(intval($_POST[POST_POST_URL]));
		$at_url = 'viewtopic.php?p=' . $at_p . '#' . $at_p;
	}

	preg_match_all("!(@|＠)([\\x{4e00}-\\x{9fa5}A-Za-z0-9_\\-]{1,12})(\x20|&nbsp;|<|\xC2\xA0|\r|\n|\x03|\t|,|\\?|\\!|:|;|，|。|？|！|：|；|、|…|$)!ue", $message, $matches);

	$atuser = array_count_values($matches[2]);

	$atuser = array_keys($atuser);//删除重复值

	for ($i = 0; $i < count($atuser); $i++)
	{

		if ( !$row = get_userdata($atuser[$i], true))
		{
			continue;//用户不存在，跳过这次循环
		}

		$to_userdata = $row['user_id'];

		$msg_time = time();

		$sql_info = 'INSERT INTO ' . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip) 
			VALUES (" . PRIVMSGS_NEW_MAIL . ", '系统@消息', " . $userdata['user_id'] . ", " . $row['user_id'] . ", $msg_time, '$user_ip')";
		
		if ( !$db->sql_query($sql_info) )
		{
			trigger_error('无法插入数据到信息表', E_USER_WARNING);
		}

		$sql = 'SELECT privmsgs_id 
			FROM ' . PRIVMSGS_TABLE . ' 
			WHERE privmsgs_id = (SELECT MAX(privmsgs_id) FROM ' . PRIVMSGS_TABLE . ')';

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('无法获取最后privmsgs_id', E_USER_WARNING);
		}

		$msgrow = $db->sql_fetchrow($result);

		$sql = 'UPDATE ' . USERS_TABLE . ' 
			SET user_new_privmsg = user_new_privmsg + 1 
			WHERE user_id = ' . $row['user_id'];

		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法更新 ' . USERS_TABLE . ' 表', E_USER_WARNING);
		}

		$privmsg_sent_id = $msgrow['privmsgs_id'];

		$at_message = '您收到一条来自系统的@消息，您可以点击 <a href="' . $at_url . '">这里</a> 查看';

		$sql = 'INSERT INTO ' . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_text)
		   VALUES ($privmsg_sent_id, '" . str_replace("\'", "''", $at_message) . "')";

		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法插入数据到 privmsgs_text', E_USER_WARNING);
		}

	}

	return $message;
}

/**
* 	对风格安装包的cfg文件进行解析
*	@参数 字符串 $filename 文件名
*	@参数 数组 $lines 这个貌似对于现在没什么用处
*/
function parse_cfg_file($filename, $lines = false)
{
	$parsed_items = array();

	if ($lines === false)
	{
		$lines = file($filename);
	}

	if (is_array($lines))
	{
		foreach ($lines as $line)
		{
			$line = trim($line);

			if (!$line || $line[0] == '#' || ($delim_pos = strpos($line, '=')) === false)
			{
				continue;
			}

			// Determine first occurrence, since in values the equal sign is allowed
			$key = strtolower(trim(substr($line, 0, $delim_pos)));
			$value = trim(substr($line, $delim_pos + 1));

			if (in_array($value, array('off', 'false', '0')))
			{
				$value = false;
			}
			else if (in_array($value, array('on', 'true', '1')))
			{
				$value = true;
			}
			else if (!trim($value))
			{
				$value = '';
			}
			else if (($value[0] == "'" && $value[sizeof($value) - 1] == "'") || ($value[0] == '"' && $value[sizeof($value) - 1] == '"'))
			{
				$value = substr($value, 1, sizeof($value)-2);
			}

			$parsed_items[$key] = $value;
		}
	}
	
	if (isset($parsed_items['inherit_from']) && isset($parsed_items['name']) && $parsed_items['inherit_from'] == $parsed_items['name'])
	{
		unset($parsed_items['inherit_from']);
	}

	return $parsed_items;
}

function obtain_images_data()
{
	global $template;

	$filename = $template->root . 'imageset.cfg';

	if (!file_exists($filename))
	{
		trigger_error("配置文件 $filename 不存在", E_USER_WARNING);
	}

	$image_array = array();
	$newdata = parse_cfg_file($filename);

	foreach ($newdata as $image_name => $value)
	{
		if (strpos($value, '*') !== false)
		{
			if (substr($value, -1, 1) === '*')
			{
				list($image_filename, $image_height) = explode('*', $value);
				$image_width = 0;
			}
			else
			{
				list($image_filename, $image_height, $image_width) = explode('*', $value);
			}
		}
		else
		{
			$image_filename = $value;
			$image_height = $image_width = 0;
		}

		if (strpos($image_name, 'img_') === 0 && $image_filename)
		{
			$image_name = substr($image_name, 4);
			$image_array[$image_name] = array(
				'image_filename'	=> (string) $image_filename,
				'image_height'		=> (int) $image_height,
				'image_width'		=> (int) $image_width
			);
		}
	}				

	return $image_array;
}

/*
* 生成网站的图标
*/
function make_style_image($image_name, $image_title = '', $image_alt = '.', $other_property = '')
{
	global $images, $template;

	$image = '<img src="" alt="."/>';
	
	if (empty($images))
	{
		$image = '<img src="" alt="."/>';
	}

	if(isset($images[$image_name]))
	{
		$image = '<img src="' . $template->root . 'images/' . $images[$image_name]['image_filename'] 
		. '" width="' . $images[$image_name]['image_width'] 
		. '" height="' . $images[$image_name]['image_height'] 
		. '" title="' . $image_title 
		. '" alt="' . $image_alt
		. '" ' . $other_property . '/>';
	}

	return $image;
}

/**
*	黑名单
*/
function session_userban($user_ip, $user_id)
{
	global $db, $userdata;

	preg_match('/(..)(..)(..)(..)/', $user_ip, $user_ip_parts);

	$sql = "SELECT ban_ip, ban_userid, ban_email 
		FROM " . BANLIST_TABLE . " 
		WHERE ban_ip IN ('" . $user_ip_parts[1] . $user_ip_parts[2] . $user_ip_parts[3] . $user_ip_parts[4] . "', '" . $user_ip_parts[1] . $user_ip_parts[2] . $user_ip_parts[3] . "ff', '" . $user_ip_parts[1] . $user_ip_parts[2] . "ffff', '" . $user_ip_parts[1] . "ffffff')
			OR ban_userid = $user_id";

	if ( $user_id != ANONYMOUS )
	{
		$sql .= " OR ban_email LIKE '" . str_replace("\'", "''", $userdata['user_email']) . "' 
			OR ban_email LIKE '" . substr(str_replace("\'", "''", $userdata['user_email']), strpos(str_replace("\'", "''", $userdata['user_email']), "@")) . "'";
	}
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法获得黑名单信息', E_USER_WARNING);
	}

	$ban_information = '';
	
	if($db->sql_numrows($result))
	{
		$i=0;
		while ($ban_info = $db->sql_fetchrow($result))
		{
			if ($ban_info['ban_ip'])
			{
				$ban_information .= '<p class="red">您的IP（' . $ban_info['ban_ip'] . '）已被列为黑名单</p>';
			}

			if ($ban_info['ban_userid'])
			{
				$ban_information .= '<p class="red">你已被列为黑名单, 详情请咨询系统管理员</p>';
			}

			if ($ban_info['ban_email'])
			{
				$ban_information .= '<p class="red">您的E-mail（' . $ban_info['ban_email'] . '）已被列为黑名单</p>';
			}
			$i++;
		}
	}
	else
	{
		return false;
	}

	return $ban_information;
}

/**
*	append_sid
*/
function append_sid($url, $non_html_amp = false)
{
	global $session;

	if ( !empty($session->SID) && !preg_match('#sid=#', $url) )
	{
		$url .= ( ( strpos($url, '?') !== false ) ?  ( ( $non_html_amp ) ? '&' : '&amp;' ) : '?' ) . $session->SID;
	}

	return $url;
}

/**
*	设置config表的信息
*	@参数 $config_name 名称
*	@参数 $config_value 值
*/
function set_config($config_name, $config_value)
{
	global $db, $cache;

	$sql = 'UPDATE ' . CONFIG_TABLE . " 
		SET config_value = '$config_value'
		WHERE config_name = '$config_name'";

	if (!$db->sql_query($sql))
	{
		trigger_error("无法更新 $config_name 的值", E_USER_WARNING);
	}

	return true;
}

/**
*	在页面中生成一个导航栏
*/
function page_jump()
{
	global $template, $userdata;

	$template->set_filenames(array(
		'jump' => 'jump_body.tpl')
	);

	$template->assign_vars(array(
		'U_UCP'				=> append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $userdata['user_id']),
		'U_MYPOST'			=> append_sid('search.php?search_author=' . $userdata['username'] . '&ucp'),
		'U_MYTOPIC'			=> append_sid('search.php?search_author=' . $userdata['username'] . '&mode=all_topics&ucp'),
		'U_MYUPLOAD'		=> append_sid('ucp.php?mode=viewfiles&' . POST_USERS_URL . '=' . $userdata['user_id']),
		'U_FORUM'			=> append_sid('forum.php'),
		'U_ARTICLE'			=> append_sid('article.php'),
		'U_MODS'			=> append_sid('mods.php'),
		'U_INDEX'			=> append_sid('index.php'))
	);

	$template->assign_var_from_handle('PAGE_JUMP', 'jump');
}

/**
*	把错误参数注册在模版中
*	@参数 字符串 $assign_tag 注册的标签，建议设置大写
*	@参数 字符串 $error_message 错误的内容
*/
function error_box($assign_tag, $error_message)
{
	global $template;
	
	$template->set_filenames(array('error_box' => 'error_body.tpl'));
	
	$template->assign_var('ERROR_MESSAGE', $error_message);
	
	$template->assign_var_from_handle($assign_tag, 'error_box');
}

/*
*	创建确认操作页面
*	该函数执行后会退出脚本执行操作
*	@参数 字符串 $page_title 页面的标题
*	@参数 字符串 $confirm_title 确认提示标题
*	@参数 字符串 $confirm_message 确认提示语
*	@参数 字符串 $confirm_action 确认的提交地址
*	@参数 字符串 $confirm_hidden 确认的其它参数	
*/
function confirm_box($page_title, $confirm_title, $confirm_message, $confirm_action, $confirm_hidden = '')
{
	global $template;

	page_header($page_title);
	
	$template->set_filenames(array(
		'confirm' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' 	=> $confirm_title,
		'MESSAGE_TEXT' 		=> $confirm_message,
		'L_YES' 			=> '是',
		'L_NO' 				=> '否',
		'S_CONFIRM_ACTION' 	=> $confirm_action,
		'S_HIDDEN_FIELDS' 	=> $confirm_hidden)
	);

	$template->pparse('confirm');

	page_footer();
}

/**
*	移除魔术引号并进行编码
*	@参数 字符串 $text 要编码的字符串
*/
function magic_quotes($text)
{
	return (MAGIC_QUOTES) ? htmlspecialchars(stripslashes($text), ENT_QUOTES) : htmlspecialchars($text, ENT_QUOTES);
}

/**
*	把编码过的字符串转为实体
*	@参数 字符串 $decode_text 转换为实体的字符串
*/
function decode_char($decode_text)
{
	return htmlspecialchars_decode($decode_text, ENT_QUOTES);
}

/**
*	生成登录后跳转到原页面的地址
*	参数 字符串 $url 登录后跳转的地址
*/
function login_back($url, $is_url = false)
{
	$encode_url = urlencode($url);

	if ($is_url)
	{
		return append_sid('login.php?redirect=' . $encode_url, true);
	}
	
	redirect(append_sid('login.php?redirect=' . $encode_url, true));
}
?>