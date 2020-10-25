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

$opts = getopt('s:h', ['server:', 'help']);

if (isset($opts['h']) || isset($opts['help'])) {
    $script = array_shift($GLOBALS['argv']);
    $help = <<<EOF
Start a gearman client.

Usage:
  $script -s HOST[:PORT]

Options:
  -s,--server HOST[:PORT]  Connect to server HOST and optional PORT(default 127.0.0.1:4730)
  -h,--help                Show this help information
     --debug               Debug mode
     --dump                Dump all config data
\n
EOF;
    fwrite(\STDOUT, $help);
    exit(0);
}

$client = new \inhere\gearman\client\JobClient([
    'servers' => isset($opts['s']) ? $opts['s'] : (isset($opts['server']) ? $opts['server'] : ''),
]);

$ret[] = $client->doNormal('test_reverse', 'hello a');
$ret[] = $client->doBackground('test_reverse', 'hello b');
$ret[] = $client->doBackground('test_reverse', 'hello c');
$ret[] = $client->doHighBackground('test_reverse', 'hello d');

$ret[] = $client->doBackground('test_job', 'hello welcome');

$ret[] = $client->doBackground('test_echo', 'hello welcome!!');

var_dump($ret);
