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

if ( !defined('IN_PHPBB') )
{
	exit;
}

if (!function_exists('html_entity_decode'))
{
	function html_entity_decode($given_html, $quote_style = ENT_QUOTES)
	{
		$trans_table = array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style));
		$trans_table['&#39;'] = "'";
		return (strtr($given_html, $trans_table));
	}
}

function base64_pack($number) 
{ 
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';
	$base = strlen($chars);

	if ($number > 4096)
	{
		return;
	}
	else if ($number < $base)
	{
		return $chars[$number];
	}
	
	$hexval = '';
	
	while ($number > 0) 
	{ 
		$remainder = $number%$base;
	
		if ($remainder < $base)
		{
			$hexval = $chars[$remainder] . $hexval;
		}

		$number = floor($number/$base); 
	} 

	return $hexval; 
}

function base64_unpack($string)
{
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';
	$base = strlen($chars);

	$length = strlen($string); 
	$number = 0; 

	for($i = 1; $i <= $length; $i++)
	{ 
		$pos = $length - $i; 
		$operand = strpos($chars, substr($string,$pos,1));
		$exponent = pow($base, $i-1); 
		$decValue = $operand * $exponent; 
		$number += $decValue; 
	} 

	return $number; 
}

function auth_pack($auth_array)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';
	$one_char = $two_char = false;
	$auth_cache = '';
	
	for ($i = 0; $i < sizeof($auth_array); $i++)
	{
		$val = base64_pack(intval($auth_array[$i]));
		if (strlen($val) == 1 && !$one_char)
		{
			$auth_cache .= $one_char_encoding;
			$one_char = true;
		}
		else if (strlen($val) == 2 && !$two_char)
		{		
			$auth_cache .= $two_char_encoding;
			$two_char = true;
		}
		
		$auth_cache .= $val;
	}

	return $auth_cache;
}

function auth_unpack($auth_cache)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';

	$auth = array();
	$auth_len = 1;
	
	for ($pos = 0; $pos < strlen($auth_cache); $pos += $auth_len)
	{
		$forum_auth = substr($auth_cache, $pos, 1);
		if ($forum_auth == $one_char_encoding)
		{
			$auth_len = 1;
			continue;
		}
		else if ($forum_auth == $two_char_encoding)
		{
			$auth_len = 2;
			$pos--;
			continue;
		}
		
		$forum_auth = substr($auth_cache, $pos, $auth_len);
		$forum_id = base64_unpack($forum_auth);
		$auth[] = intval($forum_id);
	}
	return $auth;
}

function is_forum_authed($auth_cache, $check_forum_id)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';

	if (trim($auth_cache) == '')
	{
		return true;
	}

	$auth = array();
	$auth_len = 1;
	
	for ($pos = 0; $pos < strlen($auth_cache); $pos+=$auth_len)
	{
		$forum_auth = substr($auth_cache, $pos, 1);
		if ($forum_auth == $one_char_encoding)
		{
			$auth_len = 1;
			continue;
		}
		else if ($forum_auth == $two_char_encoding)
		{
			$auth_len = 2;
			$pos--;
			continue;
		}
		
		$forum_auth = substr($auth_cache, $pos, $auth_len);
		$forum_id = (int) base64_unpack($forum_auth);
		if ($forum_id == $check_forum_id)
		{
			return true;
		}
	}
	return false;
}

