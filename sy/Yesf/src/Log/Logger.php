<?php
/**
 * 日志封装类
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
use Yesf\DI\Container;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

class Logger implements LoggerAwareInterface {
	const LOG_DEBUG = 0;
	const LOG_INFO = 1;
	const LOG_NOTICE = 2;
	const LOG_WARNING = 3;
	const LOG_ERROR = 4;
	const LOG_CRITICAL = 5;
	const LOG_ALERT = 6;
	const LOG_EMERGENCY = 7;

	/** @var LoggerInterface $logger Logger */
	private $logger = null;

	public static function check($check) {
		static $level = null;
		if ($level === null) {
			$level = Yesf::app()->getConfig('logger.level');
			if ($level === 'none') {
				return false;
			}
			if (defined(self::class . '::LOG_' . strtoupper($level))) {
				$level = constant(self::class . '::LOG_' . strtoupper($level));
			} else {
				$level = self::LOG_WARNING;
			}
		}
		return $check >= $level;
	}
	public static function getName() {
		static $name = null;
		if ($name === null) {
			if (Yesf::app()->getConfig('logger.name')) {
				$name = Yesf::app()->getConfig('logger.name');
			} else {
				$name = Yesf::app()->getConfig('name', Yesf::CONF_PROJECT);
			}
		}
		return $name;
	}
	public function __construct() {
		$className = null;
		switch (Yesf::app()->getConfig('logger.adapter')) {
			case 'saeslog':
				$className = \Yesf\Log\Adapter\SeasLog::class;
				break;
			case 'file':
				$className = \Yesf\Log\Adapter\File::class;
				break;
			case 'syslog':
			default:
				$className = \Yesf\Log\Adapter\Syslog::class;
				break;
		}
		if ($className !== null) {
			$this->setLogger(Container::getInstance()->get($className));
		}
	}
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}
	public function __call($name, $arguments) {
		if (method_exists($this->logger, $name)) {
			$this->logger->$name(...$arguments);
		}
	}
	public static function __callStatic($name, $arguments) {
		Container::getInstance()->get(self::class)->$name(...$arguments);
	}
}