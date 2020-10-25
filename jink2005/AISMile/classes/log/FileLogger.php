<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */
class FileLoggerCore extends AbstractLogger
{
	protected $filename = '';

	/**
	* Write the message in the log file
	*
	* @param string message
	* @param level
	*/
	protected function logMessage($message, $level)
	{
		$formatted_message = '*'.$this->level_value[$level].'* '."\t".date('Y/m/d - H:i:s').': '.$message."\r\n";
		return file_put_contents($this->getFilename(), $formatted_message, FILE_APPEND);
	}

	/**
	* Check if the specified filename is writable and set the filename
	*
	* @param string filename
	*/
	public function setFilename($filename)
	{
		if (is_writable(dirname($filename)))
			$this->filename = $filename;
		else
			die('Directory '.dirname($filename).' is not writable');
	}

	/**
	* Log the message
	*
	* @param string message
	* @param level
	*/
	public function getFilename()
	{
		if (empty($this->filename))
			die('Filename is empty.');

		return $this->filename;
	}
}

