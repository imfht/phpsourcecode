<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2019/3/3
 * Time: 12:13
 */
namespace app\lib;

use think\swoole\template\Timer;

class chatTimer extends Timer {
    protected $server = null;
    protected $fd = null;
    protected $data = [];

    /**
     * chatTimer constructor.
     * @param $server
     * @param $fd
     * @param $data
     */
    public function __construct($server,$fd,$data)
    {
        $this->server = $server;
        $this->fd = $fd;
        $this->data = $data;
    }

    public function initialize($args){

    }

    /**
     *
     */
    public function run()
    {
        $this->server->push($this->fd,$this->__json($this->data));
    }

    /**
     * @param array $data
     * @param string $type
     * @return false|string
     */
    protected function __json($data=[],$type='ticker'){
        return json_encode([
            'emit'=>$type,
            'data'=>$data
        ]);
    }
}