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
	die('Hacking attempt');
}

$allowed_extensions	= array();
$display_categories	= array();
$download_modes 	= array();
$upload_icons 		= array();
$attachments 		= array();

/*
* 清除模板缓存编译
*/
function display_compile_cache_clear($filename, $template_var)
{
	global $template;
	
	if (isset($template->cachedir))
	{
		$filename = str_replace($template->root, '', $filename);
		if (substr($filename, 0, 1) == '/')
		{
			$filename = substr($filename, 1, strlen($filename));
		}

		if (file_exists($template->cachedir . $filename . '.php'))
		{
			@unlink($template->cachedir . $filename . '.php');
		}
	}

	return;
}

/*
* 创建所需的阵列扩展作业
*/
function init_complete_extensions_data()
{
	global $db, $allowed_extensions, $display_categories, $download_modes, $upload_icons;

	$extension_informations = get_extension_informations();
	$allowed_extensions = array();

	for ($i = 0, $size = count($extension_informations); $i < $size; $i++)
	{
		$extension = strtolower(trim($extension_informations[$i]['extension']));
		$allowed_extensions[] = $extension;
		$display_categories[$extension] = intval($extension_informations[$i]['cat_id']);
		$download_modes[$extension] = intval($extension_informations[$i]['download_mode']);
		$upload_icons[$extension] = trim($extension_informations[$i]['upload_icon']);
	}
}

/**
* 将数据写入到 Template 的 Vars 中
*/
function init_display_template($template_var, $replacement, $filename = 'viewtopic_attach_body.tpl')
{
	global $template;

	// 这个函数是改编自旧模板类
	// 我希望我从3.x的人有该功能。（这个类的岩石，不能等待使用它在MODS）

	//处理附件信息
	if (!isset($template->uncompiled_code[$template_var]) && empty($template->uncompiled_code[$template_var]))
	{
		//如果我们没有分配给此句柄的文件，终结。
		if (!isset($template->files[$template_var]))
		{
			die("Template->loadfile(): No file specified for handle $template_var");
		}

		$filename_2 = $template->files[$template_var];

		$str = implode('', @file($filename_2));
		if (empty($str))
		{
			die("Template->loadfile(): File $filename_2 for handle $template_var is empty");
		}

		$template->uncompiled_code[$template_var] = $str;
	}

	$complete_filename = $filename;
	if (substr($complete_filename, 0, 1) != '/')
	{
		$complete_filename = $template->root . $complete_filename;
	}


	if (!file_exists($complete_filename))
	{
		$complete_filename = $template->root . $filename;
		if( !file_exists($complete_filename) )
		{
			die("Template->make_filename(): Error - file $complete_filename does not exist");
		}

	}
	
	$content = implode('', file($complete_filename));
	if (empty($content))
	{
		die('Template->loadfile(): File ' . $complete_filename . ' is empty');
	}
	
	// 替换 $replacement 在未编译的代码到 $filename
	$template->uncompiled_code[$template_var] = str_replace($replacement, $content, $template->uncompiled_code[$template_var]);
	// 强制刷新缓存的版本
	display_compile_cache_clear($template->files[$template_var], $template_var);
}

/**
* BEGIN ATTACHMENT DISPLAY IN POSTS
*/

/**
* 显示帖子中的附件
*/
function display_post_attachments($post_id, $switch_attachment)
{
	global $board_config, $is_auth;
		
	if (intval($switch_attachment) == 0 || intval($board_config['disable_mod']))
	{
		return;
	}
	
	if ($is_auth['auth_download'] && $is_auth['auth_view'])
	{
		display_attachments($post_id);
	}
	else
	{
		display_attachments_info($post_id);
	}
}

