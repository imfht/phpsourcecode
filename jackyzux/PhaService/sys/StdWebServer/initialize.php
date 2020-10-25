<?php
/**
 * PHP Settings
 */
date_default_timezone_set('PRC'); //默认时区
ini_set('memory_limit', -1); //不限制内存使用
ini_set('default_socket_timeout', -1);//socket没有时间限制
ini_set('set_time_limit', 0);//php执行时间没有限制

/**
 * Path
 */
define('APP_NAME', 'PhaService StdWebServer');
define('BASE_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', BASE_PATH . '/app');

if (!is_dir(BASE_PATH . '/var/pid')) mkdir(BASE_PATH . '/var/pid', 0777, TRUE);
if (!is_dir(BASE_PATH . '/var/log')) mkdir(BASE_PATH . '/var/log', 0777, TRUE);
if (!is_dir(BASE_PATH . '/var/tmp')) mkdir(BASE_PATH . '/var/tmp', 0777, TRUE);
if (!is_dir(BASE_PATH . '/var/cache')) mkdir(BASE_PATH . '/var/cache', 0777, TRUE);

/**
 * Std Web Server Settings
 */

define('RUN_PID_FILE', BASE_PATH . '/var/pid/std_web_server.pid');// pid保存文件名


define('LOG_PATH', BASE_PATH . '/var/log/');// 日志文件目录

// 设置调试模式 先定义等下写静态
$is_debug = TRUE;

// 判断是否 cli 运行
if (php_sapi_name() != 'cli') die('Please use cli Mode to Start!');

// 判断是否已经运行
if (file_exists(RUN_PID_FILE)) {
    // 判断进程是否存在
    $run_pid = file_get_contents(RUN_PID_FILE);
    if (file_exists("/proc/{$run_pid}/")) {
        die(APP_NAME . " is running, pid: {$run_pid}\n");
    }
}

// 保存当前进程pid 感觉用不到
$run_pid = posix_getpid();
file_put_contents(RUN_PID_FILE, $run_pid) || die(APP_NAME . " save pid file error!\n");
printf(APP_NAME . " started, pid: %s\n", $run_pid);


//**** 运行前初始化 ****//

// 判断是否传递后台运行命令
$isRun = !empty($argv['1']) && $argv['1'] == 'start';

// 判断是否传入日志文件名
if (empty($argv['2'])) {
    // 判断日志文件夹是否存在
    file_exists(LOG_PATH) || mkdir(LOG_PATH);
    $log_file = sprintf(LOG_PATH . '/std_web_server_%s_%s.log', date('Ymd'), $run_pid);
} else {
    $log_file = $argv['2'];
}

// 根据运行情况设置调试模式
define('IS_DEBUG', !$isRun && $is_debug);


//**** 启动进程 ****//

// 引入进程文件
require __DIR__ . '/StdWebServer.php';

// 启动服务器
StdWebServer::getInstance($isRun, $log_file);

//**** 结束前处理 ****//

// 删除pid文件
unlink(RUN_PID_FILE);

// 输出结果
echo APP_NAME . " is shutdown!";
