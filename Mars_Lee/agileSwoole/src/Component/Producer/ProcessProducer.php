<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午3:33
 */

namespace Component\Producer;


use Component\Controller\Controller;
use Kernel\Core\Exception\ExecException;
use Kernel\Server;
use Swoole\Process;

class ProcessProducer implements IProducer
{
    protected $producer = [];
    protected $server;
    protected $after;
    protected $before;
    protected $processId = 0;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function addBefore(\Closure $closure): IProducer
    {
        $this->before[] = $closure;
        return $this;
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

    public function addAfter(\Closure $closure): IProducer
    {
        $this->after[] = $closure;
        return $this;
    }

    public function run()
    {
        try {
            if (!empty($this->before)) {
                foreach ($this->before as $closure) {
                    call_user_func($closure);
                }
            }
            $process = new Process(function () {
                call_user_func_array([$this->producer['obj'], $this->producer['method']], $this->producer['args']);
                return 0;
            });
            $process->name(get_class($this->producer['obj']) . time());
            $process->start();

            \swoole_process::signal(SIGCHLD, function ($sig) {
                //必须为false，非阻塞模式
                while ($ret = \swoole_process::wait(false)) {
                    echo "PID={$ret['pid']} exists\n";
                }
            });
            $_SERVER['process_id'] = $this->processId = $process->pid;
            if (!empty($this->after)) {
                foreach ($this->after as $closure) {
                    call_user_func($closure);
                }
            }
        } catch (\Exception $exception) {
            throw new ExecException($exception->getMessage());
        }
        return ['processId' => $process->pid];
    }

    public function getProcessId(): int
    {
        return $this->processId;
    }


}