/*
//
// Generate the Display Assign File Link
//
function display_assign_link($post_id)
{
	global $attach_config, $is_auth, $phpEx;

	$image = 'templates/subSilver/images/icon_mini_message.gif';

	if ( (intval($attach_config['disable_mod'])) || (!( ($is_auth['auth_download']) && ($is_auth['auth_view']))) )
	{
		return ('');
	}

	$temp_url = append_sid("assign_file.$phpEx?p=" . $post_id);
	$link = '<a href="' . $temp_url . '" target="_blank"><img src="' . $image . '" alt="Add File" title="Add File" border="0" /></a>';
	
	return ($link);
}
*/

/**
* Initializes some templating variables for displaying Attachments in Posts
*/
function init_display_post_attachments($switch_attachment)
{
	global $board_config, $db, $is_auth, $template, $postrow, $total_posts, $attachments, $forum_row, $forum_topic_data;

	if (empty($forum_topic_data) && !empty($forum_row))
	{
		$switch_attachment = $forum_row['topic_attachment'];
	}

	//if (intval($switch_attachment) == 0 || intval($board_config['disable_mod']) || (!($is_auth['auth_download'] && $is_auth['auth_view'])))
	if (intval($switch_attachment) == 0 || intval($board_config['disable_mod']))
	{
		return;
	}

	$post_id_array = array();
	
	for ($i = 0; $i < $total_posts; $i++)
	{
		if ($postrow[$i]['post_attachment'] == 1)
		{
			$post_id_array[] = (int) $postrow[$i]['post_id'];
		}
	}

	if (count($post_id_array) == 0)
	{
		return;
	}

	$rows = get_attachments_from_post($post_id_array);
	$num_rows = count($rows);

	if ($num_rows == 0)
	{
		return;
	}

	@reset($attachments);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$attachments['_' . $rows[$i]['post_id']][] = $rows[$i];
	}

	init_display_template('body', '{postrow.ATTACHMENTS}');

	init_complete_extensions_data();
}

/**
* BEGIN ATTACHMENT DISPLAY IN TOPIC REVIEW WINDOW
*/

/**
* Display Attachments in Review Window
*/
function display_review_attachments($post_id, $switch_attachment, $is_auth)
{
	global $board_config, $attachments;
		
	if (intval($switch_attachment) == 0 || intval($board_config['disable_mod']) || (!($is_auth['auth_download'] && $is_auth['auth_view'])) || intval($board_config['attachment_topic_review']) == 0)
	{
		return;
	}

	@reset($attachments);
	$attachments['_' . $post_id] = get_attachments_from_post($post_id);

	if (count($attachments['_' . $post_id]) == 0)
	{
		return;
	}

	display_attachments($post_id);
}

/**
* Initializes some templating variables for displaying Attachments in Review Topic Window
*/
function init_display_review_attachments($is_auth)
{
	global $board_config;

	if (intval($board_config['disable_mod']) || (!($is_auth['auth_download'] && $is_auth['auth_view'])) || intval($board_config['attachment_topic_review']) == 0)
	{
		return;
	}

	init_display_template('reviewbody', '{postrow.ATTACHMENTS}');

	init_complete_extensions_data();
	
}

/**
* END ATTACHMENT DISPLAY IN TOPIC REVIEW WINDOW
*/

