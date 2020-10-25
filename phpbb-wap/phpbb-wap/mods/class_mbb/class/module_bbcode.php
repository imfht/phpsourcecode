<?php

/**
*	在线统计人数MBB扩展
*/

class class_mbb_module_bbcode
{
	var $_module;

	function class_mbb_module_bbcode(&$module)
	{
		$this->_module = $module;
	}

	function main()
	{
		$this->_module->reg_bbcode_exp('[调用专题_A_B]', '该MBB用与调用专题的帖子<br />A：【专题ID】要调用的专题ID：多个用英文逗号分隔<br />B：【显示条数】显示专题的条数：最多可设置50条');
	}

	function bbcodes()
	{
		$this->_module->reg_bbcodes('call', '#\[调用专题_([1-9][0-9]{0,7}(\,[1-9][0-9]{0,7})*?)_([1-9]|[1-2]\d|5[0])\]#', array(&$this, 'class_mbb'), 'medium');
	}

	function class_mbb($canshu)
	{
		$class_id = $canshu[1];
		$limit = $canshu[3];

		$sql = 'SELECT topic_id, topic_title
			FROM ' . TOPICS_TABLE . '
			WHERE topic_class = ' . $class_id . '
			ORDER BY topic_id DESC
			LIMIT 0, ' . $limit;

		if ( !($result = $this->_module->_db->sql_query($sql)) ) trigger_error('无法统计在线人数', E_USER_WARNING);

		$text = '';

		while ( $row = $this->_module->_db->sql_fetchrow($result) )
		{
			$text .= '<a href="' . append_sid(ROOT_PATH . 'viewtopic.php?t=' . $row['topic_id']) . '">' . $row['topic_title'] . '</a><br />';
		}

		return $text;
	}

}

?>