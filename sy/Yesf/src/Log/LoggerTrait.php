<?php
/**
 * Logger Trait
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Log;

use Yesf\Yesf;
use Yesf\Log\Logger;

trait LoggerTrait {
	/**
	 * 以下为各个级别的封装
	 * 方便调用
	 * @access public
	 * @param string $message
	 */
	public function debug($message, array $context = []) {
		$this->log(Logger::LOG_DEBUG, $message, $context);
	}
	public function info($message, array $context = []) {
		$this->log(Logger::LOG_INFO, $message, $context);
	}
	public function notice($message, array $context = []) {
		$this->log(Logger::LOG_NOTICE, $message, $context);
	}
	public function warning($message, array $context = []) {
		$this->log(Logger::LOG_WARNING, $message, $context);
	}
	public function error($message, array $context = []) {
		$this->log(Logger::LOG_ERROR, $message, $context);
	}
	public function critical($message, array $context = []) {
		$this->log(Logger::LOG_CRITICAL, $message, $context);
	}
	public function alert($message, array $context = []) {
		$this->log(Logger::LOG_ALERT, $message, $context);
	}
	public function emergency($message, array $context = []) {
		$this->log(Logger::LOG_EMERGENCY, $message, $context);
	}
}