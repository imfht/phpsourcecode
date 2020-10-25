<?php
//异步任务，类似于任务投递
//初始化文件
define('DEBUG', 'on');
define('DS', DIRECTORY_SEPARATOR);
define('WEBPATH', __DIR__ . DS . '..');
//设置APP目录
define('APPSPATH', WEBPATH .'/task');
//设定为swoole服务
define('SWOOLE_SERVER', true);

//使用composer扩展
require_once WEBPATH . '/vendor/autoload.php';
//载入swoole frameworkZ框架配置
require_once WEBPATH . '/vendor/matyhtf/swoole_framework/libs/lib_config.php';

//Swoole::$php->setAppPath(WEBPATH . '/queue/');
//设置调试模式
Swoole\Config::$debug = true;

class QueueServer extends \Swoole\Protocol\Base
{
    protected $queueName;
    public function __construct()
    {

    }

    /**
     * 检查队列数据
     * @param $queueData
     * @return bool
     */
    protected function checkQueueData($queueData)
    {
        if (!$queueData['queueName'])
        {
            throw new Exception('请提供队列名称', 1001);
        }
        if (strpos($queueData['queueName'], '/' ) <= 0){
            throw new Exception('队列格式错误', 1002);
        }
        return true;
    }

    /**
     * 路由
     * @return array
     */
    public function router()
    {
        if (isset($this->queueName)){
            $urlParam = explode('/', $this->queueName);
            return ['controller' => $urlParam[0], 'view' => $urlParam[1]];
        }else{
            return ['controller' => 'Home', 'view'=>'index'];
        }
    }
    /**
     * 接收数据
     * @param $server
     * @param $client_id
     * @param $from_id
     * @param $data
     */
    public function onReceive($server, $client_id, $from_id, $data)
    {
        $receiveData = json_decode($data, true);
        try {
            $this->checkQueueData($receiveData);
            $this->server->task($receiveData);
            //$this->server->finish(['code' => 1000, 'msg'=>'操作成功']);
        } catch (\Exception $e) {
            echo 'code:'.$e->getCode().',msg:'.$e->getMessage().PHP_EOL;
            //$this->server->finish(['code'=>$e->getCode(),'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 执行任务
     * @param $server
     * @param $taskId
     * @param $fromId
     * @param $data
     */
    public function onTask($server, $taskId, $fromId, $data)
    {
        try {
            $this->checkQueueData($data);
            $this->queueName = $data['queueName'];
            Swoole::$php->router([$this, 'router']);
            Swoole::$php->request = $data['recData'];
            $rs = Swoole::$php->runMVC();
            $this->server->finish($rs);
        } catch (Exception $e) {
            $this->log($e->getCode().':'.$e->getMessage());
        }
    }

    /**
     * 完成处理
     * @param $server
     * @param $taskId
     * @param $data
     */
    public function onFinish($server, $taskId, $data)
    {
        echo "AsyncTask[$taskId] Finish,执行结果是：" . $data . PHP_EOL;
    }
}
//设置PID文件的存储路径
Swoole\Network\Server::setPidFile(WEBPATH . '/server/pid/queueServer.pid');
Swoole\Error::$echo_html = false;
/**
 * 显示Usage界面
 * php queueServer.php start|stop|reload
 */
Swoole\Network\Server::start(function ($options) {
    $AppSvr = new QueueServer();
    $AppSvr->setLogger(new \Swoole\Log\EchoLog(true));
    $server = Swoole\Network\Server::autoCreate('0.0.0.0', 9443);
    $server->setProtocol($AppSvr);
    $server->run([
        'worker_num' => 100,
        'max_request' => 1,
        'ipc_mode' => 2,
        'task_worker_num' => 100,
    ]);
});