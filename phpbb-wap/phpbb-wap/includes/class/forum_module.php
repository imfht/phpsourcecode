<?php

/**
* 
*/
class Forum_module
{

	var $mod_bbcodes = array();
	var $module_text = '';
	var $_db = NULL;
	var $_template = NULL;
	var $_config;
	var $_userdata;
	var $forum_id;

	var $module_id;
	var $module_top = '';
	var $module_bottom = '';

	function __construct($forum_id)
	{
		global $userdata, $template, $db, $board_config;

		$this->forum_id = $forum_id;
		$this->_template = $template;
		$this->_db = $db;
		$this->_config = $board_config;
		$this->_userdata = $userdata;

		$this->mod_bbcodes = array(
			'[br]'			=> '<br />',
			'[n]'			=> '&nbsp;',
			'[当前时间]'	=> create_date('G:i', time(), $this->_config['board_timezone']),
			'[年]'			=> create_date('Y', time(), $this->_config['board_timezone']),
			'[月]'			=> create_date('n', time(), $this->_config['board_timezone']),
			'[日]'			=> create_date('j', time(), $this->_config['board_timezone']),
			'[时]'			=> create_date('G', time(), $this->_config['board_timezone']),
			'[分]'			=> create_date('i', time(), $this->_config['board_timezone']),
			'[秒]'			=> create_date('s', time(), $this->_config['board_timezone']),
			'[星期]'		=> $this->bbcode_week(),
			'[时刻]'		=> $this->bbcode_hello(),
			'[用户名]'		=> $this->_userdata['username'],
			'[ID]'			=> $this->_userdata['user_id'],
			'[金币]'		=> $this->_userdata['user_points']
		);
		
		$this->obtain_module();
	}

	function parse_module($module_text)
	{
		$this->module_text = ' ' . $module_text;
		// 如果没有设置任何UBB则返回
		if ( !(strpos($this->module_text, '[') && strpos($this->module_text, ']')) ) return substr($this->module_text, 1);
		foreach ($this->mod_bbcodes as $str_find_tag => $str_replace_tag )
		{
			$this->module_text = str_replace($str_find_tag, $str_replace_tag, $this->module_text);
		}
		
		$this->module_text = preg_replace_callback('#\[html\](.*?)\[/html\]#s', 'Forum_module::bbcode_html', $this->module_text);

		return $this->module_text;
	}

	static function bbcode_html($html)
	{
		return htmlspecialchars_decode($html[1], ENT_QUOTES);
	}

	function bbcode_week() {
		$week_data = create_date('N', time(), $this->_config['board_timezone']);
		switch ($week_data) {
			case 1: return '一'; break;
			case 2: return '二'; break;
			case 3: return '三'; break;
			case 4: return '四'; break;
			case 5: return '五'; break;
			case 6: return '六'; break;
			case 7: return '日'; break;
			default: return'';
		}
		return $week_text;
	}
	
	function bbcode_hello() {
		$hello_time = create_date('H', time(), $this->_config['board_timezone']);
		if ( $hello_time < 5) $hello_text = '凌晨';
		elseif ( $hello_time < 9) $hello_text = '早上';
		elseif ( $hello_time < 14) $hello_text = '中午';
		elseif ( $hello_time < 18) $hello_text = '下午';
		else $hello_text = '晚上';
		return $hello_text;
	}

	/**
	*	创建一个没有内容的模块
	*/
	function create_module()
	{
		$sql = 'SELECT MAX(module_id) AS max_id
			FROM ' . FORUM_MODULE_TABLE;

		if (!$result = $this->_db->sql_query($sql))
		{
			trigger_error('无法获取论坛模块的ID', E_USER_WARNING);
		}

		$row = $this->_db->sql_fetchrow($result);

		$max_id = $row['max_id'] + 1;

		$sql = 'INSERT INTO ' . FORUM_MODULE_TABLE . " (module_id, module_forum, module_top, module_bottom)
			VALUES ($max_id, $this->forum_id, '', '')";

		if (!$this->_db->sql_query($sql))
		{
			trigger_error('无法创建论坛模块', E_USER_WARNING);
		}
		
	}

	/**
	*	更新模块
	*/
	function update_module($module_top, $module_bottom)
	{
		$sql = 'UPDATE ' . FORUM_MODULE_TABLE . "
			SET module_top = '$module_top',
			module_bottom = '$module_bottom' 
			WHERE module_id = $this->module_id";

		if (!$this->_db->sql_query($sql))
		{
			trigger_error('无法更新论坛模块', E_USER_WARNING);
		}
	}

	/**
	*	从数据库中返回模块的信息
	*/
	function obtain_module()
	{
		$sql = 'SELECT module_id, module_top, module_bottom
			FROM ' . FORUM_MODULE_TABLE . '
			WHERE module_forum = ' . $this->forum_id;

		if (!$result = $this->_db->sql_query($sql))
		{
			trigger_error('无法获得模块数据', E_USER_WARNING);
		}

		if ($this->_db->sql_numrows($result))
		{
			$row = $this->_db->sql_fetchrow($result);

			$this->module_id = $row['module_id'];
			$this->module_top = htmlspecialchars_decode($row['module_top'], ENT_QUOTES);
			$this->module_bottom = htmlspecialchars_decode($row['module_bottom'], ENT_QUOTES);;
		}

	}

	/**
	*	显示顶部的内容
	*/
	function display_top()
	{
		$this->_template->assign_var('FORUM_TOP', $this->parse_module($this->module_top));
	}

	/**
	*	显示底部的内容
	*/
	function display_bottom()
	{
		$this->_template->assign_var('FORUM_BOTTOM', $this->parse_module($this->module_bottom));
	}
}

?>