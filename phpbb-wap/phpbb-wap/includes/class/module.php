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

if (!defined('IN_PHPBB')) exit;

require_once(ROOT_PATH . 'includes/class/module_bbcode.php');

/**
* 	phpBB-WAP 模块类
*	作者：Crazy
*/
class Module extends Module_bbcode
{
	
	var $page_id = 0;
	var $page_ago = 0;
	var $page_title = '';
	var $page_type = 0;
	var $_userdata = NULL;
	var $_config = NULL;
	var $_db = NULL;
	var $_template = NULL;

	var $page_modules = array();

	function __construct($page_id = 0, $page_title)
	{
		global $template, $db, $userdata, $board_config;

		$this->page_id 		= abs(intval($page_id));
		$this->_db 			= $db;
		$this->_template 	= $template;
		$this->_config 		= $board_config;
		$this->_userdata 	= $userdata;
		
		if ($this->page_id == 0)
		{
			if (defined('THIS_INDEX'))
			{
				$this->page_title = $this->_config['sitename'];
			}
			else
			{
				$this->page_title = $page_title;
			}
		}
		else
		{
			$this->page_title = ($page_title == '') ? $this->_config['sitename'] : $page_title;
		}

		if ( !defined('THIS_INDEX') )
		{
			$this->page_title = $this->page_title . '_' . $this->_config['sitename'];
		}

		parent::__construct();
		
		$this->page_info();
		$this->other();
		$this->main();
	}

	/**
	*	方法：display()
	*	把页面展示出来
	*	@参数 整数 $module_type 要加载的模块类型
	*/
	public function display($module_type, $admin_module = false)
	{

		switch ($module_type)
		{
			case MODULE_HEADER:
				$this->_template->assign_var('MODULE_HEADER', $this->parser_mbb($this->page_modules['other'][MODULE_HEADER]));
				break;
			case MODULE_FOOTER:
				$this->_template->assign_var('MODULE_FOOTER', $this->parser_mbb($this->page_modules['other'][MODULE_FOOTER]));
				break;
			case MODULE_TOP:
				$module_top = ($admin_module) ? '【<a href="' . append_sid(ROOT_PATH . 'admin/admin_module.php?mode=top&page=' . $this->page_id) . '">顶</a>】' : '';
				$this->_template->assign_var('MODULE_TOP', $module_top . $this->parser_mbb($this->page_modules['other'][MODULE_TOP]));
				break;
			case MODULE_BOTTOM:
				$module_bottom = ($admin_module) ? '【<a href="' . append_sid(ROOT_PATH . 'admin/admin_module.php?mode=bottom&page=' . $this->page_id) . '">底</a>】' : '';
				$this->_template->assign_var('MODULE_BOTTOM', $module_bottom . $this->parser_mbb($this->page_modules['other'][MODULE_BOTTOM]));
				break;
			case MODULE_HEAD:
				$this->_template->assign_var('MODULE_HEAD', $this->parser_mbb($this->page_modules['other'][MODULE_HEAD]));
				break;
			
			default:

				if (is_array($this->page_modules['main']))
				{
					foreach ($this->page_modules['main'] as $main_key => $main_value)
					{

						$this->_template->assign_block_vars('module_main', array(
							'MODULE_TITLE' 		=> $this->gen_link($main_value['module_type'], $main_value['module_title'], $main_value['module_param'], $main_value['module_needle']),
							'MODULE_TEXT'		=> $this->parser_mbb($main_value['module_text']),
							'MODULE_BR'			=> ($main_value['module_br']) ? '<br />' : '',
							'U_INSERT_EDIT'		=> (defined('IN_ADMIN')) ? '【<a href="' . append_sid(ROOT_PATH . 'admin/admin_module.php?mode=insert&id=' . $main_value['module_id'] . '&page=' . $this->page_id) . '">插</a>.<a href="' . append_sid(ROOT_PATH . 'admin/admin_module.php?mode=edit&id=' . $main_value['module_id'] . '&page=' . $this->page_id) . '">修</a>】' : '')
						);
					}
				}

				$this->_template->set_filenames(array('body' => 'index_body.tpl'));
				break;
		}
	}

