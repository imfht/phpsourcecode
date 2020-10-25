<?php
namespace workerbase\classs\worker;
use Swoole\Process;
use Swoole\Timer;
use workerbase\classs\Config;
use workerbase\classs\datalevels\Redis;
use workerbase\classs\Log;
use workerbase\classs\MQ\imps\MessageServer;

/**
 * Worker server, 主要用于管理和维护worker进程
 */
class WorkerServer
{
    /**
     * 当前实例
     * @var WorkerServer
     */
    private static $_instance = null;

    /**
     * 整个worker服务的配置
     */
    private $_conf;

    /**
     * 正在运行的workers
     * 格式:
     *    'Worker type' => [pid1 => true, pid2 => true, pid3 => true]
     * @var array
     */
    private $_runningWorkers = [];

    /**
     * pid to worker type
     * 格式:
     *  pid => worker type
     * @var array
     */
    private $_pidMapToWorkerType = [];

    /**
     * 监控worker的Timer ID
     */
    private $_monitorTimerId;

    /**
     * 子进程都退出后，主进程是否退出
     * @var bool
     */
    private $_masterProcessExit = false;

    /**
     * 系统可用资源
     * @var null
     */
    private $_systemResource = null;

    //配置的总进程数
    private $_workerConfigThreadNum = 0;

    //队列是否已经初始化过
    private $_hasInit = false;

    /**
     * 上次重新分配队列进程资源的时间戳
     * @var bool
     */
    private $_reallocateTimestamp = null;

    /**
     * 每个队列动态配置的进程数量
     * 格式:
     *    'jobName' => '进程数量'
     * @var bool
     */
    private $_jobNameToThreadNum = [];

    /**
     * 队长进程（值守进程）
     * 格式:
     *    'jobName' => 'pid'
     * @var bool
     */
    private $_jobNameToCaptainPid = [];

    private function __construct()
    {
        $this->_log("start worker server...");
        $this->_conf = Config::read("", "worker");

        //关闭协程，采用异步进程(必须放在服务初始化最前面)
        if (version_compare(swoole_version(), '4.0.1', '>=')) {
            \swoole_async_set([
                'enable_coroutine' => false
            ]);
        }

        //masker进程注册相关信号处理
        Process::signal(SIGCHLD, [$this, 'doSignal']);
        Process::signal(SIGTERM, [$this, 'doSignal']);

        //根据 -d 参数确认是否后台运行
        $options = getopt('d');
        if (isset($options['d'])) {
            Process::daemon();
            file_put_contents($this->_conf['pid'], posix_getpid());
        }

        //获取系统当前剩余资源
        $options = getopt('m:');
        if (isset($options['m']) && !empty($options['m'])) {
            list($memory,$ableThreadNum) = explode('_', $options['m']);
            $this->_systemResource = [
                'memory' => $memory,
                'resourceThreadNum' => $ableThreadNum
            ];
        }

        //初始化worker队列
        $this->_initWorkerMessageQueue();
    }

    /**
     * 获取worker服务
     * @return WorkerServer
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new WorkerServer();
        }
        return self::$_instance;
    }

    /**
     * 启动worker server
     */
    public function run()
    {
        ini_set('memory_limit', -1);
        //清除队列实例，让子进程创建自己的队列实例
        MessageServer::clearInstance();

        if (isset($this->_conf['workerConf'])) {
            $queueNum = count($this->_conf['workerConf']);

            //计算最少需要的内存资源
            $needMemory = $queueNum*32;
            if (in_array(WK_ENV, ['dev', 'test'])) {
                $needMemory = $queueNum;
            }
            if (!is_null($this->_systemResource) && ($needMemory > $this->_systemResource['memory'])) {
                $this->_log('error: The system has no memory resources available, need memory:' .
                    $needMemory . 'M' . ',remaining memory:' . $this->_systemResource['memory'] . 'M');
                Log::error('error: The system has no memory resources available, need memory:' .
                    $needMemory . 'M' . ',remaining memory:' . $this->_systemResource['memory'] . 'M');
                $this->_killMaster();
            }

            //配置的总进程数
            if ($queueNum > 0) {
                foreach ($this->_conf['workerConf'] as $conf) {
                    $this->_workerConfigThreadNum += $conf['threadNum'];
                }
            }
        }

        $this->startWorker();

        //监控worker进程 (5分钟后触发回调函数)
        Timer::after(5*60*1000, function () {
            //每秒执行一次worker
            $this->_monitorTimerId = Timer::tick(1000, function () {
                $this->startWorker();
            });
        });
    }

