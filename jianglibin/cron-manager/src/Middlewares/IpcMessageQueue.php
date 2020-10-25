<?php
namespace SuperCronManager\Middlewares; 

use SuperCronManager\Interfaces\MiddlewareInterface;
/**
 * Linux下的消息队列扩展封装类
 * @author godv
 */
class IpcMessageQueue implements MiddlewareInterface
{
	/**
	 * 队列资源句柄
	 * @var result
	 */
	private $handle;

	/**
	 * 每次读取数据大小
	 * @var integer
	 */
	public $readSize = 65535;

	/**
	 * 是否阻塞
	 * @var boolean
	 */
	public $blocking = false;

	public function __construct($key, $blocking = false)
	{
		$this->handle = msg_get_queue($key);
		$this->blocking = $blocking;
	}

	/**
	 * 入队
	 * @param  string $key  队列key
	 * @param  mixed $data 数据
	 * @return boolean
	 */
	public function push($key, $data)
	{
		return msg_send($this->handle, $key, $data, false, $this->blocking, $errcode);
	}

	/**
	 * 出队(非阻塞式)
	 * @param  string $key 队列key
	 * @return string/false
	 */
	public function pop($key)
	{
		$message = false;
		if ($this->blocking) {
        	msg_receive($this->handle, $key, $type, $this->readSize, $message, false);
		} else {
        	msg_receive($this->handle, $key, $type, $this->readSize, $message, false, MSG_IPC_NOWAIT);
		}
        return $message;
	}

	/**
	 * 获取队列里的消息数量
	 * @return interge
	 */
	public function getMessageNum()
	{
		$stat = msg_stat_queue($this->handle);
		return $stat['msg_qnum'];
	}
	/**
	 * 关闭连接
	 * @return void
	 */
	public function close()
	{
        msg_remove_queue($this->handle);
	}

}
