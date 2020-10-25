<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午3:33
 */

namespace Component\Producer;


use Component\Controller\Controller;
use Kernel\Server;

class TaskProducer implements IProducer
{
    protected $producer = [];
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function addProducer($controller, string $method, array $args = []): IProducer
    {
        $this->producer = [
            'obj' => $controller,
            'method' => $method,
            'args' => $args
        ];
        return $this;
    }


    public function getProcessId(): int
    {
        return 0;
    }

    public function run()
    {
//               if($flag = $this->server->getServer()->task("test",-1,function (\swoole_server $serv, $task_id, $data){
//                       var_dump("test");
//                       call_user_func_array([$this->producer['obj'], $this->producer['method']], $this->producer['args']);
//                       return 0;
//               })){
//                       return ['code'=>0];
//               }
        $flag = $this->server->getServer()->task("taskcallback", -1, function (\swoole_server $serv, $task_id, $data) {
            call_user_func_array([$this->producer['obj'], $this->producer['method']], $this->producer['args']);
        });

        if ($flag !== false) {
            return ['taskId' => $flag];
        }
        return ['code' => 1];
    }


    public function addBefore(\Closure $closure): IProducer
    {
        return $this;
    }

    public function addAfter(\Closure $closure): IProducer
    {
        // TODO: Implement addAfter() method.
        return $this;
    }

}