<?php
namespace SuperCronManager\Interfaces;

interface WorkerInterface
{
	/**
	 * 设置通讯中间件
	 * @param MiddlewareInterface $middleware
	 */
	public function setMiddleware(MiddlewareInterface $middleware);
	
	/**
	 * 设置进程名称
	 * @param string $title
	 * @return void
	 */
	public function setProcTitle($title);

	/**
	 * 处理进程信号
	 */
	public function waitSign();

	/**
	 * 退出worker
	 * @param  integer $exitcode 退出码
	 * @return void
	 */
	public function stop($exitcode);

	/**
	 * 重启worker
	 * @return void
	 */
	public function restart($exitcode);

	/**
	 * worker主循环
	 * @return void
	 */
	public function loop();
	
}