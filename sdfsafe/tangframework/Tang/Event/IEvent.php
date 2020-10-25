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
/**
 * 事件接口，每个开发者定义的事件用于框架中，都必须实现IEvent接口
 * @author jibing
 *
 */
interface IEvent
{
	/**
	 * 增加事件
	 * @param string $name 事件名称
	 * @param \Closure|callable $handler 事件处理方法
	 */
	public function  addListener($name,$handler);
	/**
	 * 移除事件
	 * @param string $name 事件名称
	 * @param \Closure|callable $handler 事件处理方法  如果为空，则移除所有
	 */
	public function removeListener($name,$handler = null);
	/**
	 * 移除所有$name事件
	 * @param string $name
	 */
	public function removeAllListeners($name);
	/**
	 * 执行事件
	 * @param string $name 执行的事件名称
	 * @param Object $sendObject 执行的对象
	 * .....还可以跟上需要传递的值例如
	 * PS：当运行的某个事件返回FALSE后，后面的事件句柄将不会执行
	 * <code>
	 * $x->attach('事件名称',执行的对象,'传递的值1','传递的值2');
	 * <code>
	 * ..后面加需要的参数
	 * @return mixed 返回最后一个执行事件返回的值
	 */
	public function attach($name,$sendObject);
    /**
     * 触发事件 自己构建参数
     * 可以设置引用
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    public function attachByParameters($name,array $parameters = array());
}
 