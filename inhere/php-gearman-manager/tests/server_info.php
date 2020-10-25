<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/4/28
 * Time: 下午11:03
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');
require dirname(__DIR__) . '/examples/simple-autoloader.php';

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

$tt = new \inhere\gearman\tools\TelnetGmdServer($host, $port);

print_r($tt->statusInfo());
print_r($tt->workersInfo());
