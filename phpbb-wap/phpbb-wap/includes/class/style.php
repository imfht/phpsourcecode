<?php

/**
* phpBB-WAP风格系统
*/

class Style
{
	
	var $style = '';
	var $name = '';
	var $path = '';
	var $data = array();

	var $_db = NULL;
	var $_userdata = NULL;
	var $_config = NULL;
	var $_cache;

	function __construct()
	{
		global $db, $cache, $userdata, $board_config;

		$this->_db = $db;
		$this->_userdata = $userdata;
		$this->_config = $board_config;
		$this->_cache = $cache;

		$this->Data();
		
		if ($this->_userdata['user_id'] == ANONYMOUS)
		{
			$this->style = $this->_config['default_style'];

		}
		else
		{
			$this->style = $this->_userdata['user_style'];
		}

		$this->name = $this->data[$this->style]['name'];
		$this->path = $this->data[$this->style]['path'];
	}

	function Setup()
	{
		global $template;

		$template = new Template(ROOT_PATH . 'styles/' . $this->path . '/');

	}

	function Data()
	{
		// 如果风格数据不存在则重新写入缓存
		if (!$this->data = $this->_cache->read('data_styles'))
		{
			$this->data = $this->Updata();
			$this->_cache->write('data_styles', $this->data);
		}
	}

	function Clear()
	{
		$this->_cache->clear('data_styles');
	}

	function Updata()
	{
		$sql = 'SELECT style_id, style_path, style_name, style_version, style_copyright
			FROM ' . STYLES_TABLE;

		if (!$result = $this->_db->sql_query($sql))
		{
			trigger_error('无法查询风格的信息', E_USER_WARNING);
		}

		$style_data = array();

		while ($row = $this->_db->sql_fetchrow($result))
		{
			$style_data[$row['style_id']] = array(
				'name' => $row['style_name'],
				'path' => $row['style_path'],
				'copy' => $row['style_copyright'],
				'version' => $row['style_version']
			);
		}

		return $style_data;
	}
}


?>