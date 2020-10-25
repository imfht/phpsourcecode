<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:31
 */

namespace fastwork\swoole;


use fastwork\Container;
use fastwork\Db;
use fastwork\db\MysqlPool;
use fastwork\facades\Log;
use fastwork\Fastwork;
use think\Template;

class Server
{
    protected $conf = [];

    /**
     * @var \swoole_websocket_server
     */
    protected $server = null;

    /**
     * 是否是task线程
     * @var bool
     */
    public $is_task = false;
    /**
     * 文件最后修改时间
     * @var
     */
    protected $lastMtime;
    /**
     * 容器
     * @var Fastwork
     */
    protected $app;


    public function __construct($server, array $conf)
    {
        $this->server = $server;
        $this->conf = $conf;
    }

    public function onStart(\swoole_server $server)
    {
        $config = $this->conf;
        echo "swoole is start {$config['ip']}:{$config['port']}" . PHP_EOL;
    }

    public function onShutdown(\swoole_server $server)
    {
        echo 'swoole on Shutdown' . PHP_EOL;
    }

    /**
     * @param \swoole_server $server
     * @param $worker_id
     * @throws \ReflectionException
     */
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        /* 初始化配置 */
        $this->app = Container::get('fastwork');
        $this->app->init();

        $this->lastMtime = time();
        $this->is_task = $server->taskworker ? true : false;
        $this->app->initialize();
        $this->app->swoole = $server;

        $this->app->routeInit();

        /**
         * 模板引擎
         */
        $tempConfig = $this->app->config->get('template');
        $this->app->view = new Template($tempConfig);
        /**
         * 定时监控
         */
        if (0 == $worker_id) {
            $this->monitor($server);
        }
        $this->app->log->clearTimer($server);
        /**
         * 开启redis连接池
         */
        $this->app->redis->clearTimer($server);
        /**
         * 开启数据库
         */
        $db = Db::start();
        $db->clearTimer($server);
        //放入容器中
        $this->app->db = $db;
    }

    /**
     * work进程停止
     * @param \swoole_server $server
     * @param $worker_id
     */
    public function onWorkerStop(\swoole_server $server, $worker_id)
    {
    }

    public function onWorkerExit(\swoole_server $server, $worker_id)
    {
    }

    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_code, $signal)
    {
        echo 'swoole Worker on Error' . PHP_EOL;
        Log::record('SWOOLE', "进程异常 WorkerID:{$worker_id} WorkerPID:{$worker_pid}  ExitCode:{$exit_code}");
    }

    /**
     * 管道信息
     * @param \swoole_server $server
     * @param $src_worker_id
     * @param $message
     */
    public function onPipeMessage(\swoole_server $server, $src_worker_id, $message)
    {

    }

    public function onManagerStart(\swoole_server $server)
    {
        echo 'swoole Manager on start' . PHP_EOL;
    }

    public function onManagerStop(\swoole_server $server)
    {
        echo 'swoole Manager on stop' . PHP_EOL;
    }

    /**
     * onTask任务投递
     * @param $server
     * @param $task_id
     * @param $workder_id
     * @param $data
     */
    public function onTask($server, $task_id, $workder_id, $data)
    {
        var_dump("onTask \n");
        var_dump("task_id:{$task_id}, workder_id:{$workder_id} \n");
        var_dump($data);
    }

    /**
     * Task任务投递结束
     * @param $server
     * @param $task_id
     * @param $data
     */
    public function onFinish($server, $task_id, $data)
    {
        var_dump('onTaskFinish:' . $task_id);
    }

    /**
     * 文件监控
     *
     * @param $server
     */
    protected function monitor(\swoole_server $server)
    {
        $monitor = $this->conf['monitor'];
        $paths = $monitor['path'];
        $timer = $monitor['timer'] ?: 2;

        $server->tick($timer, function () use ($paths, $server) {
            foreach ($paths as $path) {
                $dir = new \RecursiveDirectoryIterator($path);
                $iterator = new \RecursiveIteratorIterator($dir);

                foreach ($iterator as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) != 'php') {
                        continue;
                    }

                    if ($this->lastMtime < $file->getMTime()) {
                        $this->lastMtime = $file->getMTime();
                        echo '[update]' . $file . " reload...\n";
                        $server->reload();
                        return;
                    }
                }
            }
        });
    }
}