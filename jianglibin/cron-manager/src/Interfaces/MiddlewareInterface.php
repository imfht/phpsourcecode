<?php
namespace SuperCronManager\Interfaces; 

interface MiddlewareInterface
{
	/**
	 * 入队
	 * @param  string $key  队列key
	 * @param  mixed $data 数据
	 * @return boolean
	 */
	public function push($key, $data);

	/**
	 * 出队
	 * @param  string $key 队列key
	 * @return string
	 */
	public function pop($key);

	/**
	 * 获取队列里的消息数量
	 * @return interge
	 */
	public function getMessageNum();

	/**
	 * 关闭连接
	 */
	public function close();
}