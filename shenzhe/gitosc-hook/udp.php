<?php
function mylog($msg, $filename='pull'){
    error_log(date("Y-m-d H:i:s")."\t".$msg.PHP_EOL, 3, __DIR__.DIRECTORY_SEPARATOR.$filename.'.log');
}
$opt = getopt("d", [
    "ip::",
    "port::",
    "worker::"
]);
$ip = empty($opt['ip']) ? '0.0.0.0' : $opt['ip'];
$port = empty($opt['port']) ? '8991' : $opt['port'];
if (isset($opt['d'])) {
    $daemonize = 1;
} else {
    $daemonize = 0;
}
$serv = new swoole_server($ip, $port, SWOOLE_BASE, SWOOLE_SOCK_UDP);
$serv->set([
    'daemonize'=>$daemonize,
]);
$serv->on('Packet', function($server, $_data, $client) {
    $data = json_decode(trim($_data), true);
    if(!empty($data[2])) {
        system('cd '.$data[2].' && git pull', $ret);
        mylog($data[2].':'.$ret);
        if('gitosc-hook' == $data[0]) { //自动重载配置
            file_get_contents('http://127.0.0.1:9501/reload');
        }
    } else {
        mylog($_data.':'.strlen($_data));
    }
});
$serv->start();