function attach_init_ftp($mode = false)
{
	global $board_config;

	$server = (trim($board_config['ftp_server']) == '') ? 'localhost' : trim($board_config['ftp_server']);
	
	$ftp_path = ($mode == MODE_THUMBNAIL) ? trim($board_config['ftp_path']) . '/' . THUMB_DIR : trim($board_config['ftp_path']);

	$conn_id = @ftp_connect($server);

	if (!$conn_id)
	{

		trigger_error('无法链接到FTP服务器::' . $server, E_USER_ERROR);
	}

	$login_result = @ftp_login($conn_id, $board_config['ftp_user'], $board_config['ftp_pass']);

	if (!$login_result)
	{
		trigger_error('无法登录FTP服务器::' . $board_config['ftp_user'], E_USER_ERROR);
	}
		
	if (!@ftp_pasv($conn_id, intval($board_config['ftp_pasv_mode'])))
	{
		trigger_error('被动模式错误', E_USER_ERROR);
	}
	
	$result = @ftp_chdir($conn_id, $ftp_path);

	if (!$result)
	{
		trigger_error('FTP路径出错::' . $ftp_path, E_USER_ERROR);
	}

	return $conn_id;
}

function unlink_attach($filename, $mode = false)
{
	global $upload_dir, $board_config;

	$filename = basename($filename);
	
	if (!intval($board_config['allow_ftp_upload']))
	{
		if ($mode == MODE_THUMBNAIL)
		{
			$filename = $upload_dir . '/' . THUMB_DIR . '/t_' . $filename;
		}
		else
		{
			$filename = $upload_dir . '/' . $filename;
		}

		$deleted = @unlink($filename);
	}
	else
	{
		$conn_id = attach_init_ftp($mode);

		if ($mode == MODE_THUMBNAIL)
		{
			$filename = 't_' . $filename;
		}
		
		$res = @ftp_delete($conn_id, $filename);
		if (!$res)
		{
			$add = ($mode == MODE_THUMBNAIL) ? '/' . THUMB_DIR : ''; 
			trigger_error('FTP无法删除文件夹的文件::' . $board_config['ftp_path'] . $add, E_USER_ERROR);

			return $deleted;
		}

		@ftp_quit($conn_id);

		$deleted = true;
	}

	return $deleted;
}

function ftp_file($source_file, $dest_file, $mimetype, $disable_error_mode = false)
{
	global $board_config, $error, $error_msg;

	$conn_id = attach_init_ftp();

	$mode = FTP_BINARY;
	if (preg_match("/text/i", $mimetype) || preg_match("/html/i", $mimetype))
	{
		$mode = FTP_ASCII;
	}

	$res = @ftp_put($conn_id, $dest_file, $source_file, $mode);

	if (!$res && !$disable_error_mode)
	{
		$error = true;
		if (!empty($error_msg))
		{
			$error_msg .= '<br />';
		}
		$error_msg = 'FTP无法删除文件到' . $board_config['ftp_path'] . '目录<br />';
		@ftp_quit($conn_id);
		return false;
	}

	if (!$res)
	{
		return false;
	}

	@ftp_site($conn_id, 'CHMOD 0644 ' . $dest_file);
	@ftp_quit($conn_id);
	return true;
}

function attachment_exists($filename)
{
	global $upload_dir, $board_config;

	$filename = basename($filename);

	if (!intval($board_config['allow_ftp_upload']))
	{
		if (!@file_exists(@phpbb_realpath($upload_dir . '/' . $filename)))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		$found = false;

		$conn_id = attach_init_ftp();

		$file_listing = array();

		$file_listing = @ftp_rawlist($conn_id, $filename);

		for ($i = 0, $size = sizeof($file_listing); $i < $size; $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd') 
				{	
					$dirinfo[0] = 1;
				}
				$dirinfo[1] = $regs[2]; 
				$dirinfo[2] = $regs[3]; 
				$dirinfo[3] = $regs[4];
				$dirinfo[4] = $regs[5]; 
			}
			
			if ($dirinfo[0] != 1 && $dirinfo[4] == $filename)
			{
				$found = true;
			}
		}

		@ftp_quit($conn_id);	
		
		return $found;
	}
}

