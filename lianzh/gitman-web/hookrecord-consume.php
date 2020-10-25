#!/usr/bin/env php
<?php

require __DIR__ . '/lib/G.class.php';

set_error_handler('hookrecord_error_handler');
set_exception_handler('hookrecord_exception_handler');

G::$configs = include(__DIR__ . '/config.php');

G::app_init();

function app_init(){	
	if (PHP_SAPI !== 'cli'){
		G::diestr('only run in cli mode!');
	}
}

function log_message($msg){
	G::echo2($msg);
	
	$log_file = __DIR__ . '/_tmp/consume.'.date('Ymd').'.log';
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

	$record = Sql::assistant( G::$ds )->select_row('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_NO,
					'mode' => GitWebhook::DEPLOY_MODE_LOCAL,
			), '*', 'created_at DESC');

	if (empty($record)) return;

	log_message("record: {$record['id']} start " . SqlHelper::timestamp(time()));

	$deploy = Sql::assistant( G::$ds )->select_row('gitman_deploy', array(
					'id' => $record['deploy_id'],
			));

	if (empty($deploy)) {
		hookrecord_invalidHandler($record, '无效 deploy');
		return;
	}

	if ($deploy['xstatus'] != GitWebhook::XSTATUS_ENABLE) {
		hookrecord_invalidHandler($record, 'deploy 被禁用');
		return;
	}	

	$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'id' => $record['repository_id'],
			));
	if (empty($repository)) {
		hookrecord_invalidHandler($record, '无效 repository');
		return;
	}

	// 同一分支仅执行最新提交记录的pull请求,并对其之前未执行的 hookrecod 做忽略处理
	Sql::assistant( G::$ds )->update('gitman_hookrecord', array(
			'do_status' => GitWebhook::DO_STATUS_IGNORE,
			'do_at' => time(),
			'do_msg' => "IGNORE by {$record['id']}/" . SqlHelper::timestamp($record['created_at']),
		), array(
			'do_status' => GitWebhook::DO_STATUS_NO,
			'deploy_id'	=> $record['deploy_id'],
			'repository_id'	=> $record['repository_id'],
			// 不包括自身的
			'id'	=> array($record['id'], '!='),
			// 创建时间小于当前记录的
			'created_at'	=> array($record['created_at'], '<='),			
		));
	
	// 修改当前记录的执行状态为 开始
	hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_START, 'start ..');

	$code_dir = $deploy['code_dir'];
	$branch_origin = $deploy['branch_origin'];
	$extra_commands = $deploy['extra_commands'];

	if (empty($code_dir) || !is_dir($code_dir))
	{
		hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_FAILED, "code_dir is not a dir");
		return;
	}

	if (empty($branch_origin))
	{
		hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_FAILED, "branch_origin is null");
		return;
	}

	// 其它的命令
	$extra_commands = G::normalize($extra_commands, ';;;');

	$success = hookrecord_execute_gitpull($code_dir, $branch_origin, $extra_commands, $failedmsg);
	if ($success)
	{
		hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_END, "success");
	}
	else
	{
		hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_FAILED, "Failed: {$failedmsg}");
	}
}

function hookrecord_execute_gitpull($code_dir, $branch_origin, $extra_commands=[], &$failedmsg = ''){

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

	if ($re !== false){
		$return = true;
	}

	return $return;
}

function hookrecord_doStatusHandler($id, $do_status, $do_msg){
	log_message("record: {$id} do_status change " . GitWebhook::dostatusText($do_status));
	Sql::assistant( G::$ds )->update('gitman_hookrecord', array(
			'do_status' => $do_status,
			'do_at' => time(),
			'do_msg' => $do_msg,
		), array(
			'id'	=> $id,
		));
}

function hookrecord_invalidHandler(array $record, $errorMsg='invalid'){

	log_message("record: {$record['id']} invalid {$errorMsg}");
	// 将此分支对应的这一批未开始的记录都设置成无效

	Sql::assistant( G::$ds )->update('gitman_hookrecord', array(
			'do_status' => GitWebhook::DO_STATUS_INVALID,
			'do_at' => time(),
			'do_msg' => $errorMsg,
		), array(
			'do_status' => GitWebhook::DO_STATUS_NO,
			'deploy_id'	=> $record['deploy_id'],
			'repository_id'	=> $record['repository_id'],
		));

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