	/**
	*	方法：main()
	*	获取页面的main部分
	*/
	function main()
	{

		$sql = 'SELECT module_id, module_hide, module_title, module_text, module_br, module_type, module_param, module_needle
			FROM ' . MODULES_TABLE . '
			WHERE module_page = ' . $this->page_id . '
				AND module_type NOT IN(' . MODULE_HEADER . ', ' . MODULE_TOP . ', ' . MODULE_BOTTOM . ', ' . MODULE_FOOTER . ', ' . MODULE_HEAD . ')
			ORDER BY module_sort ASC';


		if ($result = $this->_db->sql_query($sql))
		{
			while ($row = $this->_db->sql_fetchrow($result))
			{

				$this->page_modules['main'][] = array(
					'module_title' 	=> (!defined('IN_ADMIN') && $row['module_hide']) ? '' : htmlspecialchars_decode($row['module_title'], ENT_QUOTES),
					'module_text'	=> htmlspecialchars_decode($row['module_text'], ENT_QUOTES),
					'module_br'		=> $row['module_br'],
					'module_type' 	=> $row['module_type'],
					'module_param'	=> $row['module_param'],
					'module_id'		=> $row['module_id'],
					'module_needle'	=> $row['module_needle']
				);
			}

			$this->page_modules['main'] = isset($this->page_modules['main']) ? $this->page_modules['main'] : '';
			
		}
		else
		{
			trigger_error('获取网页内容失败', E_USER_WARNING);
		}
	}

	/**
	*	方法：other()
	*	获取除main的所有内容
	*/
	function other()
	{
		$sql = 'SELECT module_text, module_type 
			FROM ' . MODULES_TABLE . '
			WHERE module_type IN (' . MODULE_HEADER . ', ' . MODULE_FOOTER . ', ' . MODULE_HEAD . ')';

		if ($result = $this->_db->sql_query($sql))
		{
			while ($row = $this->_db->sql_fetchrow($result))
			{
				$this->page_modules['other'][$row['module_type']] = htmlspecialchars_decode($row['module_text'], ENT_QUOTES);
			}

			$this->page_modules['other'][MODULE_HEADER] = isset($this->page_modules['other'][MODULE_HEADER]) ? $this->page_modules['other'][MODULE_HEADER] : '';
			$this->page_modules['other'][MODULE_FOOTER] = isset($this->page_modules['other'][MODULE_FOOTER]) ? $this->page_modules['other'][MODULE_FOOTER] : '';
			$this->page_modules['other'][MODULE_HEAD] = isset($this->page_modules['other'][MODULE_HEAD]) ? $this->page_modules['other'][MODULE_HEAD] : '';
		}
		else
		{
			trigger_error('无法获得全局顶部和全局底部的内容', E_USER_WARNING);
		}

		$sql = 'SELECT module_text, module_type
			FROM ' . MODULES_TABLE . '
			WHERE module_type IN(' . MODULE_TOP . ', ' . MODULE_BOTTOM . ', ' . MODULE_HEAD . ')
				AND module_page = ' . $this->page_id;

		if ($result = $this->_db->sql_query($sql))
		{
			while ($row = $this->_db->sql_fetchrow($result))
			{
				$this->page_modules['other'][$row['module_type']] = htmlspecialchars_decode($row['module_text'], ENT_QUOTES);
			}

			$this->page_modules['other'][MODULE_TOP] = isset($this->page_modules['other'][MODULE_TOP]) ? $this->page_modules['other'][MODULE_TOP] : '';
			$this->page_modules['other'][MODULE_BOTTOM] = isset($this->page_modules['other'][MODULE_BOTTOM]) ? $this->page_modules['other'][MODULE_BOTTOM] : '';
		}
		else
		{
			trigger_error('无法获得网页顶部和底部的内容', E_USER_WARNING);
		}
	}

	/**
	*	取得页面中的信息
	*/
	function page_info()
	{
		$sql = 'SELECT page_title, page_ago
			FROM ' . PAGES_TABLE . '
			WHERE page_id = ' . $this->page_id;

		if ($result = $this->_db->sql_query($sql))
		{
			$row = $this->_db->sql_fetchrow($result);

			$this->page_title = (isset($row['page_title'])) ? $row['page_title'] : '';
			$this->page_ago = (isset($row['page_ago'])) ? $row['page_ago'] : 0;
		}
		else
		{
			trigger_error('获取网页信息失败', E_USER_WARNING);
		}
	}

