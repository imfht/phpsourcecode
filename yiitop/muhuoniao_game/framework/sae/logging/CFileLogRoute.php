<?php
/**
 * SAELogRoute，使用sae_debug()记录log信息
 * @author biner <huanghuibin@gmail.com>
 */
class CFileLogRoute extends CLogRoute
{
	/**
	 * Sends log messages to specified email addresses.
	 * @param array $logs list of log messages
	 */
	protected function processLogs($logs)
	{
		$message='';
		foreach($logs as $log)
			$message.=$this->formatLogMessage($log[0],$log[1],$log[2],$log[3]);
		#$message=wordwrap($message,70);
    	@sae_debug($message);
	}

}
