<?php
class PortChecker {
    public static $status;
    public function __construct()
    {
        return true;
    }
    public function check($ip, $port){
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_nonblock($sock);
        socket_connect($sock,$ip, $port);
        socket_set_block($sock);
        self::$status = @socket_select($r = array($sock), $w = array($sock), $f = array($sock), 5);
        return self::$status;
    }
    public function status(){
        return self::$status;
    }
}
