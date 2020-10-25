<?php
namespace Yurun\Util;

trait ClassEvent
{
	/**
	 * 事件绑定记录
	 */
	protected $yurunEvents = array();

	/**
	 * 注册事件(监听)
	 * @param string $event 事件名称
	 * @param mixed $callback 回调
	 * @param bool $first 是否优先执行，以靠后设置的为准
	 * @param bool $once 是否只执行一次
	 */
	public function register($event, $callback, $first = false, $once = false)
	{
		if (!isset($this->yurunEvents[$event]))
		{
			$this->yurunEvents[$event] = array();
		}
		$item = array(
			'callback'	=>	$callback,
			'once'		=>	$once,
		);
		if($first)
		{
			array_unshift($this->yurunEvents[$event], $item);
		}
		else 
		{
			$this->yurunEvents[$event][] = $item;
		}
	}

	/**
	 * 注册事件(监听)，register的别名
	 * @param string $event 事件名称
	 * @param mixed $callback 回调
	 * @param bool $first 是否优先执行，以靠后设置的为准
	 */
	public function on($event, $callback, $first = false)
	{
		$this->register($event, $callback, $first);
	}

	/**
	 * 注册一次性事件(监听)
	 * @param string $event 事件名称
	 * @param mixed $callback 回调
	 * @param boolean $first 是否优先执行，以靠后设置的为准
	 */
	public function once($event, $callback, $first = false)
	{
		$this->register($event, $callback, $first, true);
	}
	
	/**
	 * 触发事件
	 * @param name $event 事件名称
	 * @param array $params 参数
	 * @return mixed
	 */
	protected function trigger($event, $params = array())
	{
		if (isset($this->yurunEvents[$event]))
		{
			$args = func_get_args();
			array_shift($args);
			foreach ($this->yurunEvents[$event] as $key => $item)
			{
				if(true === $item['once'])
				{
					unset($this->yurunEvents[$event][$key]);
				}
				if(true === call_user_func_array($item['callback'], $args))
				{
					// 事件返回true时不继续执行其余事件
					return true;
				}
			}
			return false;
		}
		return true;
	}

}