/**
* BEGIN DISPLAY ATTACHMENTS -> PREVIEW
*/
function display_attachments_preview($attachment_list, $attachment_filesize_list, $attachment_filename_list, $attachment_comment_list, $attachment_extension_list, $attachment_thumbnail_list, $attachment_filetime_list)
{
	global $board_config, $is_auth, $allowed_extensions, $userdata, $display_categories, $upload_dir, $upload_icons, $template, $db;

	if (count($attachment_list) != 0)
	{
		init_display_template('preview', '{ATTACHMENTS}');
			
		init_complete_extensions_data();

		$template->assign_block_vars('postrow', array());
		$template->assign_block_vars('postrow.attach', array());

		for ($i = 0, $size = count($attachment_list); $i < $size; $i++)
		{
			$filename = $upload_dir . '/' . $attachment_filetime_list . '/' . basename($attachment_list[$i]);

			$thumb_filename = $upload_dir . '/' . THUMB_DIR . '/t_' . basename($attachment_list[$i]);

			$filesize = $attachment_filesize_list[$i];
			$size_lang = ($filesize >= 1048576) ? 'MB' : ( ($filesize >= 1024) ? 'KB' : 'Bytes');

			if ($filesize >= 1048576)
			{
				$filesize = (round((round($filesize / 1048576 * 100) / 100), 2));
			}
			else if ($filesize >= 1024)
			{
				$filesize = (round((round($filesize / 1024 * 100) / 100), 2));
			}

			$display_name = $attachment_filename_list[$i];
			$comment = $attachment_comment_list[$i];
			$comment = str_replace("\n", '<br />', $comment);
			
			$extension = $attachment_extension_list[$i];

			$denied = false;

			// Admin is allowed to view forbidden Attachments, but the error-message is displayed too to inform the Admin
			if (!in_array($extension, $allowed_extensions))
			{
				$denied = true;

				$template->assign_block_vars('postrow.attach.denyrow', array(
					'L_DENIED'		=> '文件扩展名 “' . $extension . '” 已被管理员禁用，因此这个附件是不被显示的')
				);
			} 

			if (!$denied)
			{

				// define category
				$image 		= FALSE;
				$stream 	= FALSE;
				$swf 		= FALSE;
				$thumbnail 	= FALSE;
				$link 		= FALSE;

				if (intval($display_categories[$extension]) == STREAM_CAT)
				{
					$stream = TRUE;
				}
				else if (intval($display_categories[$extension]) == SWF_CAT)
				{
					$swf = TRUE;
				}
				else if (intval($display_categories[$extension]) == IMAGE_CAT && intval($board_config['img_display_inlined']))
				{
					if (intval($board_config['img_link_width']) != 0 || intval($board_config['img_link_height']) != 0)
					{
						list($width, $height) = image_getdimension($filename);

						if ($width == 0 && $height == 0)
						{
							$image = TRUE;
						}
						else
						{
							if ($width <= intval($board_config['img_link_width']) && $height <= intval($board_config['img_link_height']))
							{
								$image = TRUE;
							}
						}
					}
					else
					{
						$image = TRUE;
					}
				}
			
				if (intval($display_categories[$extension]) == IMAGE_CAT && intval($attachment_thumbnail_list[$i]) == 1)
				{
					$thumbnail = TRUE;
					$image = FALSE;
				}

				if (!$image && !$stream && !$swf && !$thumbnail)
				{
					$link = TRUE;
				}

				if ($image)
				{
					// Images
					$template->assign_block_vars('postrow.attach.cat_images', array(
						'DOWNLOAD_NAME'		=> $display_name,
						'IMG_SRC'			=> $filename,
						'FILESIZE'			=> $filesize,
						'SIZE_VAR'			=> $size_lang,
						'COMMENT'			=> $comment)
					);
				}
			
				if ($thumbnail)
				{
					// Images, but display Thumbnail
					$template->assign_block_vars('postrow.attach.cat_thumb_images', array(
						'DOWNLOAD_NAME'		=> $display_name,
						'IMG_SRC'			=> $filename,
						'IMG_THUMB_SRC'		=> $thumb_filename,
						'FILESIZE'			=> $filesize,
						'SIZE_VAR'			=> $size_lang,
						'COMMENT'			=> $comment)
					);
				}

				if ($stream)
				{
					// Streams
					$template->assign_block_vars('postrow.attach.cat_stream', array(
						'U_DOWNLOAD_LINK'	=> $filename,
						'DOWNLOAD_NAME'		=> $display_name,
						'FILESIZE'			=> $filesize,
						'SIZE_VAR'			=> $size_lang,
						'COMMENT'			=> $comment)
					);
				}
			
				if ($swf)
				{
					list($width, $height) = swf_getdimension($filename);
					
					// Macromedia Flash Files
					$template->assign_block_vars('postrow.attach.cat_swf', array(
						'U_DOWNLOAD_LINK'		=> $filename,
						//'U_DOWNLOAD_LINK'		=> append_sid(ROOT_PATH . 'down/' . $attachments['_' . $post_id][$i]['physical_filename']),
						'DOWNLOAD_NAME'			=> $display_name,
						'FILESIZE'				=> $filesize,
						'SIZE_VAR'				=> $size_lang,
						'COMMENT'				=> $comment,
						'WIDTH'					=> $width,
						'HEIGHT'				=> $height)
					);
				}

				if ($link)
				{
					$upload_image = '';

					if ($board_config['upload_img'] != '' && $upload_icons[$extension] == '')
					{
						$upload_image = '<img src="' . $board_config['upload_img'] . '" alt="" border="0" />';
					}
					else if (trim($upload_icons[$extension]) != '')
					{
						$upload_image = '<img src="' . $upload_icons[$extension] . '" alt="" border="0" />';
					}

					$target_blank = 'target="_blank"';
					// display attachment
					$template->assign_block_vars('postrow.attach.attachrow', array(
						'U_DOWNLOAD_LINK'		=> $filename,
						//'U_DOWNLOAD_LINK'	=> append_sid(ROOT_PATH . 'down/' . $attachments['_' . $post_id][$i]['physical_filename']),
						'S_UPLOAD_IMAGE'		=> $upload_image,
						
						'DOWNLOAD_NAME'			=> $display_name,
						'FILESIZE'				=> $filesize,
						'SIZE_VAR'				=> $size_lang,
						'COMMENT'				=> $comment,
						'TARGET_BLANK'			=> $target_blank)
					);
				}
			}
		}
	}
}

