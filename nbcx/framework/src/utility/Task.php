<?php
/**
 *
 * User: Collin
 * QQ: 1169986
 * Date: 17/12/8 上午10:44
 */
namespace nb\utility;

use nb\Config;
use nb\Server;

class Task {

    /**
     * @var \swoole\Server
     */
    private $server;

    private $space = 'task\\';

    private $action;

    private $param=null;

    public function __construct() {
        if(Server::$o) {
            $this->server = Server::$o->server;
        }
    }

    public function space($space) {
        $this->space = $space;
    }

    public function action($class,$function='index') {
        $this->action = [$this->space.$class,$function];
        return $this;
    }

    public function args($args) {
        $this->param=func_get_args();
        return $this;
    }

    public function exec($worker_id=null, $finish_callback=null) {
        $do = [$this->action,$this->param];
        if($this->server) {
            return $this->server->task(
                $do,
                $worker_id,
                $finish_callback
            );
        }
        $class = $this->action[0];
        $class = new $class();
        $func = $this->action[1];
        $do = [$class,$func];
        return call_user_func_array($do,$this->param);
    }

}