<?php

if (!function_exists('checkEnvironment')) {
    function checkEnvironment() : void
    {
        if (php_sapi_name() != 'cli') {
            echo '请在cli模式下运行本工具';
            echo PHP_EOL;
            exit(1);
        }
        if (!extension_loaded('swoole')) {
            echo '请安装Swoole2.1.0+';
            echo PHP_EOL;
            exit(1);
        }
        if (!function_exists('go')) {
            echo '请安装Swoole2.1.0+';
            echo PHP_EOL;
            exit(1);
        }
        if (!class_exists('chan')) {
            echo '请安装Swoole2.1.0+';
            echo PHP_EOL;
            exit(1);
        }
        if (!class_exists('Co\Http\Client')) {
            echo '请安装Swoole2.1.0+';
            echo PHP_EOL;
            exit(1);
        }
    }
}

if (!function_exists('getOptions')) {
    function getOptions(array $args, array $option_names) : array
    {
        $option_names_map = [];
        foreach ($option_names as $name) {
            $option_names_map[$name] = $name;
        }

        $options = [];
        foreach ($args as $k => $arg) {
            if (strpos($arg, '-') === 0) {
                if (strpos($arg, '--') === 0) {
                    $option = substr($arg, 2);
                    if (strpos($option, '=') > 0) {
                        $option_arr = explode('=', $option);
                        if (count($option_arr) == 2) {
                            list($option_name, $option_value) = $option_arr;
                        } else {
                            $option_name = $option;
                            $option_value = '';
                        }
                    } else {
                        $option_name = $option;
                        $option_value = '';
                    }
                    if (array_key_exists($option_name, $option_names_map)) {
                        $options[$option_name] = $option_value;
                    }
                } else {
                    $option_name = substr($arg, 1);
                    if (array_key_exists($option_name, $option_names_map)) {
                        $option_value = '';
                        if (isset($args[$k + 1])) {
                            $next_arg = $args[$k + 1];
                            if (strpos($next_arg, '-') === 0) {
                                if (strpos($next_arg, '--') === 0) {
                                    if (!array_key_exists(substr($next_arg, 2), $option_names_map)) {
                                        $option_value = $next_arg;
                                    }
                                } else {
                                    if (!array_key_exists(substr($next_arg, 1), $option_names_map)) {
                                        $option_value = $next_arg;
                                    }
                                }
                            } else {
                                $option_value = $next_arg;
                            }
                        }
                        $options[$option_name] = $option_value;
                    }
                }
            }
        }
        return $options;
    }
}

if (!function_exists('output')) {
    function output(array $params, bool $test = false) : void
    {
        //Clear stdout
        !$test && system('clear');

        $executedTimes = $params['executedTimes'] ?? 0;
        $totalTime = $params['totalTime'] ?? 0;
        $maxTime = $params['maxTime'] ?? 0;
        $minTime = $params['minTime'] ?? 0;
        $successTimes = $params['successTimes'] ?? 0;
        $successTotalTime = $params['successTotalTime'] ?? 0;
        $successMaxTime = $params['successMaxTime'] ?? 0;
        $successMinTime = $params['successMinTime'] ?? 0;
        $failedTimes = $params['failedTimes'] ?? 0;
        $failedTotalTime = $params['failedTotalTime'] ?? 0;
        $failedMaxTime = $params['failedMaxTime'] ?? 0;
        $failedMinTime = $params['failedMinTime'] ?? 0;
        $qps = $params['qps'] ?? 0;
        $i = $params['i'] ?? 0;
        $avgQps = $params['avgQps'] ?? 0;
        $memoryUsage = $params['memoryUsage'] ?? 0;

        echo '请求并发: ';
        echo $i;
        echo PHP_EOL;
        echo '请求总数: ';
        echo $executedTimes;
        echo PHP_EOL;
        echo '平均耗时: ';
        echo $executedTimes > 0 ? ($totalTime / $executedTimes) * 1000 : 0;
        echo '毫秒';
        echo PHP_EOL;
        echo '最大耗时: ';
        echo $maxTime * 1000;
        echo '毫秒';
        echo PHP_EOL;
        echo '最小耗时: ';
        echo $minTime * 1000;
        echo '毫秒';
        echo PHP_EOL;
        echo '成功请求总数: ';
        echo $successTimes;
        echo PHP_EOL;
        echo '成功率: ';
        echo $executedTimes > 0 ? ($successTimes / $executedTimes) * 100 : 0;
        echo '%';
        echo PHP_EOL;
        echo '成功平均耗时: ';
        echo $successTimes > 0 ? ($successTotalTime / $successTimes) * 1000 : 0;
        echo '毫秒';
        echo PHP_EOL;
        echo '成功最大耗时: ';
        echo $successMaxTime * 1000;
        echo '毫秒';
        echo PHP_EOL;
        echo '成功最小耗时: ';
        echo $successMinTime * 1000;
        echo '毫秒';
        echo PHP_EOL;
        echo '失败请求总数: ';
        echo $failedTimes;
        echo PHP_EOL;
        echo '失败率: ';
        echo $executedTimes > 0 ? ($failedTimes / $executedTimes) * 100 : 0;
        echo '%';
        echo PHP_EOL;
        echo '失败平均耗时: ';
        echo $failedTimes > 0 ? ($failedTotalTime / $failedTimes) * 1000 : 0;
        echo '毫秒';
        echo PHP_EOL;
        echo '失败最大耗时: ';
        echo $failedMaxTime * 1000;
        echo '毫秒';
        echo PHP_EOL;
        echo '失败最小耗时: ';
        echo $failedMinTime * 1000;
        echo '毫秒';
        echo PHP_EOL;
        echo '实时QPS: ';
        echo $qps;
        echo PHP_EOL;
        echo '平均QPS: ';
        echo $avgQps;
        echo PHP_EOL;
        echo '内存占用: ';
        echo $memoryUsage / 1000;
        echo 'KB';
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
    }
}