/**
* END DISPLAY ATTACHMENTS -> PREVIEW
*/

/*
* 显示那些没有权限下载的附件
*/
function display_attachments_info($post_id)
{
	global $template, $attachments, $board_config, $upload_icons, $is_auth;
	
	$num_attachments = count($attachments['_' . $post_id]);
	
	if ($num_attachments == 0)
	{
		return;
	}
	
	$template->assign_block_vars('postrow.attach', array());
	
	for ($i = 0; $i < $num_attachments; $i++)
	{
		$upload_image = '';
		if ($board_config['upload_img'] != '' && trim($upload_icons[$attachments['_' . $post_id][$i]['extension']]) == '')
		{
			$upload_image = '<img src="' . $board_config['upload_img'] . '" alt="" border="0" />';
		}
		else if (trim($upload_icons[$attachments['_' . $post_id][$i]['extension']]) != '')
		{
			$upload_image = '<img src="' . $upload_icons[$attachments['_' . $post_id][$i]['extension']] . '" alt="" border="0" />';
		}
		
		$display_name 	= $attachments['_' . $post_id][$i]['real_filename']; 
		
		$template->assign_block_vars('postrow.attach.attachinfo', array(
			'S_UPLOAD_IMAGE'	=> $upload_image,
			'DOWNLOAD_NAME'		=> $display_name)
		);
	}
	$template->assign_block_vars('postrow.attach.attachnote', array());
}

