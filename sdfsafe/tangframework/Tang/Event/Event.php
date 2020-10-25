<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Event;
use Tang\Exception\IsNotCallableException;

/**
 * Event
 * Class Event
 * @package Tang\Event
 */
class Event implements IEvent
{
    /**
     * 事件列表
     * @var array
     */
    private $listeners = array();

    /**
     * 增加事件
     * $handler回调函数里面只要return false;那么接下来的事件将不会执行
     * @param string $name
     * @param callable $handler
     */
    public function addListener($name,$handler)
	{
        if(!is_callable($handler))
        {
            throw new IsNotCallableException();
        }
		if(!isset($this->listeners[$name]))
		{
			$this->listeners[$name] = array();
		}
		$this->listeners[$name][] = $handler;
	}

    /**
     * 移除事件
     * $handler为null的时候，则删除所有$name事件
     * @param string $name
     * @param null $handler
     */
    public function removeListener($name,$handler = null)
	{
		if(!isset($this->listeners[$name]))
		{
			return;
		} else if($handler == null)
		{
			unset($this->listeners[$name]);
		} else if(($index = array_search($handler, $this->listeners[$name])) !== false)
		{
			unset($this->listeners[$name][$index]);
		}
	}

    /**
     * 移除$name所有事件
     * @param string $name
     */
    public function removeAllListeners($name)
	{
		$this->removeListener($name);
	}

    /**
     * 触发事件
     * @param string $name
     * @param Object $sendObject
     * @return mixed
     */
    public function attach($name,$sendObject)
	{
		$parameters = func_get_args();
		array_shift($parameters);
		return $this->attachByParameters($name,$parameters);
	}

    /**
     * 触发事件 自己构建参数
     * 可以设置引用
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    public function attachByParameters($name,array $parameters = array())
    {
        if(!isset($this->listeners[$name]) || !is_array($this->listeners[$name]) || !$this->listeners[$name])
        {
            return;
        }
        foreach ($this->listeners[$name] as $handler)
        {
            $result = call_user_func_array($handler,$parameters);
            if($result === false)
            {
                return;
            }
        }
        return $result;
    }
}