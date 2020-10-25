<?php
namespace Yurun\Util;

class EventClass
{
	use ClassEvent { trigger as public __trigger; }
	
	/**
	* 实例对象
	* @var EventClass
	*/
	private static $instance;

	private function __construct()
	{
	}

	/**
	 * 单例模式获取实例
	 * @return EventClass
	 */
	public static function getInstance()
	{
		if(null === static::$instance)
		{
			static::$instance = new static;
		}
		return static::$instance;
	}
	
	/**
	 * 触发事件
	 * @param name $event 事件名称
	 * @param array $params 参数
	 * @return mixed
	 */
	public function trigger($event, $params = array())
	{
		return call_user_func_array([$this, '__trigger'], func_get_args());
	}
}