    /**
     * 启动worker, 允许重复执行
     */
    public function startWorker()
    {
        $workersConf = $this->_conf['workerConf'];
        if (empty($workersConf)) {
            $this->_killMaster();
        }

        //10秒，进行一次队列进程资源调配
        $isReallocation = false;
        if ($this->_hasInit && (time() - $this->_reallocateTimestamp) > 10) {
            $isReallocation = true;
        }

        $masterPid = posix_getpid();
        foreach ($workersConf as $jobName => $conf) {
            if (!isset($conf['threadNum']) || !isset($conf['lifeTime']) || !isset($conf['maxHandleNum'])) {
                $this->_log("worker config error. jobName={$jobName}");
                continue;
            }

            //该队列目前有多少个worker进程在执行
            $workers = $this->_getWorkers($jobName);

            $isKeepThreadNum = true;//是否保持伸缩记录的进程数
            //10秒，进行一次队列进程资源调配，发现消息积压的队列，优先配置进程
            if ($isReallocation) {
                //总的可用进程资源
                $countThreadNum = $this->_workerConfigThreadNum;
                if (isset($this->_systemResource['resourceThreadNum'])) {
                    $countThreadNum = $this->_systemResource['resourceThreadNum'];
                }
                //总剩余可用进程数
                $residueThreadNum = max(0, $countThreadNum - $this->_getTotalWorkers());
                if ($residueThreadNum) {
                    //目前积压的消息数量
                    $msgCount = MessageServer::getInstance(null, true)->getQueueSize($jobName);
                    if (false !== $msgCount) {
                        $msgBacklogPoint = isset($conf['msgBacklogPoint'])?$conf['msgBacklogPoint']:$this->_conf['msgbacklogpoint'];
                        //消息积压增量
                        if ($msgCount > $msgBacklogPoint) {
                            //可调配的最大进程数
                            $elasticWorkers = max(0, $conf['threadNum'] - $workers);
                            $elasticWorkers = min($residueThreadNum, $elasticWorkers);
                            if ($elasticWorkers) {
                                //先分配一条进程，逐步扩容
                                $this->_jobNameToThreadNum[$jobName] = $workers + 1;
                            }
                            Log::info("jobName={$jobName}, has_msg_num:" .$msgCount.',worker_num:'.$workers
                                .',new_worker_num:'.$this->_jobNameToThreadNum[$jobName]
                                .',all_thread_num:'.$countThreadNum.',residue_thread_num:'.$residueThreadNum);
                        }
                    }
                }
            }
            elseif (!$this->_hasInit) {
                //每个队列按比例开进程
                if (isset($this->_systemResource['resourceThreadNum'])) {
                    $threadNum = max(1, floor(($conf['threadNum']/$this->_workerConfigThreadNum)*$this->_systemResource['resourceThreadNum']));
                    $conf['threadNum'] = min($threadNum, $conf['threadNum']);
                }

                $this->_jobNameToThreadNum[$jobName] = $conf['threadNum'];
                $isKeepThreadNum = false;
            }

            if ($isKeepThreadNum) {
                $conf['threadNum'] = $this->_jobNameToThreadNum[$jobName];
            }

            //控制测试环境的进程数
            if (in_array(WK_ENV, ['dev', 'test'])) {
                //默认启动一个进程用于测试
                $conf['threadNum'] = 1;
            }

            if ($workers >= $conf['threadNum']) {
                continue;
            }

            //启动设置的多进程处理worker任务
            $hasWorkers = $conf['threadNum'] - $workers;
            //启动worker
            for ($i=0; $i < $hasWorkers; $i++) {
                //是否是队长进程
                $isFirst = false;
                if (!isset($this->_jobNameToCaptainPid[$jobName])) {
                    //队长进程值守
                    $isFirst = true;
                }
                $workerProcess = new Process(function (Process $worker) use ($jobName, $isFirst, $masterPid) {
                    if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                        $this->_log("start worker, jobName={$jobName}, pid={$worker->pid}");
                    }
                    //直接执行，处理队列
                    Worker::getInstance($jobName, $isFirst, $masterPid)->run();
                }, false, 0);

                $pid = $workerProcess->start();
                if ($pid === false) {
                    $this->_log("start worker failure. jobName={$jobName}");
                    continue;
                }
                //注册worker
                $this->_addWorker($jobName, $pid);
            }
        }

