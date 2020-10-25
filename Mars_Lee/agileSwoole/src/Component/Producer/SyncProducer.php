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

class SyncProducer implements IProducer
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
                        'obj'           =>      $controller,
                        'method'        =>      $method,
                        'args'          =>      $args
                ];
                return $this;
        }


        public function run()
        {
               $response = call_user_func_array([$this->producer['obj'], $this->producer['method']], $this->producer['args']);
               return $response;
        }

        public function addBefore(\Closure $closure):IProducer
        {
                return $this;
        }

        public function addAfter(\Closure $closure):IProducer
        {
                return $this;
        }

        public function getProcessId(): int
        {
                return 0;
        }


}