function thumbnail_exists($filename)
{
	global $upload_dir, $board_config;

	$filename = basename($filename);

	if (!intval($board_config['allow_ftp_upload']))
	{
		if (!@file_exists(@phpbb_realpath($upload_dir . '/' . THUMB_DIR . '/t_' . $filename)))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		$found = false;

		$conn_id = attach_init_ftp(MODE_THUMBNAIL);

		$file_listing = array();

		$filename = 't_' . $filename;
		$file_listing = @ftp_rawlist($conn_id, $filename);

		for ($i = 0, $size = sizeof($file_listing); $i < $size; $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd')
				{	
					$dirinfo[0] = 1;
				}
				$dirinfo[1] = $regs[2]; 
				$dirinfo[2] = $regs[3]; 
				$dirinfo[3] = $regs[4]; 
				$dirinfo[4] = $regs[5]; 
			}
			
			if ($dirinfo[0] != 1 && $dirinfo[4] == $filename)
			{
				$found = true;
			}
		}

		@ftp_quit($conn_id);	
		
		return $found;
	}
}

function physical_filename_already_stored($filename)
{
	global $db;

	if ($filename == '')
	{
		return false;
	}

	$filename = basename($filename);

	$sql = 'SELECT attach_id 
		FROM ' . ATTACHMENTS_DESC_TABLE . "
		WHERE physical_filename = '" . $db->sql_escape($filename) . "' 
		LIMIT 1";

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get attachment information for filename: ' . htmlspecialchars($filename), E_USER_WARNING);
	}
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	return ($num_rows == 0) ? false : true;
}

function attachment_exists_db($post_id, $page = 0)
{
	global $db;

	$post_id = (int) $post_id;

	$sql_id = 'post_id';

	$sql = 'SELECT attach_id
		FROM ' . ATTACHMENTS_TABLE . "
		WHERE $sql_id = $post_id 
		LIMIT 1";

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get attachment informations for specific posts', E_USER_WARNING);
	}
	
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_rows > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_attachments_from_post($post_id_array)
{
	global $db, $board_config;

	$attachments = array();

	if (!is_array($post_id_array))
	{
		if (empty($post_id_array))
		{
			return $attachments;
		}

		$post_id = intval($post_id_array);

		$post_id_array = array();
		$post_id_array[] = $post_id;
	}

	$post_id_array = implode(', ', array_map('intval', $post_id_array));

	if ($post_id_array == '')
	{
		return $attachments;
	}

	$display_order = (intval($board_config['display_order']) == 0) ? 'DESC' : 'ASC';
	
	$sql = 'SELECT a.post_id, d.*
		FROM ' . ATTACHMENTS_TABLE . ' a, ' . ATTACHMENTS_DESC_TABLE . " d
		WHERE a.post_id IN ($post_id_array)
			AND a.attach_id = d.attach_id
		ORDER BY d.filetime $display_order";

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not get Attachment Informations for post number ' . $post_id_array, E_USER_WARNING);
	}
	
	$num_rows = $db->sql_numrows($result);
	$attachments = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	if ($num_rows == 0)
	{
		return array();
	}
		
	return $attachments;
}

/*
* 统计指定附件的大小
* @参数 数组 $attach_ids 附件的ID
*/
function get_total_attach_filesize($attach_ids)
{
	global $db;

	if (!is_array($attach_ids) || !sizeof($attach_ids))
	{
		return 0;
	}

	$attach_ids = implode(', ', array_map('intval', $attach_ids));

	if (!$attach_ids)
	{
		return 0;
	}

	$sql = 'SELECT filesize
		FROM ' . ATTACHMENTS_DESC_TABLE . "
		WHERE attach_id IN ($attach_ids)";

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query Total Filesize', E_USER_WARNING);
	}

	$total_filesize = 0;

	while ($row = $db->sql_fetchrow($result))
	{
		$total_filesize += (int) $row['filesize'];
	}
	$db->sql_freeresult($result);

	return $total_filesize;
}