        //标记队列服务已完成初始化
        if (!$this->_hasInit) {
            $this->_hasInit = true;
            $this->_reallocateTimestamp = time();
        } elseif ($isReallocation) {
            $this->_reallocateTimestamp = time();
        }
    }

    /**
     * 处理进程信号
     * @param int $sig  - 信号类型
     */
    public function doSignal($sig) {
        switch ($sig) {
            case SIGCHLD:
                //子进程退出时，回收子进程资源
                //必须为false，非阻塞模式
                while($ret =  Process::wait(false)) {
                    $pid = $ret['pid'];
                    if ($this->_delWorkerByPid($pid) && in_array(WK_ENV, ['dev', 'local_debug'])) {
                        $this->_log("回收进程资源, pid={$ret['pid']}");
                    }
                }

                if ($this->_masterProcessExit && $this->_getTotalWorkers() == 0) {
                    $this->_log("worker server shutdown...");
                    //当子进程都退出后，结束masker进程
                    @unlink($this->_conf['pid']);
                    exit(0);
                }
                break;
            case SIGTERM:
                $this->_log("recv terminate signal, exit worker.");
                //主进程退出处理
                //关闭监控
                if ($this->_monitorTimerId) {
                    Timer::clear($this->_monitorTimerId);
                    $this->_monitorTimerId = null;
                }
                //主进程退出信号标记（子进程都退出，则主进程退出）
                $this->_masterProcessExit = true;
                if (!empty($this->_pidMapToWorkerType)) {
                    foreach (array_keys($this->_pidMapToWorkerType) as $pid) {
                        //检查子进程心跳
                        if (!Process::kill($pid, SIG_DFL)) {
                            if ($this->_delWorkerByPid($pid)) {
                                $this->_log("回收进程资源2, pid={$pid}");
                            }
                        }
                        Process::kill($pid, SIGTERM);
                    }
                } elseif ($this->_getTotalWorkers() == 0) {
                    $this->_log("worker server shutdown...");
                    //当子进程都退出后，结束masker进程
                    @unlink($this->_conf['pid']);
                    exit(0);
                }
                break;
        }
    }

    /**
     * 初始化消息队列(redis队列用不到，为其他队列驱动预留接口)
     */
    private function _initWorkerMessageQueue()
    {
        if (empty($this->_conf)|| $this->_conf['driver'] == 'redis') {
            return;
        }

        $messageServer = MessageServer::getInstance($this->_conf['driver']);
        foreach ($this->_conf['workerConf'] as $jobName => $workerConfig) {
            //获取根据环境拼接后的队列名称
            $queueName = $messageServer->getQueueNameByJobName($jobName);
            if (empty($queueName)) {
                $this->_log("creare worker message queue failure, get queue name failure. queueName={$queueName}");
                continue;
            }

            //创建队列（不存在则创建，存在则返回true）
            $ret = $messageServer->createQueue($queueName);
            if (!$ret) {
                $this->_log("creare worker message queue failure. queueName={$queueName}");
            }

            if (isset($workerConfig['option'])) {
                //设置队列属性
                $messageServer->setQueueAttributes($queueName, $workerConfig['option']);
            }
        }
    }


    /**
     * 添加worker
     * @param string $jobName
     * @param  int $pid - 进程id
     */
    private function _addWorker($jobName, $pid)
    {
        if (!isset($this->_runningWorkers[$jobName])) {
            $this->_runningWorkers[$jobName] = [];
        }
        $this->_runningWorkers[$jobName][$pid] = true;
        $this->_pidMapToWorkerType[$pid] = $jobName;

        if (!isset($this->_jobNameToCaptainPid[$jobName])) {
            $this->_jobNameToCaptainPid[$jobName] = $pid;
        }
    }

    /**
     * 根据jobName返回指定worker目前正在运行的worker数量
     * @param string $jobName
     * @return int
     */
    private function _getWorkers($jobName)
    {
        if (!isset($this->_runningWorkers[$jobName])) {
            return 0;
        }
        return count($this->_runningWorkers[$jobName]);
    }

    /**
     * 删除worker
     * @param int $pid      - 进程id
     * @return bool
     */
    private function _delWorkerByPid($pid) {
        if (!isset($this->_pidMapToWorkerType[$pid])) {
            return false;
        }
        $workerType = $this->_pidMapToWorkerType[$pid];
        unset($this->_pidMapToWorkerType[$pid]);
        if (isset($this->_runningWorkers[$workerType]) && isset($this->_runningWorkers[$workerType][$pid])) {
            unset($this->_runningWorkers[$workerType][$pid]);
        }

        if (isset($this->_jobNameToCaptainPid[$workerType])) {
            unset($this->_jobNameToCaptainPid[$workerType]);
        }
        //减掉队列出让的进程
        if (Redis::getInstance([], true)->rPop('worker-exit-flag:' . $workerType)) {
            $this->_jobNameToThreadNum[$workerType] = max($this->_jobNameToThreadNum[$workerType] - 1, 1);
        }
        return true;
    }

    /**
     * 返回workers总数
     * @return int
     */
    private function _getTotalWorkers()
    {
        if (empty($this->_runningWorkers)) {
            return 0;
        }
        $total = 0;
        foreach (array_keys($this->_runningWorkers) as $workerType) {
            $total += count($this->_runningWorkers[$workerType]);
        }
        return $total;
    }

    private function _killMaster()
    {
        Process::kill(posix_getpid(), SIGTERM);
        //主进程退出信号标记（子进程都退出，则主进程退出）
        $this->_masterProcessExit = true;
        if (!empty($this->_pidMapToWorkerType)) {
            foreach (array_keys($this->_pidMapToWorkerType) as $pid) {
                Process::kill($pid, SIGTERM);
            }
        }
        if ($this->_getTotalWorkers() == 0) {
            $this->_log("worker server shutdown...");
            //当子进程都退出后，结束masker进程
            @unlink($this->_conf['pid']);
            exit(0);
        }
    }

    /**
     * 输出日志
     * @param $msg
     */
    private function _log($msg)
    {
        $dateStr = date("Y-m-d H:i:s");
        $pid = posix_getpid();
        echo "[{$dateStr}] [pid={$pid}] {$msg}\n";
    }
}