#!/usr/bin/env php
<?php

require __DIR__ . '/lib/G.class.php';
require __DIR__ . '/lib/Unirest.class.php';

set_error_handler('hookrecord_error_handler');
set_exception_handler('hookrecord_exception_handler');

G::$configs = include(__DIR__ . '/config.php');

G::app_init();

function app_init(){	
	if (PHP_SAPI !== 'cli'){
		G::diestr('only run in cli mode!');
	}

	RConsume::init();
}

function log_message($msg){
	G::echo2($msg);
	
	$log_file = __DIR__ . '/_tmp/r-consume.'.date('Ymd').'.log';
	$level = empty($level) ? '' : "[$level]: ";
	$string = "{$level}{$msg}\n";
	$fp = @fopen($log_file, 'a');
    if ($fp && @flock($fp, LOCK_EX))
    {
        @fwrite($fp, $string);
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
}

function app_index(){

	log_message(sprintf("%s runing", SqlHelper::timestamp(time())));

	while (1)
	{
		try{
			hookrecord_execute();
		}
		catch(Exception $ex)
		{
			// 自行处理,不中止程序执行
			hookrecord_exception_handler($ex);
		}

		sleep(5);
	}
}

function hookrecord_execute(){
	
	$upstatus = 1;

	$one = RConsume::dealone($upstatus);

	// 空就继续等
	if (empty($one)) return;

	G::dump($one, 'RConsume::dealone');

	$code_dir = $one['code_dir'];
	$branch_origin = $one['branch_origin'];
	$extra_commands = $one['extra_commands'];

	$report = array(
			'record_id'	=> $one['record_id'],
			'do_status'	=> null,
			'do_msg'	=> null,
		);

	do {

		if (!is_dir($code_dir))
		{
			$report['do_status'] = GitWebhook::DO_STATUS_FAILED;
			$report['do_msg'] = "code_dir is not a dir";
			break;
		}

		// 其它的命令
		$extra_commands = G::normalize($extra_commands, ';;;');
		
		$success = hookrecord_execute_gitpull($code_dir, $branch_origin, $extra_commands);
		if ($success)
		{
			$report['do_status'] = GitWebhook::DO_STATUS_END;
			$report['do_msg'] = "success";

			break;
		}
		else
		{
			$report['do_status'] = GitWebhook::DO_STATUS_FAILED;
			$report['do_msg'] = "failed";

			break;
		}
	}
	while (false);

	$rSt = RConsume::report($report['record_id'], $report['do_status'], $report['do_msg']);

	if ($rSt) {
		G::echo2('success');
	}
	else {
		G::echo2('failed');
	}
}

function hookrecord_execute_gitpull($code_dir, $branch_origin, $extra_commands=[]){

	$return = false;

	$commands = array(		
		"cd {$code_dir}",
		"git reset --hard",
		"git pull origin {$branch_origin}"
	);

	$commands = array_merge($commands, $extra_commands);

	$commands = array_map('escapeshellcmd', $commands);

	// 可以使用 ; 或者 && 来联立指令
	$cmd_line = implode('&&', $commands);

	log_message("[commands]: " . $cmd_line);

	system($cmd_line, $re);

	// windows cannot get return
	$winis = 'win' === strtolower(substr(php_uname("s"), 0, 3));

	if ($winis) {
		$return = true;
	}
	else {
		if ($re !== false){
			$return = true;
		}
	}

	return $return;
}

function hookrecord_invalidHandler(array $record, $errorMsg='invalid'){
	log_message("record: {$record['id']} invalid {$errorMsg}");
}

function hookrecord_error_handler($errno, $errstr, $errfile, $errline)
{
	$args = [
		'errno'	=> $errno,
		'errstr'	=> $errstr,
		'errfile'	=> $errfile,
		'errline'	=> $errline,
	];

	log_message( G::dump($args, 'error', true) );

	/* Don't execute PHP internal error handler */
    return true;
}

function hookrecord_exception_handler($e)
{
	if (is_object($e))
	{
		$msg = $e->getMessage();
		$trace = $e->getTraceAsString();

		log_message("[Exception]: {$msg}\n{$trace}");
	}	
}
