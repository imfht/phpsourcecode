<?php
/**
 * File日志封装类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Log\Adapter;

use Swoole\Coroutine as co;
use Yesf\Yesf;
use Yesf\Log\Logger;
use Yesf\Log\LoggerTrait;
use Psr\Log\LoggerInterface;

class File implements LoggerInterface {
	use LoggerTrait;
	public function __construct() {
		$this->handler = fopen($this->path . '/' . date('Y_m_d') . '_' . getmypid(), 'a');
	}
	/**
	 * 记录日志主函数
	 * @access public
	 * @param string $type 日志类型
	 * @param string $message 日志内容
	 */
	public function log($level, $message, array $context = []) {
		if (!Logger::check($level)) {
			return;
		}
		if (count($context) > 0) {
			foreach ($context as $k => $v) {
				$message = str_replace('{' . $k . '}', $v, $message);
			}
		}
		$content = sprintf("%s | %s | %s | %s", date('Y-m-d H:i:s'), $this->getLevel($level), microtime(true), $message);
		co::fwrite($this->handler, $content);
	}
	private function getLevel($level) {
		switch ($level) {
			case Logger::LOG_DEBUG:
				return 'DEBUG';
			case Logger::LOG_NOTICE:
				return 'NOTICE';
			case Logger::LOG_WARNING:
				return 'WARNING';
			case Logger::LOG_ERROR:
				return 'ERROR';
			case Logger::LOG_CRITICAL:
				return 'CRITICAL';
			case Logger::LOG_ALERT:
				return 'ALERT';
			case Logger::LOG_EMERGENCY:
				return 'EMERGENCY';
		}
	}
	public function __destory() {
		@fclose($this->handler);
	}
}