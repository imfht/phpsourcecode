<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------


$debug = 1;

if($debug) {
	$Drive = 'Database';
} else {
	$Drive = 'Redis';
}

$Queue = [
	'Database' => [
		'connector'  => 'Database',
		'expire'     => 60,
		'default'    => 'default',
		'table'      => 'queue',
		'dsn'        => [],
	],
	'Redis'    => [
		'connector'  => 'Redis',
		'expire'     => 60,
		'default'    => 'default',
		'host'       => '127.0.0.1',
		'port'       => 6379,
		'password'   => '',
		'select'     => 0,
		'timeout'    => 0,
		'persistent' => false,
	],
];

return $Queue[$Drive];

