<?php
/**
 * Syslog日志封装类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Log\Adapter;

use Yesf\Yesf;
use Yesf\Log\Logger;
use Yesf\Log\LoggerTrait;
use Psr\Log\LoggerInterface;

class Syslog implements LoggerInterface {
	use LoggerTrait;
	public function __construct() {
		openlog(Logger::getName(), LOG_PID | LOG_NDELAY, LOG_LOCAL0);
	}
	/**
	 * 记录日志主函数
	 * @access public
	 * @param string $level 日志类型
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
		syslog($this->getLevel($level), $message);
	}
	private function getLevel($level) {
		switch ($level) {
			case Logger::LOG_DEBUG:
				return LOG_DEBUG;
			case Logger::LOG_NOTICE:
				return LOG_NOTICE;
			case Logger::LOG_WARNING:
				return LOG_WARNING;
			case Logger::LOG_ERROR:
				return LOG_ERR;
			case Logger::LOG_CRITICAL:
				return LOG_CRIT;
			case Logger::LOG_ALERT:
				return LOG_ALERT;
			case Logger::LOG_EMERGENCY:
				return LOG_EMERG;
		}
	}
	public function __destory() {
		closelog();
	}
}