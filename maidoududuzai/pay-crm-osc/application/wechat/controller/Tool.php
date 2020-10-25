<?php

namespace app\wechat\controller;

class Tool
{

	const SEX = [
		0 => '',
		1 => '男',
		2 => '女',
	];

	public function __construct()
	{
	
	}

	public static function log($content = '', $WeSdk = 'WeChat')
	{

		if(!in_array($WeSdk, ['WeOpen', 'WeChat'])) {
			return;
		}

		if(!empty($content)) {
			if(is_array($content) || is_object($content)) {
				$content = ToArray($content);
			} else {
				if(json_decode($content)) {
					$content = json_decode($content, true);
				}
			}
			if(is_array($content)) {
				foreach($content as $key => $value) {
					if(!is_array($value) && is_array(json_decode($value, true))) {
						$content[$key] = json_decode($value, true);
					}
				}
			}
		}
		if(!empty($content)) {
			$LOG_DIR = TEMP_PATH . $WeSdk;
			if(!is_dir($LOG_DIR)) {
				mkdir($LOG_DIR, 0777, TRUE);
			}
			$content = JSON($content);
			file_put_contents($LOG_DIR . '/' . $WeSdk . '_' . gsdate('Ym') . '.log', '[' . gsdate('H:i:s') . ']' . ' ' . $content . "\r\n", FILE_APPEND);
		}

	}

}

