<?php

/**
*	聊天功能首页MBB扩展类
*/

class bbchat_module_bbcode
{
	var $_module;
	var $ad = array();

	function bbchat_module_bbcode(&$module)
	{
		$this->_module = $module;
	}

	function main()
	{
		$this->_module->reg_bbcode_exp('[聊天动态]', '调用1条聊天动态');
	}

	function bbcodes()
	{
		$this->_module->reg_bbcodes('str', '[聊天动态]', $this->index_chat(1));
	}

	function index_chat($limit)
	{
		global $table_prefix;

		if (!function_exists('smilies_pass'))
		{
			require_once ROOT_PATH . 'includes/functions/bbcode.php';
		}

		$sql = 'SELECT shout_username, shout_user_id, shout_text, shout_session_time
			FROM ' . $table_prefix .'shout
			ORDER BY shout_session_time DESC
			LIMIT 0, ' . $limit;
		if (!$result = $this->_module->_db->sql_query($sql)) trigger_error('查询聊天数据出错', E_USER_WARNING);

		$text = '';

		while ($row = $this->_module->_db->sql_fetchrow($result))
		{
			$text .= '<a href="' . append_sid('ucp.php?mode=viewprofile&u=' . $row['shout_user_id']) . '">' . $row['shout_username'] . '</a>在' . create_date('G点i分', $row['shout_session_time'], $this->_module->_config['board_timezone']) . '说了：' . smilies_pass($row['shout_text']) . '<br />';
		}
		return $text;
	}

}