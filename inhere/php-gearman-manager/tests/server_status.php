<?php
/**
 * usage: php examples/telnet.php 127.0.0.1 4730
 */

use inhere\gearman\Helper;

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/simple-autoloader.php';

global $argv;
$opts = getopt('h', ['help']);

if (isset($opts['h']) || isset($opts['help'])) {
    $script = array_shift($argv);
    $script = Helper::color($script, Helper::$styles['light_green']);
    $help = <<<EOF
Start a telnet client.

Usage:
  $script HOST [PORT]

Options:
  -h,--help  Show this help information
\n
EOF;
    exit($help);
}

$host = isset($argv[1]) ? $argv[1] : '127.0.0.1';
$port = isset($argv[2]) ? $argv[2] : 4730;

echo "Connect to the gearman server {$host}:{$port}\n";

$tt = new \inhere\gearman\tools\Telnet($host, $port);

// var_dump($tt);die;

echo $tt->command('status');
//$tt->watch('status');
// $tt->interactive();
/*
gearmand commands:

status
workers
version

 */
