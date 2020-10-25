<?php
!defined('VGOTFASTER') && exit('Access Deined');

//记录到系统纪录
function _systemLog($text) {
	global $LOG;
	$LOG[] = $text;
}

/**
 * Show An Error
 *
 * @param string $message
 * @param bool $exit Is Stop Script Running?
 * @param int $debugCalledLevel false or call level
 * @param string $heading
 * @return void
**/
function showError($message, $exit=true, $debugCalledLevel=null, $heading='An Error Occurred') {
	if ($debugCalledLevel !== null) {
		$tmp = debug_backtrace();
		$debug = $tmp[$debugCalledLevel];
		$message = "<p>Message: $message</p><p>Called: {$debug['file']}</p><p>Line: {$debug['line']}</p>";
	}
	include APPLICATION_PATH.'/errors/error.php';
	$exit && exit;
}

/**
 * Stop Running And Show 404 Not Found Error
 *
 * @param string
 * @return void
 */
function showError404($errorCode='') {
	switch($errorCode) {
		case 'controller': $errorCode = 'CTRL404'; break;
		case 'class': $errorCode = 'CLS404'; break;
		case 'action': $errorCode = 'ACT404'; break;
		default: $errorCode = '';
	}
	include APPLICATION_PATH.'/errors/404.php';
	exit;
}

/**
 * Load Config
 *
 * @param string $name
 * @param bool $ignore 当配置不存在时是否忽略错误
 * @return array
 */
function getConfig($name, $ignore=false) {
	if (isset($GLOBALS['CONFIG'][$name])) {
		return $GLOBALS['CONFIG'][$name];
	}

	$configFilePriority = APPLICATION_PATH.'/configs/'.ENVIRONMENT.'/'.$name.'.php';
	$configFileDefault = APPLICATION_PATH.'/configs/'.$name.'.php';

	if (is_file($configFilePriority)) {
		include $configFilePriority;
	} elseif (is_file($configFileDefault)) {
		include $configFileDefault;
	} elseif (!$ignore) {
		showError('No Found Config File: '.$name.'.php');
	}

	_systemLog("Load config '$name'");

	$GLOBALS['CONFIG'][$name] = isset($$name) ? $$name : null;

	return $GLOBALS['CONFIG'][$name];
}
