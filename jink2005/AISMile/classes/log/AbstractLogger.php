<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

abstract class AbstractLoggerCore
{
	public $level;
	protected $level_value = array(
		0 => 'DEBUG',
		1 => 'INFO',
		2 => 'WARNING',
		3 => 'ERROR',
	);

	const DEBUG = 0;
	const INFO = 1;
	const WARNING = 2;
	const ERROR = 3;

	public function __construct($level = self::INFO)
	{
		if (array_key_exists((int)$level, $this->level_value))
			$this->level = $level;
		else
			$this->level = self::INFO;
	}

	/**
	* Log the message
	*
	* @param string message
	* @param level
	*/
	abstract protected function logMessage($message, $level);

	/**
	 * Check the level and log the message if needed
	 *
	 * @param string message
	 * @param level
	 */
	public function log($message, $level = self::DEBUG)
	{
		if ($level >= $this->level)
			$this->logMessage($message, $level);
	}

	/**
	* Log a debug message
	*
	* @param string message
	*/
	public function logDebug($message)
	{
		$this->log($message, self::DEBUG);
	}

	/**
	* Log an info message
	*
	* @param string message
	*/
	public function logInfo($message)
	{
		$this->log($message, self::INFO);
	}

	/**
	* Log a warning message
	*
	* @param string message
	*/
	public function logWarning($message)
	{
		$this->log($message, self::WARNING);
	}

	/**
	* Log an error message
	*
	* @param string message
	*/
	public function logError($message)
	{
		$this->log($message, self::ERROR);
	}
}