/**
* Assign Variables and Definitions based on the fetched Attachments - internal
* used by all displaying functions, the Data was collected before, it's only dependend on the template used. :)
* before this function is usable, init_display_attachments have to be called for specific pages (pm, posting, review etc...)
*/
function display_attachments($post_id)
{
	global $template, $upload_dir, $userdata, $allowed_extensions, $display_categories, $download_modes, $db, $attachments, $upload_icons, $board_config;

	$num_attachments = count($attachments['_' . $post_id]);
	
	if ($num_attachments == 0)
	{
		return;
	}
	$template->assign_block_vars('postrow.attach', array());
	
	for ($i = 0; $i < $num_attachments; $i++)
	{
		// Some basic things...
		$filename = $upload_dir . '/' . create_date('Ym', $attachments['_' . $post_id][$i]['filetime'], 0) . '/' . basename($attachments['_' . $post_id][$i]['physical_filename']);
		
		$thumbnail_filename = $upload_dir . '/' . THUMB_DIR . '/t_' . basename($attachments['_' . $post_id][$i]['physical_filename']);
	
		$upload_image = '';
		if ($board_config['upload_img'] != '' && trim($upload_icons[$attachments['_' . $post_id][$i]['extension']]) == '')
		{
			$upload_image = '<img src="' . $board_config['upload_img'] . '" alt="" border="0" />';
		}
		else if (trim($upload_icons[$attachments['_' . $post_id][$i]['extension']]) != '')
		{
			$upload_image = '<img src="' . $upload_icons[$attachments['_' . $post_id][$i]['extension']] . '" alt="" border="0" />';
		}
		
		$filesize = $attachments['_' . $post_id][$i]['filesize'];
		$size_lang = ($filesize >= 1048576) ? 'MB' : ( ($filesize >= 1024) ? 'KB' : 'Bytes' );

		if ($filesize >= 1048576)
		{
			$filesize = (round((round($filesize / 1048576 * 100) / 100), 2));
		}
		else if ($filesize >= 1024)
		{
			$filesize = (round((round($filesize / 1024 * 100) / 100), 2));
		}

		$display_name 	= $attachments['_' . $post_id][$i]['real_filename']; 
		$comment 		= $attachments['_' . $post_id][$i]['comment'];
		
		if ($comment == '')
		{
			$comment 		= '作者没有对这个文件进行说明';
		}
		else
		{
			$comment 		= str_replace("\n", '<br />', $comment);
		}

		$denied = false;
		
		// Admin is allowed to view forbidden Attachments, but the error-message is displayed too to inform the Admin
		if (!in_array($attachments['_' . $post_id][$i]['extension'], $allowed_extensions))
		{
			$denied = true;

			$template->assign_block_vars('postrow.attach.denyrow', array(
				'L_DENIED'	=> '文件扩展名 “' . $attachments['_' . $post_id][$i]['extension'] . '” 已被管理员禁用，因此这个附件是不被显示的')
			);
		} 

		
		if (!$denied || $userdata['user_level'] == ADMIN)
		{
			
			// define category
			$image 		= FALSE;
			$stream 	= FALSE;
			$swf 		= FALSE;
			$thumbnail 	= FALSE;
			$link 		= FALSE;

			//流媒体
			if (intval($display_categories[$attachments['_' . $post_id][$i]['extension']]) == STREAM_CAT)
			{
				$stream = TRUE;
			}
			//Flash
			else if (intval($display_categories[$attachments['_' . $post_id][$i]['extension']]) == SWF_CAT)
			{
			
				$swf = TRUE;
			}
			
			//图片
			else if (intval($display_categories[$attachments['_' . $post_id][$i]['extension']]) == IMAGE_CAT && intval($board_config['img_display_inlined']))
			{
				if (intval($board_config['img_link_width']) != 0 || intval($board_config['img_link_height']) != 0)
				{
					list($width, $height) = image_getdimension($filename);

					if ($width == 0 && $height == 0)
					{
						$image = TRUE;
					}
					else
					{
						if ($width <= intval($board_config['img_link_width']) && $height <= intval($board_config['img_link_height']))
						{
							$image = TRUE;
						}
					}
				}
				else
				{
					$image = TRUE;
				}
			}
			
			if (intval($display_categories[$attachments['_' . $post_id][$i]['extension']]) == IMAGE_CAT && $attachments['_' . $post_id][$i]['thumbnail'] == 1)
			{
				$thumbnail 	= TRUE;
				$image 		= FALSE;
			}

			if (!$image && !$stream && !$swf && !$thumbnail)
			{
				$link 	= TRUE;
			}

			if ($image)
			{
				// Images
				// NOTE: If you want to use the download.php everytime an image is displayed inlined, replace the
				// Section between BEGIN and END with (Without the // of course):
				//	$img_source = append_sid($phpbb_root_path . 'download.' . $phpEx . '?id=' . $attachments['_' . $post_id][$i]['attach_id']);
				//	$download_link = TRUE;
				// 
				//
				if (intval($board_config['allow_ftp_upload']) && trim($board_config['download_path']) == '')
				{
					$img_source = append_sid(ROOT_PATH . 'download.php?id=' . $attachments['_' . $post_id][$i]['attach_id']);
					$download_link = TRUE;
				}
				else
				{
					// Check if we can reach the file or if it is stored outside of the webroot
					if ($board_config['upload_dir'][0] == '/' || ( $board_config['upload_dir'][0] != '/' && $board_config['upload_dir'][1] == ':'))
					{
						$img_source = append_sid(ROOT_PATH . 'download.php?id=' . $attachments['_' . $post_id][$i]['attach_id']);
						$download_link = TRUE;
					}
					else
					{
						// BEGIN
						$img_source 	= $filename;
						$download_link 	= FALSE;
						// END
					}
				}

				$template->assign_block_vars('postrow.attach.cat_images', array(
					'DOWNLOAD_NAME'		=> $display_name,
					'S_UPLOAD_IMAGE'	=> $upload_image,
					'IMG_SRC'			=> $img_source,
					'FILESIZE'			=> $filesize,
					'SIZE_VAR'			=> $size_lang,
					'COMMENT'			=> $comment,
					'DOWNLOAD_COUNT'	=> $attachments['_' . $post_id][$i]['download_count'])
				);

				// Directly Viewed Image ... update the download count
				if (!$download_link)
				{
					$sql = 'UPDATE ' . ATTACHMENTS_DESC_TABLE . ' 
						SET download_count = download_count + 1 
						WHERE attach_id = ' . (int) $attachments['_' . $post_id][$i]['attach_id'];
	
					if ( !($db->sql_query($sql)) )
					{
						trigger_error('Couldn\'t update attachment download count.', E_USER_WARNING);
					}
				}
			}
			
			if ($thumbnail)
			{
				// Images, but display Thumbnail
				// NOTE: If you want to use the download.php everytime an thumnmail is displayed inlined, replace the
				// Section between BEGIN and END with (Without the // of course):
				//	$thumb_source = append_sid($phpbb_root_path . 'download.' . $phpEx . '?id=' . $attachments['_' . $post_id][$i]['attach_id'] . '&thumb=1');
				//
				if (intval($board_config['allow_ftp_upload']) && trim($board_config['download_path']) == '')
				{
					$thumb_source = append_sid(ROOT_PATH . 'download.php?id=' . $attachments['_' . $post_id][$i]['attach_id'] . '&thumb=1');
				}
				else
				{
					// Check if we can reach the file or if it is stored outside of the webroot
					if ($board_config['upload_dir'][0] == '/' || ( $board_config['upload_dir'][0] != '/' && $board_config['upload_dir'][1] == ':'))
					{
						$thumb_source = append_sid(ROOT_PATH . 'download.php?id=' . $attachments['_' . $post_id][$i]['attach_id'] . '&thumb=1');
					}
					else
					{
						$thumb_source = $thumbnail_filename;
					}
				}
				// 缩略图
				$template->assign_block_vars('postrow.attach.cat_thumb_images', array(
					'DOWNLOAD_NAME'			=> $display_name,
					'S_UPLOAD_IMAGE'		=> $upload_image,

					'IMG_SRC'				=> append_sid(ROOT_PATH . 'download.php?id=' . $attachments['_' . $post_id][$i]['attach_id']),
					'IMG_THUMB_SRC'			=> $thumb_source,
					'FILESIZE'				=> $filesize,
					'SIZE_VAR'				=> $size_lang,
					'COMMENT'				=> $comment,
					'DOWNLOAD_COUNT'		=> $attachments['_' . $post_id][$i]['download_count'])
				);
			}

			if ($stream)
			{
				$template->assign_block_vars('postrow.attach.cat_stream', array(
					'U_DOWNLOAD_LINK'		=> $filename,
				  //'U_DOWNLOAD_LINK'		=> append_sid(ROOT_PATH . 'down/' . $attachments['_' . $post_id][$i]['physical_filename']),
					'S_UPLOAD_IMAGE'		=> $upload_image,

//					'U_DOWNLOAD_LINK' => append_sid(ROOT_PATH . 'download.php?id=' . $attachments['_' . $post_id][$i]['attach_id']),
					'DOWNLOAD_NAME'			=> $display_name,
					'FILESIZE'				=> $filesize,
					'SIZE_VAR'				=> $size_lang,
					'COMMENT'				=> $comment,
					'DOWNLOAD_COUNT'		=> $attachments['_' . $post_id][$i]['download_count'])
				);

				// Viewed/Heared File ... update the download count (download.php is not called here)
				$sql = 'UPDATE ' . ATTACHMENTS_DESC_TABLE . ' 
					SET download_count = download_count + 1 
					WHERE attach_id = ' . (int) $attachments['_' . $post_id][$i]['attach_id'];
	
				if ( !($db->sql_query($sql)) )
				{
					trigger_error('Couldn\'t update attachment download count', E_USER_WARNING);
				}
			}
			
			// Macromedia Flash Files
			if ($swf)
			{
				list($width, $height) = swf_getdimension($filename);
						
				// flash 
				$template->assign_block_vars('postrow.attach.cat_swf', array(
					'U_DOWNLOAD_LINK'		=> $filename,
				  //'U_DOWNLOAD_LINK'		=> append_sid(ROOT_PATH . 'down/' . $attachments['_' . $post_id][$i]['physical_filename']),
					'S_UPLOAD_IMAGE'		=> $upload_image,

					'DOWNLOAD_NAME'			=> $display_name,
					'FILESIZE'				=> $filesize,
					'SIZE_VAR'				=> $size_lang,
					'COMMENT'				=> $comment,
					'DOWNLOAD_COUNT'		=> $attachments['_' . $post_id][$i]['download_count'],
					'WIDTH'					=> $width,
					'HEIGHT'				=> $height)
				);

				// Viewed/Heared File ... update the download count (download.php is not called here)
				$sql = 'UPDATE ' . ATTACHMENTS_DESC_TABLE . ' 
					SET download_count = download_count + 1 
					WHERE attach_id = ' . (int) $attachments['_' . $post_id][$i]['attach_id'];
	
				if ( !($db->sql_query($sql)) )
				{
					trigger_error('Couldn\'t update attachment download count', E_USER_WARNING);
				}
			}

			if ($link)
			{
				$target_blank = '';//( (intval($display_categories[$attachments['_' . $post_id][$i]['extension']]) == IMAGE_CAT) ) ? 'target="_blank"' : '';

				// display attachment
				$template->assign_block_vars('postrow.attach.attachrow', array(
					'U_DOWNLOAD_LINK'	=> append_sid(ROOT_PATH . 'download.php?id=' . $attachments['_' . $post_id][$i]['attach_id']),
					//'U_DOWNLOAD_LINK'	=> append_sid(ROOT_PATH . 'down/' . $attachments['_' . $post_id][$i]['physical_filename']),
					'S_UPLOAD_IMAGE'	=> $upload_image,
					'DOWNLOAD_NAME'		=> $display_name,
					'FILESIZE'			=> $filesize,
					'SIZE_VAR'			=> $size_lang,
					'COMMENT'			=> $comment,
					'TARGET_BLANK'		=> $target_blank,
					'DOWNLOAD_COUNT'	=> $attachments['_' . $post_id][$i]['download_count'])
				);

			}
		}
	}
}

?>