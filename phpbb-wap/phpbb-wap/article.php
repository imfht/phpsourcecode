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

/**
*	phpBB-WAP 文章系统
*/
define('IN_PHPBB', true);
define('ROOT_PATH', './');

require(ROOT_PATH . 'common.php');

$userdata = $session->start($user_ip, PAGE_ARTICLE);
init_userprefs($userdata);

function article_class_select($default_class, $article_class)
{
	global $db;

	$class_select = '<select name="class">';

	foreach ($article_class as $key => $value)
	{
		$selected = ($key == $default_class) ? ' selected="selected"' : '';
		$class_select .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
	}
	
	$class_select .= "</select>";

	return $class_select;
}

function load_article_bbcode_tpl() {

	global $template;
	
	$tpl_filename = $template->make_filename('article/article_bbcode.tpl');
	
	$tpl = fread(fopen($tpl_filename, 'r'), filesize($tpl_filename));
	$tpl = str_replace('\\', '\\\\', $tpl);
	$tpl  = str_replace('\'', '\\\'', $tpl);
	$tpl  = str_replace("\n", '', $tpl);
	$tpl = preg_replace('#<!-- BEGIN (.*?) -->(.*?)<!-- END (.*?) -->#', "\n" . '$bbcode_tpls[\'\\1\'] = \'\\2\';', $tpl);

	$bbcode_tpls = array();
	
	eval($tpl);
	
	return $bbcode_tpls;
}

function article_ubb($text) {
	
	$text = " " . $text;

	if ( !(strpos($text, "[") && strpos($text, "]")) ) {

		$text = substr($text, 1);
		return $text;
	}

	$bbcode_tpl = load_article_bbcode_tpl();
	$bbcode_tpl['img'] 	= str_replace('{URL}', '\\1', $bbcode_tpl['img']);
	$bbcode_tpl['url']	= str_replace('{URL}', '\\1', $bbcode_tpl['url']);
	$bbcode_tpl['url_desc']	= str_replace('{DESCRIPTION}', '\\2', $bbcode_tpl['url']);
	
	$patterns = array();
	$replacements = array();

	$patterns[] = "#\[img\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#i";
	$replacements[] = $bbcode_tpl['img'];

	$patterns[] = "#\[url=([\w]+?://[^[:space:]]*?)\]([^?\n\r\t].*?)\[/url\]#is";
	$replacements[] = $bbcode_tpl['url_desc'];

	$text = preg_replace($patterns, $replacements, $text);

	$text = substr($text, 1);

	return $text;
}

$sql = 'SELECT ac_id, ac_name
	FROM ' . ARTICLES_CLASS_TABLE . '
	ORDER BY ac_sort ASC';

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询文章分类数据', E_USER_WARNING);
}

$article_class = array();

while ($row = $db->sql_fetchrow($result))
{
	$article_class[$row['ac_id']] = $row['ac_name'];
	$template->assign_block_vars('article_class', array(
		'ARTICLE_CLASS' 	=> $row['ac_name'],
		'U_ARTICLE_CLASS'	=> append_sid('article.php?mode=cat&id=' . $row['ac_id']))
	);
}

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