if (!function_exists('help')) {
    function help() : void
    {
        echo <<<EOF
功能描述：
A simple stress tester based on swoole coroutine.

使用方式：
# GET
php start.php -c 100 -n 1000 -host www.baidu.com -uri / -port 443 -ssl 1 -step 1

# POST
php start.php -c 100 -n 1000 -host www.baidu.com -uri / -port 443 -ssl 1 -step 1 -http_method POST -http_body {\"foo\":\"bar\"}

# PUT
php start.php -c 100 -n 1000 -host www.baidu.com -uri / -port 443 -ssl 1 -step 1 -http_method PUT -http_body {\"foo\":\"bar\"}

# DELETE
php start.php -c 100 -n 1000 -host www.baidu.com -uri / -port 443 -ssl 1 -step 1 -http_method DELETE -http_body {\"foo\":\"bar\"}

输出示例：
测试中...
最大请求并发: 100
请求并发: 100
请求总数: 1000
平均耗时: 41.335484266281毫秒
最大耗时: 165.99607467651毫秒
最小耗时: 25.51007270813毫秒
成功请求总数: 1000
成功率: 100%
成功平均耗时: 41.335484266281毫秒
成功最大耗时: 165.99607467651毫秒
成功最小耗时: 25.51007270813毫秒
失败请求总数: 0
失败率: 0%
失败平均耗时: 0毫秒
失败最大耗时: 0毫秒
失败最小耗时: 0毫秒
实时QPS: 1000
平均QPS: 1000
内存占用: 2012.72KB

参数说明:
1. -c Concurrency
2. -n Requests
3. -host Hostname
4. -uri Uri
5. -port Port
6. -ssl SSL
7. -step Concurrency Step
8. -http_method HTTP Method
9. -http_body HTTP Body
10. -memory_limit Memory Limit
EOF;
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
    }
}

if (!function_exists('outputToCsv')) {
    function outputToCsv($params, $reportId)
    {
        $executedTimes = $params['executedTimes'] ?? 0;
        $totalTime = $params['totalTime'] ?? 0;
        $maxTime = $params['maxTime'] ?? 0;
        $minTime = $params['minTime'] ?? 0;
        $successTimes = $params['successTimes'] ?? 0;
        $successTotalTime = $params['successTotalTime'] ?? 0;
        $successMaxTime = $params['successMaxTime'] ?? 0;
        $successMinTime = $params['successMinTime'] ?? 0;
        $failedTimes = $params['failedTimes'] ?? 0;
        $failedTotalTime = $params['failedTotalTime'] ?? 0;
        $failedMaxTime = $params['failedMaxTime'] ?? 0;
        $failedMinTime = $params['failedMinTime'] ?? 0;
        $qps = $params['qps'] ?? 0;
        $i = $params['i'] ?? 0;
        $avgQps = $params['avgQps'] ?? 0;
        $memoryUsage = $params['memoryUsage'] ?? 0;
        $avgTime = $executedTimes > 0 ? ($totalTime / $executedTimes) * 1000 : 0;
        $successRate = $executedTimes > 0 ? ($successTimes / $executedTimes) * 100 : 0;
        $successAvgTime = $successTimes > 0 ? ($successTotalTime / $successTimes) * 1000 : 0;
        $failRate = $executedTimes > 0 ? ($failedTimes / $executedTimes) * 100 : 0;
        $failAvgTime = $failedTimes > 0 ? ($failedTotalTime / $failedTimes) * 1000 : 0;

        $fd = getReportFd($reportId);
        co::fwrite($fd, implode(',', compact(
                'executedTimes', 'totalTime', 'maxTime', 'minTime', 'successTimes', 'successTotalTime',
                'successMaxTime', 'successMinTime', 'failedTimes', 'failedTotalTime', 'failedMaxTime', 'failedMinTime',
                'qps', 'i', 'avgQps', 'memoryUsage', 'avgTime', 'successRate', 'successAvgTime', 'failRate', 'failAvgTime'
            )) . PHP_EOL);
        fclose($fd);
    }
}

if (!function_exists('getReportFd')) {
    function getReportFd($reportId)
    {
        $csv_filename = 'report_' . $reportId . '.csv';

        //文件不存在，创建
        $csv_file_dir = __DIR__ . '/../reports/';
        $csv_file_path = $csv_file_dir . $csv_filename;
        if (!is_dir($csv_file_dir)) {
            mkdir($csv_file_dir, 0777, true);
        }
        if (!file_exists($csv_file_path)) {
            touch($csv_file_path);
        }

        return fopen($csv_file_path, 'ab');
    }
}

if (!function_exists('getReportId')) {
    function getReportId()
    {
        date_default_timezone_set('PRC');
        return (new DateTime())->format('YmdHi') . (string) intval(microtime(true) * 10000);
    }
}
