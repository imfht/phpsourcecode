<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 分布式文件存储类
class Storage
{

	/**
	 * 操作句柄
	 *
	 * @var string
	 * @access protected
	 */
	static protected $handler;

	/**
	 * 连接分布式文件系统
	 *
	 * @access public
	 * @param string $type   文件类型
	 * @param array $options 配置数组
	 * @return void
	 */
	static public function connect($type, $options = array())
	{
		$class = 'Think_Storage_Driver_' . $type;
		self::$handler = new $class($options);
	}

	// 读取文件内容
	public static function read($filename, $type = '')
	{
		self::$handler->read($filename, $type);
	}

	// 写文件
	public static function put($filename, $content, $type = '')
	{
		self::$handler->put($filename, $content, $type);
	}

	// 文件追加
	public static function append($filename, $content, $type = '')
	{
		self::$handler->append($filename, $content, $type);
	}

	//加载文件
	public static function load($filename, $vars = null, $type = '')
	{
		self::$handler->load($filename, $vars, $type);
	}

	// 判断文件是否存在
	public static function has($filename, $type = '')
	{
		self::$handler->has($filename, $type);
	}

	// 删除文件
	public static function get($filename, $name, $type = '')
	{
		self::$handler->get($filename, $name, $type);
	}
}

class Think_Storage extends Storage
{
}