<?php

namespace app\helpers;

class Logger{
	
	public static function add($msg) {
		printf("%s: %s \n", date('H:i:s'), $msg);
	}
	
}