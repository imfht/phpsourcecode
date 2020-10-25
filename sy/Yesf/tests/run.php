<?php
use Swoole\Process;
use Swoole\Event;

$worker = new Process(function() use ($argc, $argv) {
	cli_set_process_title("PHPUnit Worker");
	go(function() use ($argc, $argv) {
		require(__DIR__ . '/../vendor/phpunit/phpunit/phpunit');
	});
	Event::wait();
}, true, 1);

$buf = "";

Process::signal(SIGCHLD, function($sig) use (&$buf) {
	while($ret =  Process::wait(false)) {
		// Process exited
		if (strpos($buf, 'OK') !== false) {
			exit(0);
		} else {
			exit(1);
		}
	}
});

cli_set_process_title("PHPUnit Main");
swoole_event_add($worker->pipe, function ($pipe) use (&$buf, $worker) {
	$ret = $worker->read();
	$buf .= $ret;
	echo $ret;
});

$worker->start();