if ($mode == 'view')
{
	if (!isset($_GET['id']))
	{
		trigger_error('请指定要查看的文章');
	}

	$article_id = intval($_GET['id']);

	$sql = 'SELECT article_class, article_title, article_poster, article_time, article_views, article_approval, article_text
		FROM ' . ARTICLES_TABLE . '
		WHERE article_id = ' . $article_id;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询文章数据', E_USER_WARNING);
	}

	if ($row = $db->sql_fetchrow($result))
	{
		if (!$posterdata = get_userdata($row['article_poster']))
		{
			$poster = '热心网友';
		}
		else
		{
			$poster = '<a href="' . append_sid('ucp.php?mode=viewprofile&u=' . $posterdata['user_id']) . '">' . $posterdata['username'] . '</a>';
		}

		$article_text = str_replace("\n", '</p><p>', $row['article_text']);

	}
	else
	{
		trigger_error('您要查看的文章不存在');
	}

	if ($row['article_poster'] == $userdata['user_id'] || $userdata['user_level'] == ADMIN)
	{
		// 如果时间小于一天则不允许编辑
		if (((time() - $row['article_time']) < 86400) || $userdata['user_level'] == ADMIN)
		{
			$template->assign_block_vars('is_poster', array(
				'U_EDIT' => append_sid('article.php?mode=edit&id=' . $article_id),
				'U_DELETE' => append_sid('article.php?mode=delete&id=' . $article_id))
			);
		}

		if (!$row['article_approval'])
		{
			$template->assign_block_vars('approval', array());
		}	
	}
	else
	{
		if (!$row['article_approval'])
		{
			trigger_error('此文章还没有通过审核');
		}		
	}

	if ($row['article_approval'])
	{
		$new_view = $row['article_views'] + 1;

		$sql = 'UPDATE ' . ARTICLES_TABLE . ' 
			SET article_views = ' . $new_view . '
			WHERE article_id = ' . $article_id;

		if (!$db->sql_query($sql))
		{
			trigger_error('无法更新浏览次数', E_USER_WARNING);
		}
	}



	$sql = 'SELECT article_id, article_title
		FROM ' . ARTICLES_TABLE . '
		WHERE article_id < ' . $article_id . '
		LIMIT 0, 1';

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法取得下一篇文章的数据', E_USER_WARNING);
	}

	if ($db->sql_numrows($result))
	{
		$previou_row = $db->sql_fetchrow($result);

		$previou_article = '<a href="' . append_sid('article.php?mode=view&id=' . $previou_row['article_id']) . '">' . $previou_row['article_title'] . '</a>';
	}
	else
	{
		$previou_article = '没有了';
	}

	$sql = 'SELECT article_id, article_title
		FROM ' . ARTICLES_TABLE . '
		WHERE article_id > ' . $article_id . '
		LIMIT 0, 1';

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法取得下一篇文章的数据', E_USER_WARNING);
	}

	if ($db->sql_numrows($result))
	{
		$next_row = $db->sql_fetchrow($result);

		$next_article = '<a href="' . append_sid('article.php?mode=view&id=' .$next_row['article_id']) . '">' . $next_row['article_title'] . '</a>';
	}
	else
	{
		$next_article = '没有了';
	}

	page_header($row['article_title'] . '_浏览文章');

	$template->set_filenames(array(
		'body' => 'article/article_view.tpl')
	);

	$template->assign_vars(array(
		'ARTICLE_TITLE' => $row['article_title'],
		'ARTICLE_POSTER' => $poster,
		'ARTICLE_TIME' => create_date('Y年m月d日 H:i', $row['article_time'], $board_config['board_timezone']),
		'ARTICLE_VIEWS' => $row['article_views'],
		'ARTICLE_TEXT' => article_ubb($article_text),

		'ARTICLE_PREVIOU' => $previou_article,
		'ARTICLE_NEXT' => $next_article,

		'U_BACK' => append_sid('article.php'),
		)
	);

	$template->pparse('body');

	page_footer();

}
elseif ($mode == 'cat')
{

	if (!isset($_GET['id']))
	{
		trigger_error('请指定分类');
	}

	if (!isset($article_class[$_GET['id']]))
	{
		trigger_error('分类不存在');
	}

	$cid = $_GET['id'];

	$per = 15;
	$start = get_pagination_start($per);

	$sql = 'SELECT article_title, article_id, article_views, article_approval
		FROM ' . ARTICLES_TABLE .'
		WHERE article_class = ' . $cid . '
		ORDER BY article_time DESC
		LIMIT ' . $start . ', ' . $per;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询该分类的文章数据', E_USER_WARNING);
	}

	$i = 0;
	$noin_number = 0;
	while ($row = $db->sql_fetchrow($result))
	{
		if (!$row['article_approval'])
		{
			if ($row['article_poster'] == $userdata['user_id'] || $userdata['user_level'] == ADMIN)
			{
				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
				$template->assign_block_vars('article_cat', array(
					'NUMBER'		=> $i + $start + 1,
					'ROW_CLASS'		=> $row_class,
					'ARTICLE_TITLE' => $row['article_title'],
					'U_ARTICLE'		=> append_sid('article.php?mode=view&id=' . $row['article_id']))
				);
				$i++;
			}
			else
			{
				$noin_number++;
			}
		}
		else
		{
			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
			$template->assign_block_vars('article_cat', array(
				'NUMBER'		=> $i + $start + 1,
				'ROW_CLASS'		=> $row_class,
				'ARTICLE_TITLE' => $row['article_title'],
				'U_ARTICLE'		=> append_sid('article.php?mode=view&id=' . $row['article_id']))
			);
			$i++;
		}
	}

	$sql = 'SELECT article_id AS total
		FROM ' . ARTICLES_TABLE .'
		WHERE article_class = ' . $cid;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询该分类的文章数据', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);

	$total_article = (($row['total'] - $noin_number) < 0) ? 0 : ($row['total'] - $noin_number);

	if (!$total_article)
	{
		$template->assign_block_vars('not_article', array());
	}

	page_header($article_class[$cid] . '_文章分类');

	$template->set_filenames(array(
		'body' => 'article/article_cat.tpl')
	);

	$template->assign_vars(array(
		'CAT_TITLE' => $article_class[$cid],
		'U_BACK' => append_sid('article.php'),
		'U_NEW_ARTICLE' => append_sid('article.php?mode=post'),
		'PAGINATION' => generate_pagination('article.php?mode=cat&id=' . $cid, $total_article, $per, $start))
	);

	$template->pparse('body');

	page_footer();

}
elseif ($mode == 'edit')
{
	if (!$userdata['session_logged_in'])
	{
		trigger_error('请先登录');
	}

	if (!isset($_GET['id']))
	{
		trigger_error('您没有选中任何文章');
	}

	$edit_id = intval($_GET['id']);

	$sql = 'SELECT article_poster, article_time, article_class, article_title, article_text
		FROM ' . ARTICLES_TABLE . '
		WHERE article_id = ' . $edit_id;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法获取要修改的文章信息', E_USER_WARNING);
	}

	if (!$row = $db->sql_fetchrow($result))
	{
		trigger_error('没有这篇文章');
	}

	if ($userdata['user_level'] != ADMIN)
	{
		if ($userdata['user_id'] != $row['article_poster'])
		{
			trigger_error('您没有权限修改');
		}

		if (((time() - $row['article_time']) > 86400))
		{
			trigger_error('这篇文章为历史文章(时间超过一天)不能编辑');
		}
	}
	else
	{
		$template->assign_block_vars('admin', array(
			'CLASS_MANAGE' 	=> append_sid('article.php?mode=manage&b=edit&i=' . $edit_id),
			'CLASS_CREATE' 	=> append_sid('article.php?mode=manage&m=create&b=edit&i=' . $edit_id))
		);
	}

	if (isset($_POST['submit']))
	{
		$title = get_var('title', '');
		$class = get_var('class', '');
		$text = get_var('text', '');

		$error = false;
		$error_msg = '';

		if (empty($title))
		{
			$error = true;
			$error_msg = '<p>文章标题不能为空</p>';
		}

		if (empty($class))
		{
			$error = true;
			$error_msg = '<p>必需选择文章分类</p>';
		}

		if (empty($text))
		{
			$error = true;
			$error_msg = '<p>文章内容不能为空</p>';
		}

		if (!$error)
		{
			$sql_ary = array(
				'article_title'		=> $title,
				'article_class'		=> $class,
				'article_text'		=> $text
			);

			$sql = 'UPDATE ' . ARTICLES_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE article_id = ' . $edit_id;

			if (!$db->sql_query($sql))
			{
				trigger_error('无法修改文章', E_USER_WARNING);
			}

			trigger_error('文章修改成功' . back_link(append_sid('article.php?mode=view&id=' . $edit_id)));
		}
	}

	page_header('修改文章');

	if ( $error )
	{
		error_box('ERROR_BOX', $error_msg);
	}

	$template->assign_vars(array(
		'L_TITLE' => '修改文章',
		'ARTICLE_TITLE' => $row['article_title'],
		'ARTICLE_TEXT' => $row['article_text'],
		'ARTICLE_CLASS' => article_class_select($row['article_class'], $article_class),
		'U_BACK' => append_sid('article.php?mode=view&id=' . $edit_id),
		'S_ACTION' => append_sid('article.php?mode=edit&id=' . $edit_id))
	);

	$template->set_filenames(array(
		'body' => 'article/article_edit.tpl')
	);

	$template->pparse('body');	

	page_footer();
}
elseif ($mode == 'post')
{
	if (!$userdata['session_logged_in'])
	{
		trigger_error('请先登录');
	}
	
	$error = false;
	$error_msg = '';

	if (isset($_POST['submit']))
	{
		$title = get_var('title', '');
		$class = get_var('class', '');
		$text = get_var('text', '');

		if (empty($title))
		{
			$error = true;
			$error_msg = '<p>文章标题不能为空</p>';
		}

		if (empty($class))
		{
			$error = true;
			$error_msg = '<p>必需选择文章分类</p>';
		}

		if (empty($text))
		{
			$error = true;
			$error_msg = '<p>文章内容不能为空</p>';
		}

		if (!$error)
		{
			$sql = 'SELECT MAX(article_id) AS max_id
				FROM ' . ARTICLES_TABLE;

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('无法取得文章的ID', E_USER_WARNING);
			}

			$row = $db->sql_fetchrow($result);

			$new_article_id = $row['max_id'] + 1;

			$sql_ary = array(
				'article_id'		=> $new_article_id,
				'article_poster'	=> $userdata['user_id'],
				'article_time'		=> time(),
				'article_views'		=> 0,
				'article_title'		=> $title,
				'article_class'		=> $class,
				'article_text'		=> $text
			);

			$sql = 'INSERT INTO ' . ARTICLES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);

			if (!$db->sql_query($sql))
			{
				trigger_error('无法发表文章', E_USER_WARNING);
			}

			trigger_error('发表成功' . back_link(append_sid('article.php?mode=view&id=' . $new_article_id)));
		}
	}

	if ($userdata['user_level'] == ADMIN)
	{
		$template->assign_block_vars('admin', array(
			'CLASS_MANAGE' 	=> append_sid('article.php?mode=manage&b=post'),
			'CLASS_CREATE' 	=> append_sid('article.php?mode=manage&m=create&b=post'))
		);
	}
	page_header('发表文章');

	if ( $error )
	{
		error_box('ERROR_BOX', $error_msg);
	}

	$template->assign_vars(array(
		'L_TITLE' => '发表文章',
		'ARTICLE_TITLE' => '',
		'ARTICLE_TEXT' => '',
		'ARTICLE_CLASS' => article_class_select($row['article_class'], $article_class),
		'U_BACK' => append_sid('article.php'),
		'S_ACTION' => append_sid('article.php?mode=post'))
	);

	$template->set_filenames(array(
		'body' => 'article/article_edit.tpl')
	);

	$template->pparse('body');	

	page_footer();
}
elseif ($mode == 'delete')
{
	if (!$userdata['session_logged_in'])
	{
		trigger_error('请先登录');
	}

	if (!isset($_GET['id']))
	{
		trigger_error('请指定要删除的文章');
	}

	$delete_id = intval($_GET['id']);

	$sql = 'SELECT article_title, article_class
		FROM ' . ARTICLES_TABLE . '
		WHERE article_id = ' . $delete_id;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询要删除的文章信息', E_USER_WARNING);
	}

	if (!$row = $db->sql_fetchrow($result))
	{
		trigger_error('您要删除的文章不存在');
	}	

	if ($userdata['user_level'] != ADMIN)
	{
		if ($userdata['user_id'] != $row['article_poster'])
		{
			trigger_error('您没有权限删除');
		}

		if (((time() - $row['article_time']) > 86400))
		{
			trigger_error('这篇文章为历史文章(时间超过一天)不能删除');
		}
	}
	
	if ( isset($_POST['cancel']) )
	{
		redirect(append_sid('article.php?mode=view&id=' . $delete_id, true));
	}

	$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

	if( $confirm )
	{
		$sql = 'DELETE FROM ' . ARTICLES_TABLE . ' 
			WHERE article_id = ' . $delete_id;

		if (!$db->sql_query($sql))
		{
			trigger_error('无法删除', E_USER_WARNING);
		}

		trigger_error('删除成功' . back_link(append_sid('article.php?mode=cat&id=' . $row['article_class'])));
	}

	page_header('删除文章');

	$template->set_filenames(array(
		'confirm' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' 	=> '删除文章',
		'MESSAGE_TEXT'		=> '是否删除 “' . $row['article_title'] . '” 这篇文章？',
		'L_YES' 			=> '是',
		'L_NO' 				=> '否',
		'S_CONFIRM_ACTION' 	=> append_sid('article.php?mode=delete&id=' . $delete_id))
	);

	$template->pparse('confirm');

	page_footer();

}
elseif ($mode == 'manage')
{
	if (!$userdata['session_logged_in'])
	{
		trigger_error('请先登录');
	}
	
	if ($userdata['user_level'] != ADMIN)
	{
		trigger_error('您没有权限');
	}

	$manage = isset($_GET['m']) ? $_GET['m'] : '';

	switch ($manage)
	{
		case 'create':

			$back_url = append_sid('article.php');
			$create_url = append_sid('article.php?mode=manage&m=create');

			if (isset($_GET['i']) && isset($_GET['b']))
			{
				if ($_GET['b'] == 'edit' && $_GET['i'])
				{
					$back_url = append_sid('article.php?mode=edit&id=' . (int) $_GET['i']);
					$create_url = append_sid('article.php?mode=manage&m=create&b=edit&i=' . (int) $_GET['i']);
				}
				else if ($_GET['b'] == 'post')
				{
					$back_url = append_sid('article.php?mode=post');
					$create_url = append_sid('article.php?mode=manage&m=create&b=post');
				}
			}

			if (isset($_POST['name']) && isset($_POST['sort']))
			{
				if (empty($_POST['name']))
				{
					trigger_error('分类名不能留空' . back_link($create_url));
				}

				$sort = intval($_POST['sort']);
				$sql = 'INSERT INTO ' . ARTICLES_CLASS_TABLE . " (ac_name, ac_sort)
					VALUES ('{$_POST['name']}', $sort)";

				if (!$db->sql_query($sql))
				{
					trigger_error('无法创建分类', E_USER_WARNING);
				}

				trigger_error('创建成功' . back_link($back_url));
			}

			trigger_error('<form action="' . $create_url . '" method="post">名称：<input type="text" name="name" value="" />排序：<input type="text" name="sort" size="3" value="" /><input type="submit" value="添加" /></form>');

			break;
		case 'delete':
			
			$delete_id = (int) $_GET['i'];

			$sql = 'SELECT ac_name
				FROM ' . ARTICLES_CLASS_TABLE . '
				WHERE ac_id = ' . $delete_id;

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('无法查询要删除的文章分类信息', E_USER_WARNING);
			}

			if (!$row = $db->sql_fetchrow($result))
			{
				trigger_error('此分类不存在或已被删除' . back_link(append_sid('article.php?mode=manage')));
			}

			if ( isset($_POST['cancel']) )
			{
				redirect(append_sid('article.php?mode=manage', true));
			}

			$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

			if ($confirm)
			{
				$class_id = intval($_POST['class']);

				if ($delete_id == $class_id)
				{
					$sql = 'DELETE FROM ' . ARTICLES_TABLE . '
						WHERE article_class = ' . $delete_id;

					if (!$db->sql_query($sql))
					{
						trigger_error('无法删除文章数据', E_USER_WARNING);
					}

				}
				else
				{

					$sql = 'UPDATE ' . ARTICLES_TABLE . '
						SET article_class = ' . $class . ' 
						WHERE article_class = ' . $delete_id;

					if (!$db->sql_query($sql))
					{
						trigger_error('无法转移文章数据', E_USER_WARNING);
					}
				}

				$sql = 'DELETE FROM ' . ARTICLES_CLASS_TABLE . ' 
					WHERE ac_id = ' . $delete_id;

				if (!$db->sql_query($sql))
				{
					trigger_error('无法删除文章分类', E_USER_WARNING);
				}

				trigger_error('操作成功！' . back_link(append_sid('article.php?mode=manage')));
			}

			$s_hidden = '<p>删除后把该分类的文章移动到：';
			$s_hidden .= article_class_select($delete_id, $article_class);
			$s_hidden .= '<br /><span class="red">选择当前分类表示删除该分类的文章</span></p>';

			confirm_box(
				'删除文章分类',
				'删除文章分类',
				'是否删除文章分类“' . $row['ac_name'] . '”？',
				append_sid('article.php?mode=manage&m=delete&i=' . $delete_id),
				$s_hidden
			);

			break;

		case 'edit':
			
			$edit_id = (int) $_GET['i'];

			$sql = 'SELECT ac_name, ac_sort
				FROM ' . ARTICLES_CLASS_TABLE . '
				WHERE ac_id = ' . $edit_id;

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('无法查询要修改的文章分类信息', E_USER_WARNING);
			}

			if (!$row = $db->sql_fetchrow($result))
			{
				trigger_error('此分类不存在' . back_link(append_sid('article.php?mode=manage')));
			}

			if (isset($_POST['name']) && isset($_POST['sort']))
			{
				if (empty($_POST['name']))
				{
					trigger_error('分类名不能留空' . back_link($create_url));
				}

				$sort = intval($_POST['sort']);

				$sql = 'UPDATE ' . ARTICLES_CLASS_TABLE . "
					SET ac_name = '{$_POST['name']}', ac_sort = $sort
					WHERE ac_id = " . $edit_id;

				if (!$db->sql_query($sql))
				{
					trigger_error('无法修改分类', E_USER_WARNING);
				}

				trigger_error('修改成功' . back_link(append_sid('article.php?mode=manage')));
			}

			trigger_error('<form action="' . $create_url . '" method="post">名称：<input type="text" name="name" value="' . $row['ac_name'] . '" />排序：<input type="text" name="sort" size="3" value="' . $row['ac_sort'] . '" /><input type="submit" value="修改" /></form>');

			break;
		
		default:
			
			page_header('文章分类管理');

			$i = 0;
			foreach ($article_class as $key => $value)
			{
				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
				$template->assign_block_vars('class_list', array(
					'ROW_CLASS' => $row_class,
					'CLASS_NAME' => $value,
					'U_EDIT' => append_sid('article.php?mode=manage&m=edit&i=' . $key),
					'U_DELETE' => append_sid('article.php?mode=manage&m=delete&i=' . $key))
				);

				$i++;
			}

			$template->set_filenames(array(
				'body' => 'article/article_class_manage.tpl')
			);

			$template->assign_var('U_BACK', append_sid('article.php'));

			$template->pparse('body');	

			page_footer();

			break;
	}
}
elseif ($mode == 'approval')
{
	if (!$userdata['session_logged_in'])
	{
		trigger_error('请先登录');
	}
	
	if ($userdata['user_level'] != ADMIN)
	{
		trigger_error('您没有权限');
	}

	$submit = isset($_POST['submit']) ? true : false;
	$id_list = get_var('id_list', array(0));

	if ($submit && count($id_list) > 0)
	{
		//$sql = 'DELETE FROM ' . ARTICLES_TABLE . '
		//	WHERE article_id IN (' . implode(', ', $id_list) . ')
		//		AND link_admin_user = ' . (int) $userdata['user_id'];

		$sql = 'UPDATE ' . ARTICLES_TABLE . ' 
			SET article_approval = 1 
			WHERE article_id IN (' . implode(', ', $id_list) . ')';

		if (!$db->sql_query($sql))
		{
			trigger_error('文章无法进行审核', E_USER_WARNING);
		}	

		trigger_error('操作成功！' . back_link(append_sid('article.php?mode=approval')));
	}

	$per = $board_config['topics_per_page'];
	$start = get_pagination_start($per);

	$sql = 'SELECT article_id, article_title
		FROM ' . ARTICLES_TABLE . '
		WHERE article_approval = 0
		ORDER BY article_time DESC
		LIMIT ' . $start . ', ' . $per;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询待审核的文章数据', E_USER_WARNING);
	}

	$i = 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
		$template->assign_block_vars('approval', array(
			'ROW_CLASS' => $row_class,
			'ARTICLE_TITLE' => $row['article_title'],
			'ARTICLE_ID' => $row['article_id'],
			'U_ARTICLE' => append_sid('article.php?mode=view&id=' . $row['article_id']))
		);
		$i++;
	}

	$sql = 'SELECT COUNT(article_id) AS total
		FROM ' . ARTICLES_TABLE . '
		WHERE article_approval = 0';

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法统计未审核的文章');
	}

	$row = $db->sql_fetchrow($result);

	if (!$row['total'])
	{
		$template->assign_block_vars('not_approval', array());
	}

	$pagination = generate_pagination('article.php?mode=approval', $row['total'], $per, $start);

	page_header('等待审核的文章');

	$template->set_filenames(array(
		'body' => 'article/article_approval.tpl')
	);

	$template->assign_vars(array(
		'PAGINATION' => $pagination,
		'U_BACK' => append_sid('article.php'),
		'S_ACTION' => append_sid('article.php?mode=approval'))
	);

	$template->pparse('body');

	page_footer();
}