	/**
	*	方法：save_other()
	*	保存顶部、底部、全局顶部、全局底部、head的内容
	*/
	function save_other($module_type, $module_text)
	{
		$where_and = '';

		if ($module_type == MODULE_TOP || $module_type == MODULE_BOTTOM)
		{
			$where_and = ' AND module_page = ' . $this->page_id;
		}

		$sql = 'UPDATE ' . MODULES_TABLE . "
			SET module_text = '$module_text'
			WHERE module_type = $module_type
			$where_and";

		if (!$this->_db->sql_query($sql))
		{
			trigger_error('无法保存模块信息', E_USER_WARNING);
		}
	}

	/**
	*	方法：save_main()
	*	保存模块的内容
	*/
	function save_main($module_id, $module_title, $module_param, $module_hide, $module_br, $module_type, $module_sort, $module_needle, $module_page, $insert = false)
	{
		if ($insert)
		{
			$sql = 'UPDATE ' . MODULES_TABLE . "
				SET module_title = '$module_title', module_param = '$module_param', module_hide = $module_hide, module_br = $module_br, module_type = $module_type, module_sort = $module_sort, module_page = $module_page
				WHERE module_id = $module_id";
		}
		else
		{
			$sql = 'INSERT INTO ' . MODULES_TABLE . " (module_id, module_title, module_text, module_param, module_hide, module_br, module_type, module_sort, module_needle, module_page)
				VALUE($module_id, '$module_title', '', '$module_param', $module_hide, $module_br, $module_type, $module_sort, $module_needle, $module_page)";
		}

		if (!$this->_db->sql_query($sql))
		{
			trigger_error('无法更新/插入模块数据', E_USER_WARNING);
		}

	}

	/**
	*	模块的下拉框
	*/
	function select($default, $select_name)
	{
		$select = '<select name="' . $select_name . '">';
		$select_array = array(
			MODULE_COMMON		=> '普通模块',
			MODULE_VIEWFORUM	=> '论坛模块'
		);
		
		foreach($select_array as $value => $name)
		{
			$selected = ( $value == $default ) ? ' selected="selected"' : '';
			$select .= '<option value="' . $value . '"' . $selected . '>' . $name . '</option>';
		}
		
		$select .= '</select>';
		
		return $select;
	}

	/**
	*	选择论坛
	*/
	function select_forum($default, $select_name)
	{
		$sql = 'SELECT forum_id, forum_name
			FROM ' . FORUMS_TABLE;

		if (!$result = $this->_db->sql_query($sql))
		{
			trigger_error('无法取得论坛信息', E_USER_WARNING);
		}

		$select = '<select name="' . $select_name . '">';
		while ($row = $this->_db->sql_fetchrow($result))
		{
			$selected = ( $row['forum_id'] == $default ) ? ' selected="selected"' : '';
			$select .= '<option value="' . $row['forum_id'] . '"' . $selected . '>' . $row['forum_name'] . '</option>';
		}

		$select .= '</select>';
		
		return $select;
	}

	/**
	*	生成各种链接
	*/
	function gen_link($module_type, $module_title, $module_param, $module_needle)
	{
		if ($module_title == '')
		{
			return '';
		}
		
		switch ($module_type)
		{
			case MODULE_COMMON:
				if (defined('IN_ADMIN'))
				{
					return '<a href="' . append_sid(ROOT_PATH . 'admin/admin_module.php?page=' . $module_needle) . '">' . $module_title . '</a>';
				}
				else
				{
					return '<a href="' . append_sid('index.php?page=' . $module_needle) . '">' . $module_title . '</a>';
				}
				break;
			case MODULE_VIEWFORUM:
				return '<a href="' . append_sid(ROOT_PATH . 'viewforum.php?' . POST_FORUM_URL . '=' . $module_param) . '">' . $module_title . '</a>';
				break;
			default:
				return $module_title;
		}
	}

}
?>