<?php

$extName = 'tideways';

// 检测扩展是否存在
if (!extension_loaded($extName)) {
    error_log('phpAnalysis - php extension '. $extName .'must be loaded');
    return;
}

$flagsNoBuiltins = constant(strtoupper($extName) . '_FLAGS_NO_BUILTINS');
$flagsCpu = constant(strtoupper($extName) . '_FLAGS_CPU');
$flagsMemory = constant(strtoupper($extName) . '_FLAGS_MEMORY');
$enableFn = "{$extName}_enable";
$disableFn = "{$extName}_disable";

foreach (['PDO', 'pdo_mysql', 'zlib'] as $ext) {
    if (!extension_loaded($ext)) {
        error_log('phpAnalysis - php extension '. $ext .' must be loaded');
        return;
    }
}

if (version_compare(PHP_VERSION, '5.4.0', 'le')){  
    error_log('phpAnalysis - php version must be newer 5.4.0');
    return;
}

$appDir = dirname(__DIR__);
$configure = require_once $appDir . '/config/agent.php';

if (PHP_SAPI == 'cli') {
    $_SERVER['REMOTE_ADDR'] = null;
    $_SERVER['HTTP_HOST'] = null;
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
}

// exclude myself
if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
    //return;
}

// 检测是否分析本次请求
if (rand(1, 100) > $configure['collect_weight']) {
    return;
}

$xhprofOpts = 0;
if ($configure['flags_no_builtins']) {
    $xhprofOpts += $flagsNoBuiltins;
}
if ($configure['flags_cpu']) {
    $xhprofOpts += $flagsCpu;
}
if ($configure['flags_memory']) {
    $xhprofOpts += $flagsMemory;
}

call_user_func($enableFn, $xhprofOpts);

unset($enableFn, $flagsNoBuiltins, $flagsNoBuiltins, $flagsMemory, $xhprofOpts);

register_shutdown_function(function($configure, $disableFn) {

    // 报错信息,直接过滤
    if (!is_null(error_get_last())) {
        //return;
    }
    
    $profile = call_user_func($disableFn);

    if ($configure['ignore_user_abort']) {
        ignore_user_abort(true);
    }

    $requestTime = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    $costMictime = isset($profile['main()']['wt']) ? $profile['main()']['wt'] : 0;
    $memory = isset($profile['main()']['mu']) ? $profile['main()']['mu'] : 0;
    $pmemory = isset($profile['main()']['pmu']) ? $profile['main()']['pmu'] : 0;
    $cpuMictime = isset($profile['main()']['cpu']) ? $profile['main()']['cpu'] : 0;

    $url = '';
    if (PHP_SAPI == 'cli' && isset($_SERVER['argv'])) {
        $cmd = basename($_SERVER['argv'][0]);
        $url = $cmd . ' ' . implode(' ', array_slice($_SERVER['argv'], 1));
    } else {
        if (isset($_SERVER['REQUEST_URI'])) {
            $url .= $_SERVER['REQUEST_URI'];
        }
        $arrUrl = parse_url($url);
        $url = isset($arrUrl['path']) ? $arrUrl['path']: $url;
    }

    $prefixId  = sha1(uniqid(rand(), true));
    $postfixId = sha1(uniqid(rand(), true));
    $arrRequestId = [
        substr($prefixId, 0, 8),  substr($prefixId, 10, 4),
        substr($prefixId, 20, 4), substr($prefixId, 30, 4),
        substr($postfixId, 20, 12),
    ];
    $requestId = implode('-', $arrRequestId);

    $insRequest = [
        'request_id' => $requestId,
        'url' => $url,
        'method' => strtoupper($method),
        'request_time' => $requestTime,
        'cost_time' => $costMictime,
        'host' => $host,
        'memory' => $memory,
        'pmemory' => $pmemory,
        'cpu_time' => $cpuMictime,
        'cookie' => json_encode($_COOKIE),
        'get' => json_encode($_GET),
        'post' => json_encode($_POST),
        'server' => json_encode($_SERVER),
        'profile' => gzcompress(json_encode($profile), 2),
        'ctime' => time(),
        'app' => $configure['app_name'],
    ];

    // 引入 nimble 组件库
    require_once __DIR__ . '/../nimble/src/Nimble/nimble.php';
    $mysqlConfig = $configure['connection']['connection'];

    try {
        $client = new Nimble\Mysql\Client($mysqlConfig);

        $model = $client->table('request', $mysqlConfig['tb_prefix']);
        $model->data($insRequest)->save();

    } catch (Exception $e) {
        error_log('phpAnalysis - ' . $e->getMessage());
    }

}, $configure, $disableFn);

