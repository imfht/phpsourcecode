<?php
function mylog($msg, $filename='hook_error'){
    error_log(date("Y-m-d H:i:s")."\t".$msg.PHP_EOL, 3, __DIR__.DIRECTORY_SEPARATOR.$filename.'.mylog');
}
define('PASSWORD', '');
$config = null;
$opt = getopt("d", [
    "ip::",
    "port::",
    "worker::"
]);
$ip = empty($opt['ip']) ? '0.0.0.0' : $opt['ip'];
$port = empty($opt['port']) ? '9501' : $opt['port'];
if (isset($opt['d'])) {
    $daemonize = 1;
} else {
    $daemonize = 0;
}
$workerNum = empty($opt['worker']) ? 4 : intval($opt['worker']);
$http = new swoole_http_server($ip, $port);
$http->set([
    'worker_num'=>$workerNum,
    'daemonize'=>$daemonize,
]);
$http->on('request', function ($request, $response) {
    $response->end('ok');
    if(false !== strpos($request->server['path_info'], 'reload')) {
        global $http;
        $http->reload();
        return;
    }
    if(empty($request->post['hook'])) {
        mylog('hook data empty');
        return;
    }
    $data = json_decode($request->post['hook'], true);
    if(empty($data)) {
        //错误的数据
        mylog('hook no json data');
        return;
    }
    if(PASSWORD && $data['password'] != PASSWORD) {
        //密码不匹配
        mylog('password error');
        return ;
    }

    if(empty($data['push_data']['commits'])) {
        //没有提交者
        mylog('no commit');
        return ;
    }
    //项目名称
    $project = $data['push_data']['repository']['name'];
    //分支名称
    $ref = $data['push_data']['ref'];

    mylog("{$project}: {$ref} push");

    global $config;
    if(empty($config[$project][$ref])) {
        mylog("no {$project} {$ref}");
        return ;
    }
    $client = new \swoole_client(SWOOLE_SOCK_UDP);
    foreach($config[$project][$ref] as $item) {
        $client->connect($item['host'], $item['port']);
        $client->send(json_encode([$project, $ref, $item['path']]));
    }
});
$http->on('workerStart', function($serv, $workerId) {
    global $config;
    if(function_exists('opcache_reset')) {
        opcache_reset();
    }
    $config = include(__DIR__.DIRECTORY_SEPARATOR.'config.php');
});

$http->start();

