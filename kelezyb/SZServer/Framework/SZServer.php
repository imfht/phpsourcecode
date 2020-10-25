<?php
namespace Framework;

/**
 * Socket服务器
 *
 * @author kelezyb
 * @version 0.9.0.1
 */
class SZServer {
    /**
     * 正常返回状态码
     */
    const SUCCESS_CODE = 1;

    /**
     * @var SZServer
     */
    private static $instance = null;

    /**
     * 获得服务器静态实例(获取之前第一次必须传入相关参数)
     * @param string $host
     * @param int $port
     * @return SZServer
     */
    public static function Instance($host = '', $port = 0) {
        if (null == SZServer::$instance) {
            SZServer::$instance = new SZServer($host, $port);
        }

        return SZServer::$instance;
    }

    const HEARTBEAT_TIME = 1000;

    /**
     * @var \swoole_server
     */
    private $serv;

    /**
     * @var \swoole_table
     */
    private $user_tables;

    /**
     * @var SZDispatcher
     */
    private $dispatcher;

    /**
     * 服务构造函数
     * @param string $host
     * @param int $port
     */
    private function __construct($host, $port) {
        $this->set_process_title('szserver-master');
        $this->dispatcher = new SZDispatcher();

        $this->user_tables = new \swoole_table(20000);
        $this->user_tables->column('fd', \swoole_table::TYPE_INT, 8);       //1,2,4,8
        $this->user_tables->create();

        $this->serv = new \swoole_server($host, $port);
        $server_config = SZConfig::Instance()->get('server');
        $this->serv->set($server_config);

        $this->_init();
        SZLogger::info("SZServer listen in $port.");
    }

    /**
     * 服务运行
     */
    public function run() {
        $this->serv->start();
    }

    /**
     * 连接回调函数
     * @param \swoole_server $serv
     * @param int $fd
     */
    public function _connectHandler($serv, $fd) {
        SZLogger::debug("Client:[$fd] connect.");
    }

    /**
     * 工作进程开启回调函数
     * @param \swoole_server $serv
     */
    public function _workerStart($serv) {
        $serv->addtimer(SZServer::HEARTBEAT_TIME);
        $this->set_process_title('szserver-worker');
    }

    /**
     * 管理进程开启回调函数
     * @param \swoole_server $serv
     */
    public function _managerStart($serv) {
        $this->set_process_title('szserver-manager');
    }

    /**
     * 数据接收回调函数
     * @param \swoole_server $serv
     * @param int $fd
     * @param int $from_id
     * @param string $buffer
     */
    public function _receiveHandler($serv, $fd, $from_id, $buffer) {
        try {
            $data = $this->_parse($buffer);

            $datas = $this->dispatcher->executeController($fd, $data);
            $result = array(SZServer::SUCCESS_CODE, $datas);
        } catch (\Exception $ex) {
            SZLogger::error($ex->getMessage());
            $result = array($ex->getCode(), $ex->getMessage());
        }

        $outBuffer = $this->_build($result);
        $serv->send($fd, $outBuffer);
    }

    /**
     * 连接关闭回调函数
     * @param \swoole_server $serv
     * @param int $fd
     */
    public function _closeHandler($serv, $fd) {
        SZLogger::debug("Client: Close");
    }

    /**
     * 异步任务开启回调函数
     * @param \swoole_server $serv
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return string
     */
    public function _taskHandler($serv, $task_id, $from_id, $data) {
        $tasks = unserialize($data);
        SZLogger::debug("Execute AsyncTask [{$task_id}]");

        return $this->dispatcher->executeTask($task_id, $tasks[0], $tasks[1]);;
    }

    /**
     * 消息转发
     * @param string $uid
     * @param mixed $data
     */
    public function forward($uid, $data) {
        $user = $this->user_tables->get($uid);
        $fd = $user['fd'];

        $result = array(SZServer::SUCCESS_CODE, $data);
        $outBuffer = $this->_build($result);

        $this->serv->send($fd, $outBuffer);
    }

    public function login($uid, $fd) {
        SZLogger::info(sprintf("User [%s:%s] login.", $uid, $fd));

        $this->user_tables->set($uid, array('fd' => $fd));
    }

    public function logout() {

    }

    /**
     * 异步任务完成回调函数
     * @param \swoole_server $serv
     * @param int $task_id
     * @param string $data
     */
    public function _taskFinishHandler($serv, $task_id, $data) {
        SZLogger::debug("Execute AsyncTask [{$task_id}] finish");
        $this->dispatcher->executeTaskFinish($task_id, $data);
    }

    public function _workerError($serv, $worker_id, $worker_pid, $exit_code) {
        SZLogger::error(sprintf("Error[%s] worker id %s error code (%s)\n", $worker_pid, $worker_id, $exit_code));
    }

    /**
     * timer定时器回调
     * @param \swoole_server $serv
     * @param int $interval
     */
    public function _timerHandler($serv, $interval) {
        $ppid = posix_getppid();
        if ($ppid == 1) {
            exit(0);
        }

        switch ($interval) {
            case SZServer::HEARTBEAT_TIME: //心跳检测
                $fds = $serv->heartbeat();
                foreach ($fds as $fd) {
                    $this->_closeFd($fd);
                }

                break;
        }
    }

    /**
     * 管理进程停止
     * @param \swoole_server $serv
     */
    public function _managerStop($serv) {
        echo "Server shutdown is success.";
    }

    public function reload() {
        $this->serv->reload();
    }

    /**
     * 初始化接口绑定
     */
    private function _init() {
        $this->serv->on('WorkerStart', array(&$this, '_workerStart'));
        $this->serv->on('connect', array(&$this, '_connectHandler'));
        $this->serv->on('receive', array(&$this, '_receiveHandler'));
        $this->serv->on('close', array(&$this, '_closeHandler'));
        $this->serv->on('Task', array(&$this, '_taskHandler'));
        $this->serv->on('Finish', array(&$this, '_taskFinishHandler'));
        $this->serv->on('ManagerStart', array(&$this, '_managerStart'));
        $this->serv->on('ManagerStop', array(&$this, '_managerStop'));
        $this->serv->on('Timer', array(&$this, '_timerHandler'));
        $this->serv->on('WorkerError', array(&$this, '_workerError'));
    }

    private function _build($data) {
        $buffer = \msgpack_pack($data);
        $header = pack('N', strlen($buffer));
        return $header . $buffer;
    }

    /**
     * 解析数据包
     * @param string $buffer
     * @return mixed
     * @throws \Exception
     */
    private function _parse($buffer) {
        $pos = 0;
        list(, $len) = unpack('N', substr($buffer, $pos, 4));

        $pos = 4;

        if (strlen($buffer) == 4 + $len) {
            return \msgpack_unpack(substr($buffer, $pos, $len));
        } else {
            throw new \Exception('Message parse error.', 1001);
        }
    }

    /**
     * 开启新任务
     * @param string $taskname
     * @param array $params
     * @return int
     */
    public function newTask($taskname, $params) {
        $task = array(
            $taskname,
            $params
        );
        return $this->serv->task(serialize($task));
    }

    public function finishTask($data) {
        $this->serv->finish(serialize($data));
    }

    /**
     * 关闭fd连接
     * @param int $fd
     */
    private function _closeFd($fd) {
        $this->serv->close($fd);
    }

    private function set_process_title($title) {
        if ('Linux' == PHP_OS) {
            cli_set_process_title($title);
        }
    }
}