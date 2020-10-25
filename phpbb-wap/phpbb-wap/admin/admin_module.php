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

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['系统']['排版'] = $file;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

$mode = get_var('mode', '');

switch ($mode)
{
	// 创建模块
	case 'create':
		
		$submit = (isset($_POST['submit'])) ? true : false;
		if ($submit)
		{
			$module_name = get_var('name', '');
			$module_name = (MAGIC_QUOTES) ? htmlspecialchars(stripslashes($module_name), ENT_QUOTES) : htmlspecialchars($module_name, ENT_QUOTES);
			$module_type = get_var('type', MODULE_COMMON);
			$module_forum_cat = get_var('forum_cat', '');
			$module_br = isset($_POST['br']) ? 1 : 0;
			$module_hide = isset($_POST['hide']) ? 1 : 0;
			$module_sort = get_var('sort', 0);

			if ($module_name == '')
			{
				trigger_error('模块名称必须填写' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?mode=create&page=' . $modules->page_id)), E_USER_ERROR);
			}

			// 创建论坛
			if ($module_type == MODULE_VIEWFORUM && $module_forum_cat !== '')
			{
				$sql = 'SELECT MAX(forum_order) AS max_order
					FROM ' . FORUMS_TABLE . '
					WHERE cat_id = ' . abs(intval($module_forum_cat));
				
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('无法取得论坛排序信息', E_USER_WARNING);
				}

				$row = $db->sql_fetchrow($result);

				$max_order = $row['max_order'];
				$next_order = $max_order + 10;
				
				$sql = 'SELECT MAX(forum_id) AS max_id
					FROM ' . FORUMS_TABLE;

				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('无法取得论坛的最大ID值', E_USER_WARNING);
				}
				$row = $db->sql_fetchrow($result);

				$max_id = $row['max_id'];
				$next_id = $max_id + 1;

				$forum_auth_ary = array(
					"auth_view" 		=> AUTH_ALL, 
					"auth_read" 		=> AUTH_ALL, 
					"auth_post" 		=> AUTH_REG, 
					"auth_reply" 		=> AUTH_REG, 
					"auth_edit" 		=> AUTH_REG, 
					"auth_delete" 		=> AUTH_REG, 
					"auth_sticky" 		=> AUTH_MOD, 
					"auth_announce" 	=> AUTH_MOD, 
					"auth_vote" 		=> AUTH_REG, 
					"auth_pollcreate" 	=> AUTH_REG,
					'auth_attachments'	=> AUTH_REG,
					'auth_download'		=> AUTH_REG
				);

				$field_sql = '';
				$value_sql = '';
				
				foreach($forum_auth_ary as $field => $value)
				{
					$field_sql .= ", $field";
					$value_sql .= ", $value";

				}

				$sql = 'INSERT INTO ' . FORUMS_TABLE . " (forum_id, forum_name, cat_id, forum_order, forum_status, prune_enable, forum_money, forum_postcount" . $field_sql . ")
					VALUES ('" . $next_id . "', '" . $module_name . "', " . $module_forum_cat . ", $next_order, 0, 0, 0, 0" . $value_sql . ")";

				if (!$db->sql_query($sql))
				{
					trigger_error('无法创建子论坛', E_USER_WARNING);
				}
			}
			else
			{
				$next_id = '';
			}

			if ($module_type == MODULE_COMMON)
			{
				$sql = 'SELECT MAX(page_id) AS max_page_id
					FROM ' . PAGES_TABLE;

				if (!$result = $db->sql_query($sql))
				{
					trigger_error('无法取得页面信息', E_USER_WARNING);
				}

				$row = $db->sql_fetchrow($result);

				$module_needle = (int) $row['max_page_id'] + 1;

				$sql = 'INSERT INTO ' . PAGES_TABLE . " (page_id, page_ago, page_title)
					VALUES ($module_needle, $modules->page_id, '$module_name')";

				if (!$db->sql_query($sql))
				{
					trigger_error('无法建立网页', E_USER_WARNING);
				}
			}
			else
			{
				$module_needle = 0;
			}

			$sql = 'SELECT MAX(module_id) AS max_id
				FROM ' . MODULES_TABLE;

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('无法取得最大模块ID的值', E_USER_WARNING);
			}

			$row = $db->sql_fetchrow($result);
			$module_id = (int)$row['max_id'] + 1;
			
			$modules->save_main($module_id, $module_name, $next_id, $module_hide, $module_br, $module_type, $module_sort, $module_needle, $modules->page_id);

			trigger_error('创建成功' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
		}
		else
		{
			$sql = 'SELECT cat_id, cat_title
				FROM ' . CATEGORIES_TABLE . '
				ORDER BY cat_order';

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('无法取得论坛分类信息', E_USER_WARNING);
			}

			$forum_cat = '<select name="forum_cat">';
			while ($row = $db->sql_fetchrow($result))
			{
				$forum_cat .= '<option value="' . $row['cat_id'] . '">' . $row['cat_title'] . '</option>';
			}
			$forum_cat .= '</select>';

			$template->assign_block_vars('switch_create_module', array('SELECT_FORUM_CAT' 	=> $forum_cat));

			$template->assign_vars(array(
				'L_TITLE'			=> '创建模块',
				'MODULE_TYPE' 		=> $modules->select(0, 'type'),
				'MODULE_SORT'		=> 0,
				'U_BACK_MODULE'		=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id),
				'U_INDEX_MODULE'	=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=0'),
				'S_ACTION'			=> append_sid(ROOT_PATH . 'admin/admin_module.php?mode=create&page=' . $modules->page_id))
			);

			$template->set_filenames(
				array('body' => 'admin/module_main.tpl')
			);
			$template->pparse('body');			
		}


		break;

	// 修改模块
	case 'edit':
		$module_id = get_var('id', '');

		if ($module_id == '')
		{
			trigger_error('请指定模块' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
		}

		$submit = isset($_POST['submit']) ? true : false;

		if ($submit)
		{
			if (isset($_POST['delete']))
			{
				if ( isset($_POST['cancel']) )
				{
					redirect(append_sid('admin/admin_module.php?page=' . $modules->page_id, true));
				}

				$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

				if( !$confirm )
				{
					$page_title = '确认删除';

					page_header($page_title);
					
					$template->set_filenames(array(
						'confirm' => 'confirm_body.tpl')
					);

					$template->assign_vars(array(
						'MESSAGE_TITLE' 	=> '确认删除',
						'MESSAGE_TEXT'		=> '您是否要删除该模块？',
						'L_YES' 			=> '是',
						'L_NO' 				=> '否',
						'S_HIDDEN_FIELDS'	=> '<input type="hidden" name="delete" value="true"/><input type="hidden" name="submit" value="true">',
						'S_CONFIRM_ACTION' 	=> append_sid(ROOT_PATH . 'admin/admin_module.php?mode=edit&id=' . $module_id . '&page=' . $modules->page_id))
					);

					$template->pparse('confirm');

					page_footer();
				}

				$sql = 'SELECT module_id, module_needle 
					FROM ' . MODULES_TABLE . ' 
					WHERE module_id = ' . (int)$module_id;

				if ( !$result = $db->sql_query($sql) )
				{
					trigger_error('查询模块表失败', E_USER_WARNING);
				}

				if ($row = $db->sql_fetchrow($result))
				{
					
					// 先检查该模块下还有没有内容
					$sql = 'SELECT module_type
						FROM ' . MODULES_TABLE . '
						WHERE module_page = ' . (int)$row['module_needle'];

					if (!$result = $db->sql_query($sql))
					{
						trigger_error('无法查询模块的类型', E_USER_WARNING);
					}

					while ($data = $db->sql_fetchrow($result))
					{
						if ($data['module_type'] == MODULE_COMMON)
						{
							trigger_error('对不起，在您删除该栏目之前必须先把栏目下的模块删除' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
						}
					}

					// 删除该模块
					$sql = 'DELETE FROM ' . MODULES_TABLE . ' 
						WHERE module_id = ' . (int)$module_id;

					if (!$db->sql_query($sql))
					{
						trigger_error('无法删除模块', E_USER_WARNING);
					}

					if ($row['module_needle'] !== 0)
					{
						// 删除该页面指向的模块
						$sql = 'DELETE FROM ' . MODULES_TABLE . '
							WHERE module_page = ' . (int)$row['module_needle'];

						if(!$db->sql_query($sql))
						{
							trigger_error('无法删除模块', E_USER_WARNING);
						}						

						// 删除该页面
						$sql = 'DELETE FROM ' . PAGES_TABLE . '
							WHERE page_id = ' . (int)$row['module_needle'];

						if(!$db->sql_query($sql))
						{
							trigger_error('无法删除页面', E_USER_WARNING);
						}
					}
					
					trigger_error('模块已删除成功！' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_ago)), E_USER_ERROR);
				}
				else
				{
					trigger_error('模块不存在或已被删除！' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_ago)), E_USER_ERROR);
				}
			}

			$module_name 	= get_var('name', '');
			$module_name 	= ( MAGIC_QUOTES ) ? htmlspecialchars(stripslashes($module_name), ENT_QUOTES) : htmlspecialchars($module_name, ENT_QUOTES);
			$module_type 	= get_var('type', MODULE_COMMON);
			$module_br 		= isset($_POST['br']) ? 1 : 0;
			$module_hide 	= isset($_POST['hide']) ? 1 : 0;
			$module_sort 	= get_var('sort', 0);
			$module_param 	= get_var('param', '');

			if ($module_name == '')
			{
				trigger_error('模块名称必须填写' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?mode=edit&id=' . $module_id)), E_USER_ERROR);
			}

			$sql = 'SELECT module_needle, module_param 
				FROM ' . MODULES_TABLE . '
				WHERE module_id = ' . (int) $module_id;

			if (!$result = $db->sql_query($sql))
			{
			 	trigger_error('无法获取指向页面信息', E_USER_WARNING);
			} 

			$row = $db->sql_fetchrow($result);

			if ($module_type == MODULE_COMMON && $row['module_needle'] == 0)
			{
				$sql = 'SELECT MAX(page_id) AS max_page_id
					FROM ' . PAGES_TABLE;

				if (!$result = $db->sql_query($sql))
				{
					trigger_error('无法取得页面信息', E_USER_WARNING);
				}

				$row = $db->sql_fetchrow($result);

				$module_needle = (int) $row['max_page_id'] + 1;

				$sql = 'INSERT INTO ' . PAGES_TABLE . " (page_id, page_ago, page_title)
					VALUES ($module_needle, $modules->page_id, '$module_name')";

				if (!$db->sql_query($sql))
				{
					trigger_error('无法建立网页', E_USER_WARNING);
				}
			}
			else
			{
				$module_needle = (int) $row['module_needle'];
			}

			$modules->save_main($module_id, $module_name, $module_param, $module_hide, $module_br, $module_type, $module_sort, $module_needle, $modules->page_id, true);

			if ($module_type == MODULE_COMMON)
			{
				$page_sql = 'UPDATE ' . PAGES_TABLE . "
					SET page_title = '$module_name'
					WHERE page_id = $module_needle";
				if (!$db->sql_query($page_sql))
				{
					trigger_error('无法更新更新模块的页面信息', E_USER_WARNING);
				}						
			}

			trigger_error('模块已成功修改' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
		}
		else
		{
			$sql = 'SELECT module_id, module_title, module_br, module_sort, module_hide, module_type, module_param 
				FROM ' . MODULES_TABLE . ' 
				WHERE module_id = ' . (int)$module_id;
			if ( !$result = $db->sql_query($sql) )
			{
				trigger_error('查询模块表失败', E_USER_WARNING);
			}
			if ($row = $db->sql_fetchrow($result))
			{
				$template->assign_block_vars('switch_edit_module', array());

				$template->assign_vars(array(
					'L_TITLE'			=> '您正在编辑模块（ID:' . $row['module_id'] . '）',
					'MODULE_ID'			=> $row['module_id'],
					'MODULE_PARAM'		=> $row['module_param'],
					'MODULE_NAME'		=> htmlspecialchars_decode($row['module_title'], ENT_QUOTES),
					'MODULE_TYPE' 		=> $modules->select($row['module_type'], 'type'),
					'SELECT_FORUM'		=> $modules->select_forum($row['module_param'], 'param'),
					'MODULE_BR_CHECK'	=> ( $row['module_br'] ) ? ' checked="checked"' : '',
					'MODULE_HIDE_CHECK'	=> ( $row['module_hide'] ) ? ' checked="checked"' : '',
					'MODULE_SORT'		=> $row['module_sort'],
					'U_BACK_MODULE'		=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id),
					'U_INDEX_MODULE'	=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=0'),
					'S_ACTION'			=> append_sid(ROOT_PATH . 'admin/admin_module.php?mode=edit&page=' . $modules->page_id))
				);

				$template->set_filenames(
					array('body' => 'admin/module_main.tpl')
				);
				$template->pparse('body');		
			}
			else
			{
				trigger_error('模块不存在或已被删除！' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
			}		
		}
		break;

	// 模块前面插入内容
	case 'insert':
		
		$module_id = get_var('id', '');

		if ($module_id == '')
		{
			trigger_error('请指定模块' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
		}

		$sql = 'SELECT module_type, module_text 
			FROM ' . MODULES_TABLE . ' 
			WHERE module_id = ' . (int)$module_id;

		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('查询模块表失败', E_USER_WARNING);
		}

		if ($row = $db->sql_fetchrow($result))
		{

			if ($row['module_type'] == MODULE_HEADER)
			{
				trigger_error('您指定的是全局顶部模块' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
			}

			if ($row['module_type'] == MODULE_FOOTER)
			{
				trigger_error('您指定的是全局底部模块' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
			}

			if ($row['module_type'] == MODULE_TOP)
			{
				trigger_error('您指定的是顶部模块' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
			}

			if ($row['module_type'] == MODULE_BOTTOM)
			{
				trigger_error('您指定的是底部模块' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
			}

			if ($row['module_type'] == MODULE_BOTTOM)
			{
				trigger_error('您指定的是head模块' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
			}


			$submit = isset($_POST['module_text']) ? true : false;

			if ($submit)
			{
				$module_text = get_var('module_text', '');
				
				$module_text = ( MAGIC_QUOTES ) ? htmlspecialchars(stripslashes($module_text), ENT_QUOTES) : htmlspecialchars($module_text, ENT_QUOTES);

				$sql = 'UPDATE ' . MODULES_TABLE . "
					SET module_text = '$module_text'
					WHERE module_id = " . (int)$module_id;

				if (!$db->sql_query($sql))
				{
					trigger_error('无法更新模块表', E_USER_WARNING);
				}

				trigger_error('模块已成功更新！' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
			}
			else
			{
				$template->set_filenames(array('body'=> 'admin/module_text.tpl'));

				$template->assign_vars(array(
					'L_TITLE' => '栏目前插入内容',
					'MODULE_TEXT' => htmlspecialchars_decode($row['module_text'], ENT_QUOTES),
					'U_UBB_HELP' => append_sid(ROOT_PATH . 'admin/help.php?mode=mbb'),
					'U_BACK_MODULE'	=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id),
					'U_INDEX_MODULE'=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=0'),
					'S_ACTION' => append_sid(ROOT_PATH . "admin/admin_module.php?mode=insert&id=$module_id&page=$modules->page_id"))
				);

				$template->pparse('body');
			}
		}
		else
		{
			trigger_error('模块不存在或已被删除！' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
		}
		break;

	// 顶部、底部、全局顶部、全局底部、head
	case 'top':
	case 'bottom':
	case 'header':
	case 'footer':
	case 'head':
		if ($mode == 'top')
		{
			$module_type = MODULE_TOP;
			$l_title = '修改顶部模块';
		}
		elseif ($mode == 'bottom')
		{
			$module_type = MODULE_BOTTOM;
			$l_title = '修改底部模块';
		}
		elseif ($mode == 'header')
		{
			$module_type = MODULE_HEADER;
			$l_title = '修改全局顶部模块';
		}
		elseif ($mode == 'footer')
		{
			$module_type = MODULE_FOOTER;
			$l_title = '修改全局底部模块';
		}
		else 
		{
			$module_type = MODULE_HEAD;
			$l_title = '修改head模块';
		}
		
		// 提交数据
		if (isset($_POST['module_text']))
		{
			$module_text = get_var('module_text', '');

			$module_text = ( MAGIC_QUOTES ) ? htmlspecialchars(stripslashes($module_text), ENT_QUOTES) : htmlspecialchars($module_text, ENT_QUOTES);

			$modules->save_other($module_type, $module_text);

			trigger_error('模块保存成功！' . back_link(append_sid(ROOT_PATH . "admin/admin_module.php?mode=$mode&page=$modules->page_id")), E_USER_ERROR);
		}
		// 显示未提交数据的页面
		else
		{
			if ( $module_type == MODULE_TOP || $module_type == MODULE_BOTTOM )
			{
				$sql = 'SELECT module_text
					FROM ' . MODULES_TABLE . '
					WHERE module_type = ' . $module_type . '
						AND module_page = ' . $modules->page_id;

				if (!$result = $db->sql_query($sql))
				{
					trigger_error('无法查询模块信息', E_USER_WARNING);
				}

				if (!$row = $db->sql_fetchrow($result))
				{
					$sql = 'SELECT MAX(module_id) AS max_id
						FROM ' . MODULES_TABLE;

					if (!$result = $db->sql_query($sql))
					{
						trigger_error('无法取得最大模块ID的值', E_USER_WARNING);
					}	

					$max_row = $db->sql_fetchrow($result);

					$new_module_id = $max_row['max_id'] + 1;

					$modules->save_main($new_module_id, '', '', 0, 0, $module_type, 0, 0, $modules->page_id, false);

					$sql = 'SELECT module_text
						FROM ' . MODULES_TABLE . '
						WHERE module_type = ' . $module_type . '
							AND module_page = ' . $modules->page_id;

					if (!$result = $db->sql_query($sql))
					{
						trigger_error('无法查询模块信息', E_USER_WARNING);
					}

					$row = $db->sql_fetchrow($result);
					$module_text = htmlspecialchars_decode($row['module_text'], ENT_QUOTES);
				}
				else
				{
					$module_text = htmlspecialchars_decode($row['module_text'], ENT_QUOTES);
				}
			}
			else
			{
				$sql = 'SELECT module_text
					FROM ' . MODULES_TABLE . '
					WHERE module_type = ' . $module_type;

				if (!$result = $db->sql_query($sql))
				{
					trigger_error('无法查询模块信息', E_USER_WARNING);
				}

				$row = $db->sql_fetchrow($result);

				$module_text = htmlspecialchars_decode($row['module_text'], ENT_QUOTES);
			}
			 

			$template->assign_vars(array(
				'L_TITLE'		=> $l_title,
				'S_ACTION'		=> append_sid(ROOT_PATH . "admin/admin_module.php?mode=$mode&page=$modules->page_id"),
				'MODULE_TEXT' 	=> $module_text,
				'U_UBB_HELP' => append_sid(ROOT_PATH . 'admin/help.php?mode=mbb'),
				'U_BACK_MODULE'	=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id),
				'U_INDEX_MODULE'=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=0'))
			);

			$template->set_filenames(array(
				'body' => 'admin/module_text.tpl')
			);

			$template->pparse('body');
		}

		break;
	case 'logo':

		$submit = isset($_POST['submit']) ? true : false;

		if ($submit)
		{
			if ( (($_FILES['logo']['type'] == 'image/png') || ($_FILES['logo']['type'] == 'image/gif') || ($_FILES['logo']['type'] == 'image/jpeg') || ($_FILES['logo']['type'] == 'image/jpg') || ($_FILES['logo']['type'] == 'image/pjpeg')) && ($_FILES['logo']['size'] < 2097152) )
			{
				if ($_FILES['logo']['error'] > 0)
				{
					$trigger_error('文件上传出错：' . $_FILES['logo']['error'], E_USER_WARNING);
				}
				else
				{
					if ($_FILES['logo']['type'] == 'image/png')
					{
						$logo_name = 'logo.png';
					}
					else if($_FILES['logo']['type'] == 'image/gif')
					{
						$logo_name = 'logo.gif';
					}
					else if($_FILES['logo']['type'] == 'image/jpeg' || $_FILES['logo']['type'] == 'image/jpg' || $_FILES['logo']['type'] == 'image/pjpeg')
					{
						$logo_name = 'logo.jpg';
					}
					else
					{
						$logo_name = 'logo.jpg';
					}
					
					move_uploaded_file($_FILES['logo']['tmp_name'], '../images/' . $logo_name);
					
					set_config('site_logo', $logo_name);
					
					$cache->clear('global_config');
					
					trigger_error('Logo修改成功！' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?mode=logo&page=' . $modules->page_id)), E_USER_ERROR);
				}
			}
			else
			{
				trigger_error('请检查图片类型和大小是否正确！', E_USER_ERROR);
			}
		}
		else
		{

			$template->set_filenames(array('body' => 'admin/module_logo.tpl'));
			$template->assign_vars(array(
				'S_UPLOAD_ACTION' 	=> append_sid(ROOT_PATH . 'admin/admin_module.php?mode=logo'),
				'LOGO'				=> (file_exists(ROOT_PATH . 'images/' . $board_config['site_logo'])) ? ROOT_PATH . 'images/' . $board_config['site_logo'] : ROOT_PATH . 'images/no_logo.png',
				'U_BACK_MODULE'		=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id),
				'U_INDEX_MODULE'	=> append_sid(ROOT_PATH . 'admin/admin_module.php?page=0'))
			);
			$template->pparse('body');
		}
		break;
	// 显示隐藏登录条
	case 'login':
		$display_login = $board_config['display_login'] ? 0 : 1;
		set_config('display_login', $display_login);
		$cache->clear('global_config');
		trigger_error('修改成功！' . back_link(append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_id)), E_USER_ERROR);
		break;
	// 页面编辑模式
	default:
		$modules->display(MODULE_TOP, true);
		$modules->display(MODULE_BOTTOM, true);
		$modules->display(MODULE_MAIN, true);

		$template->assign_block_vars('admin_module_header', array());

		$template->assign_block_vars('exit_admin_module', array(
			'U_EXIT' => append_sid(ROOT_PATH . 'index.php?page=' . $modules->page_id))
		);

		if ($modules->page_id !== 0)
		{
			$template->assign_block_vars('admin_module_nav', array(
				'U_PAGE_AGO' => append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $modules->page_ago),
				'U_PAGE_INDEX' => append_sid(ROOT_PATH . 'admin/admin_module.php?page=0'))
			);
		}

		$template->set_filenames(array(
			'body' => 'index_body.tpl')
		);

		$template->assign_vars(array(
			'U_CREATE' => append_sid(ROOT_PATH . 'admin/admin_module.php?mode=create&page=' . $modules->page_id),
			'U_HEAD' => append_sid(ROOT_PATH .'admin/admin_module.php?mode=head&page=' . $modules->page_id),
			'U_HEADER' => append_sid(ROOT_PATH .'admin/admin_module.php?mode=header&page=' . $modules->page_id),
			'U_FOOTER' => append_sid(ROOT_PATH .'admin/admin_module.php?mode=footer&page=' . $modules->page_id),
			'U_LOGO' => append_sid(ROOT_PATH . 'admin/admin_module.php?mode=logo'))
		);

		$template->pparse('body');
		break;
}

page_footer();

?>