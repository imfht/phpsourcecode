<?php
namespace SyApp;

class Console {
	public static function run($container) {
		$GLOBALS['is_console_run'] = 1;
	}
}