// 显示30天热门文章
$sql = 'SELECT article_id, article_class, article_title
	FROM ' . ARTICLES_TABLE . '
	WHERE article_time >= ' . (time() - 2592000) . '
		AND article_approval = 1
	ORDER BY article_views, article_time DESC
	LIMIT 0, 5';

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询热门文章数据', E_USER_WARNING);
}

if (!$db->sql_numrows($result))
{
	$template->assign_block_vars('not_hot', array());
}
while ($row = $db->sql_fetchrow($result))
{

	$ac_name = (isset($article_class[$row['article_class']])) ? $article_class[$row['article_class']] : '未知';

	$template->assign_block_vars('article_hot', array(
		'ARTICLE_TITLE' => $row['article_title'],
		'U_ARTICLE'		=> append_sid('article.php?mode=view&id=' . $row['article_id']),
		'ARTICLE_NAME'	=> $ac_name)
	);
}

// 显示热门文章
$sql = 'SELECT article_id, article_class, article_title
	FROM ' . ARTICLES_TABLE . '
		WHERE article_approval = 1
	ORDER BY article_time DESC
	LIMIT 0, 10';
if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询最新文章数据', E_USER_WARNING);
}

if (!$db->sql_numrows($result))
{
	$template->assign_block_vars('not_new', array());
}

while ($row = $db->sql_fetchrow($result))
{
	$ac_name = (isset($article_class[$row['article_class']])) ? $article_class[$row['article_class']] : '未知';

	$template->assign_block_vars('article_new', array(
		'ARTICLE_TITLE' => $row['article_title'],
		'U_ARTICLE'		=> append_sid('article.php?mode=view&id=' . $row['article_id']),
		'ARTICLE_NAME'	=> $ac_name)
	);
}

page_header('文章');

$template->set_filenames(array(
	'body' => 'article/article_body.tpl')
);

if ($userdata['user_level'] == ADMIN)
{
	$template->assign_block_vars('admin', array());
}

$template->assign_vars(array(
	'U_BACK' => append_sid('index.php'),
	'U_NEW_ARTICLE' => append_sid('article.php?mode=post'),
	'U_APPROVAL' => append_sid('article.php?mode=approval'))
);

$template->pparse('body');	

page_footer();
?>