/*
* 获取数据库中的扩展名信息
* 返回 （数组） $extensions 扩展名信息
*/
function get_extension_informations()
{
	global $db;

	$extensions = array();

	$sql = 'SELECT e.extension, g.cat_id, g.download_mode, g.upload_icon
		FROM ' . EXTENSIONS_TABLE . ' e, ' . EXTENSION_GROUPS_TABLE . ' g
		WHERE e.group_id = g.group_id
			AND g.allow_group = 1';
	
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not query Allowed Extensions.', E_USER_WARNING);
	}

	$extensions = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);
	
	return $extensions;
}

/*
* 同步帖子中的附件
* @参数 $topic_id 帖子的ID
*/
function attachment_sync_topic($topic_id)
{
	global $db;

	if (!$topic_id)
	{
		return;
	}

	$topic_id = (int) $topic_id;

	$sql = 'SELECT post_id 
		FROM ' . POSTS_TABLE . " 
		WHERE topic_id = $topic_id
		GROUP BY post_id";
		
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Couldn\'t select Post ID\'s', E_USER_WARNING);
	}

	$post_list = $db->sql_fetchrowset($result);
	$num_posts = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_posts == 0)
	{
		return;
	}
	
	$post_ids = array();

	for ($i = 0; $i < $num_posts; $i++)
	{
		$post_ids[] = intval($post_list[$i]['post_id']);
	}

	$post_id_sql = implode(', ', $post_ids);
	
	if ($post_id_sql == '')
	{
		return;
	}
	
	$sql = 'SELECT attach_id 
		FROM ' . ATTACHMENTS_TABLE . " 
		WHERE post_id IN ($post_id_sql) 
		LIMIT 1";
		
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Couldn\'t select Attachment ID\'s', E_USER_WARNING);
	}

	$set_id = ($db->sql_numrows($result) == 0) ? 0 : 1;

	$sql = 'UPDATE ' . TOPICS_TABLE . " SET topic_attachment = $set_id WHERE topic_id = $topic_id";

	if ( !($db->sql_query($sql)) )
	{
		trigger_error('Couldn\'t update Topics Table', E_USER_WARNING);
	}
		
	for ($i = 0; $i < sizeof($post_ids); $i++)
	{
		$sql = 'SELECT attach_id 
			FROM ' . ATTACHMENTS_TABLE . ' 
			WHERE post_id = ' . $post_ids[$i] . '
			LIMIT 1';

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Couldn\'t select Attachment ID\'s', E_USER_WARNING);
		}

		$set_id = ( $db->sql_numrows($result) == 0) ? 0 : 1;
		
		$sql = 'UPDATE ' . POSTS_TABLE . " SET post_attachment = $set_id WHERE post_id = {$post_ids[$i]}";

		if ( !($db->sql_query($sql)) )
		{
			trigger_error('Couldn\'t update Posts Table', E_USER_WARNING);
		}
	}
}

/*
* 获取文件的扩展名
* @参数 $filename 文件名
*/
function get_extension($filename)
{
	if (!stristr($filename, '.'))
	{
		return '';
	}

	$extension = strrchr(strtolower($filename), '.');
	$extension[0] = ' ';
	$extension = strtolower(trim($extension));
	
	if (is_array($extension))
	{
		return '';
	}
	else
	{
		return $extension;
	}
}

/*
* 获取不带扩展名的文件名称
* @参数 $filename 文件名
*/
function delete_extension($filename)
{
	return substr($filename, 0, strrpos(strtolower(trim($filename)), '.'));
}

function user_in_group($user_id, $group_id)
{
	global $db;

	$user_id = (int) $user_id;
	$group_id = (int) $group_id;

	if (!$user_id || !$group_id)
	{
		return false;
	}
	
	$sql = 'SELECT u.group_id 
		FROM ' . USER_GROUP_TABLE . ' u, ' . GROUPS_TABLE . " g 
		WHERE g.group_single_user = 0
			AND u.user_pending = 0
			AND u.group_id = g.group_id
			AND u.user_id = $user_id 
			AND g.group_id = $group_id
		LIMIT 1";
			
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get User Group', E_USER_WARNING);
	}

	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_rows == 0)
	{
		return false;
	}
	
	return true;
}

?>