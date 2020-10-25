<?php
/**
 * 基于SeasLog的日志封装类
 * 如果不存在SeasLog，则不会有任何效果，程序照常运行
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

class SeasLog implements LoggerInterface {
	use LoggerTrait;
	public function __construct() {
		if (!class_exists('\\SeasLog')) {
			return;
		}
		if (Yesf::app()->getConfig('logger.path')) {
			\SeasLog::setBasePath(Yesf::app()->getConfig('logger.path'));
		}
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
		//获取SeasLog的常量
		\SeasLog::log($this->getLevel($level), $message, $context, Logger::getName());
	}
	private function getLevel($level) {
		switch ($level) {
			case Logger::LOG_DEBUG:
				return SEASLOG_DEBUG;
			case Logger::LOG_NOTICE:
				return SEASLOG_NOTICE;
			case Logger::LOG_WARNING:
				return SEASLOG_WARNING;
			case Logger::LOG_ERROR:
				return SEASLOG_ERROR;
			case Logger::LOG_CRITICAL:
				return SEASLOG_CRITICAL;
			case Logger::LOG_ALERT:
				return SEASLOG_ALERT;
			case Logger::LOG_EMERGENCY:
				return SEASLOG_EMERGENCY;
		}
	}
}