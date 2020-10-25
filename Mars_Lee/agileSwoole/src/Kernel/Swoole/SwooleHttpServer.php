<?php


namespace Kernel\Swoole;


use Kernel\Core\Conf\Config;
use Kernel\Server;
use Kernel\Swoole\Event\Event;

class SwooleHttpServer implements Server
{
    const EVENT = [
        'request', 'task', 'finish', 'workerStart'
    ];
    protected $server;
    protected $event = [

    ];
    protected $config;
    protected static $application = null;
    public static $appType = 'normal';

    public function __construct(Config $config)
    {
        $this->config = $config;

        try {
            $serverConfig = $config->get('server');
        } catch (\Exception $exception) {
            $serverConfig = [
                'host' => '0.0.0.0',
                'port' => 9550,
                'mode' => SWOOLE_PROCESS,
                'type' => SWOOLE_SOCK_TCP
            ];
        }
        $this->server = new \Swoole\Http\Server($serverConfig['host'], $serverConfig['port'], $serverConfig['mode'], $serverConfig['type']);
        foreach (self::EVENT as $event) {
            $class = '\\Kernel\\Swoole\\Event\\Http\\' . ucfirst($event);
            /* @var \Kernel\Swoole\Event\Event $callback */
            $callback = new $class($this->server);
            $this->event[$event] = $callback;
            $this->server->on($event, [$callback, 'doEvent']);
        }
        try {
            $swooleOption = $config->get('swoole');
        } catch (\Exception $exception) {
            $swooleOption = [
                'worker_num' => 4,    //开启两个worker进程
                'task_worker_num' => '3',
                'max_request' => 5000,   //每个worker进程max request设置为3次
                'dispatch_mode' => 3,
                'open_eof_check' => true, //打开EOF检测
                'package_eof' => PHP_EOL, //设置EOF
                'open_cpu_affinity' => true,
                'user' => 'www',   //设置运行用户
                'group' => 'www',
                'buffer_output_size' => 32 * 1024 * 1024, //必须为数字  输出缓存
                'socket_buffer_size' => 128 * 1024 * 1024, //必须为数字 内存缓存
            ];
        }
        $this->server->set($swooleOption);
    }

    public function start(): Server
    {
        $this->server->start();
        return $this;
    }

    public function shutdown(\Closure $callback = null): Server
    {
        if (!is_null($callback)) {
            $callback();
        }
        return $this;
    }

    public function close($fd, $fromId = 0): Server
    {
        $this->server->close($fd, $fromId = 0);
        return $this;
    }

    public function getServer(): \swoole_server
    {
        return $this->server;
    }

    public function setTask(string $event, \Closure $closure): Server
    {
        if (!isset($this->event[$event]) or !($this->event[$event] instanceof Event)) {
            throw new \LogicException('设置任务失败');
        }
        $this->event[$event]->setEventCall($closure);
        return $this;
    }

    public function createTable(string $table)
    {
        $config = $this->config->get($table);
        $tableName = $table . 'Table';
        $table = new \swoole_table($config['max_process'] ?? 10);

        if (!isset($config['rule'])) {
            return false;
        }

        foreach ($config['rule'] as $key => $type) {
            $table->column($key, $type);
        }
        $table->create();
        $this->server->$tableName = $table;

        return true;
    }

    public static function setAppType(string $appType)
    {
        self::$appType = $appType;
    }

    public static function getAppType()
    {
        return self::$appType;
    }

    public static function setApplication($application)
    {
        self::$application = $application;
    }

    /**
     * @return \Yaf_Application
     */
    public static function getApplication()
    {
        return self::$application;
    }

}