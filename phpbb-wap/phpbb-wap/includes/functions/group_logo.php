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

function check_image_type(&$type, &$error, &$error_msg)
{

	switch( $type )
	{
		case 'jpeg':
		case 'pjpeg':
		case 'jpg':
			return '.jpg';
			break;
		case 'gif':
			return '.gif';
			break;
		case 'png':
			return '.png';
			break;
		default:
			$error = true;
			$error_msg = '<p>头像文件必须是.jpg（jpeg、pjpeg） .gif或.png</p>';
			break;
	}

	return false;
}

function user_logo_upload($mode, $avatar_mode, &$current_avatar, $current_type, &$error, &$error_msg, $avatar_filename, $avatar_realname, $avatar_filesize, $avatar_filetype)
{
	global $board_config, $db;
	
	$ini_val = ( @phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';

	$width = $height = 0;
	$type = '';

	if ( $avatar_mode == 'remote' && preg_match('/^(http:\/\/)?([\w\-\.]+)\:?([0-9]*)\/([^ \?&=\#\"\n\r\t<]*?(\.(jpg|jpeg|gif|png)))$/', $avatar_filename, $url_ary) )
	{
		if ( empty($url_ary[4]) )
		{
			$error = true;
			$error_msg = '<p> 您输入的 URL 不完整 </p>';
			return;
		}

		$base_get = '/' . $url_ary[4];
		$port = ( !empty($url_ary[3]) ) ? $url_ary[3] : 80;

		if ( !($fsock = @fsockopen($url_ary[2], $port, $errno, $errstr)) )
		{
			$error = true;
			$error_msg = '<p>无法连接到您指定的 URL</p>';
			return;
		}

		@fputs($fsock, "GET $base_get HTTP/1.1\r\n");
		@fputs($fsock, "HOST: " . $url_ary[2] . "\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");

		unset($avatar_data);
		while( !@feof($fsock) )
		{
			$avatar_data .= @fread($fsock, $board_config['avatar_filesize']);
		}
		@fclose($fsock);

		if (!preg_match('#Content-Length\: ([0-9]+)[^ /][\s]+#i', $avatar_data, $file_data1) || !preg_match('#Content-Type\: image/[x\-]*([a-z]+)[\s]+#i', $avatar_data, $file_data2))
		{
			$error = true;
			$error_msg = '<p>您指定的 URL 没有包含相关的文件数据</p>';
			return;
		}

		$avatar_filesize = $file_data1[1]; 
		$avatar_filetype = $file_data2[1]; 

		if ( !$error && $avatar_filesize > 0 && $avatar_filesize < $board_config['avatar_filesize'] )
		{
			$avatar_data = substr($avatar_data, strlen($avatar_data) - $avatar_filesize, $avatar_filesize);
			$tmp_path = ( !@$ini_val('safe_mode') ) ? '/tmp' : './images/group_logo/tmp';
			$tmp_filename = tempnam($tmp_path, uniqid(rand()) . '-');

			$fptr = @fopen($tmp_filename, 'wb');
			$bytes_written = @fwrite($fptr, $avatar_data, $avatar_filesize);
			@fclose($fptr);

			if ( $bytes_written != $avatar_filesize )
			{
				@unlink($tmp_filename);
				trigger_error('Could not write avatar file to local storage. Please contact the board administrator with this message', E_USER_WARNING);
			}

			list($width, $height, $type) = @getimagesize($tmp_filename);
		}
		else
		{
			$error = true;
			$error_msg .= '<p>Logo文件不大于 ' . round($board_config['avatar_filesize'] / 1024) . ' KB <p>';
		}
	}
	else if ( ( file_exists(@phpbb_realpath($avatar_filename)) ) && preg_match('/\.(jpg|jpeg|gif|png)$/i', $avatar_realname) )
	{
		if ( $avatar_filesize <= $board_config['avatar_filesize'] && $avatar_filesize > 0 )
		{
			preg_match('#image\/[x\-]*([a-z]+)#', $avatar_filetype, $avatar_filetype);
			$avatar_filetype = $avatar_filetype[1];
		}
		else
		{
			$error = true;
			$error_msg .= '<p>文件不大于 ' . round($board_config['avatar_filesize'] / 1024) . ' KB <p>';
			return;
		}

		list($width, $height, $type) = @getimagesize($avatar_filename);
	}

	if ( !($imgtype = check_image_type($avatar_filetype, $error, $error_msg)) )
	{
		return;
	}

	switch ($type)
	{
		case 1:
			if ($imgtype != '.gif')
			{
				@unlink($tmp_filename);
				trigger_error('Unable to upload file', E_USER_WARNING);
			}
		break;

		case 2:
		case 9:
		case 10:
		case 11:
		case 12:
			if ($imgtype != '.jpg' && $imgtype != '.jpeg')
			{
				@unlink($tmp_filename);
				trigger_error('Unable to upload file', E_USER_WARNING);
			}
		break;

		case 3:
			if ($imgtype != '.png')
			{
				@unlink($tmp_filename);
				trigger_error('Unable to upload file', E_USER_WARNING);
			}
		break;

		default:
			@unlink($tmp_filename);
			trigger_error('Unable to upload file', E_USER_WARNING);
	}

	if ( $width > 0 && $height > 0 && $width <= $board_config['avatar_max_width'] && $height <= $board_config['avatar_max_height'] )
	{
		$new_filename = uniqid(rand()) . $imgtype;
		$board_avatar_path = 'images/group_logo';
		if( $avatar_mode == 'remote' )
		{
			@copy($tmp_filename, './' . $board_avatar_path . "/$new_filename");
			@unlink($tmp_filename);
		}
		else
		{
			if (!is_uploaded_file($avatar_filename) && !$result_ua)
			{
				trigger_error('Unable to upload file', E_USER_WARNING);
			}
			@copy($avatar_filename, './' . $board_avatar_path . "/$new_filename");

			if ($result_ua)
			{
				@unlink($avatar_filename);
			}
		}

		@chmod('./' . $board_avatar_path . "/$new_filename", 0777);

		$avatar_sql = "group_logo = '$new_filename'";
	}
	else
	{
		$l_avatar_size = '不得超过 ' . $board_config['avatar_max_width'] . ' 像素宽和 ' . $board_config['avatar_max_height'] . ' 像素高';
		$error = true;
		$error_msg = ( !empty($error_msg) ) ? $error_msg . '<br />' . $l_avatar_size : $l_avatar_size;
	}

	return $avatar_sql;
}

?>