<?php
declare(ticks=1);//每执行一次低级语句会检查一次该进程是否有未处理过的信号（用于调用信号处理器）
namespace workerbase\classs\worker;
use workerbase\classs\App;
use workerbase\classs\Config;
use workerbase\classs\datalevels\Redis;
use workerbase\classs\Log;
use workerbase\classs\MQ\imps\MessageServer;
use workerbase\classs\ServiceFactory;
use Swoole\Coroutine as Co;
/**
 * Worker 工作进程, 主要用于执行异步任务(依赖于pcntl和swoole扩展)
 */
class Worker
{
    /**
     * 当前实例
     * @var Worker
     */
    private static $_instance = null;


    /**
     * worker任务配置
     */
    private $_conf;

    /**
     * 当前 worker队列名
     * @var string
     */
    private $_workerQueueName = '';

    /**
     * 初始化jobName
     * @var string
     */
    private $_jobName = '';

    /**
     * 当前worker配置
     */
    private $_workerConf;

    /**
     * 是否结束worker
     * @var bool
     */
    private $_flgWorkerExit = false;

    /**
     * 进程是否值守
     * @var bool
     */
    private $_onDuty = true;

    /**
     * 没有拉取到消息的时间戳
     * @var bool
     */
    private $_noReceiveMsgTimestamp = null;

    /**
     * 主进程id
     * @var bool
     */
    private $_masterPid = 0;

    /**
     * 上次检查主进程时间戳
     * @var bool
     */
    private $_checkMasterTimestamp = null;

    /**
     * 收到终止信号的时间戳
     * @var bool
     */
    private $_recvTerminateSignalTimestamp = null;

    /**
     * Worker constructor.
     * @param $jobName
     * @param bool $onDuty 进程是否值守
     * @param int $masterPid 主进程id
     * @throws \Exception
     */
    private function __construct($jobName, $onDuty = true, $masterPid = 0)
    {
        $this->_jobName = $jobName;
        $this->_onDuty = $onDuty;
        $this->_masterPid = $masterPid;
        $this->_checkMasterTimestamp = time();

        //获取worker配置
        $this->_conf = Config::read('', "worker");

        $this->_workerQueueName = MessageServer::getInstance($this->_conf['driver'])->getQueueNameByJobName($jobName);
        if (empty($this->_workerQueueName)) {
            Log::error("worker get queue name failure, config invalid. jobName={$jobName}");
            throw new \Exception("worker get queue name failure, config invalid. jobName={$jobName}");
        }

        //注册信号处理
        pcntl_signal(SIGTERM, [$this, 'doSignal']);
        pcntl_signal(SIGQUIT, [$this, 'doSignal']);
    }

    /**
     * 获取worker工作进程服务
     * @param string $jobName
     * @param bool $onDuty 进程是否值守
     * @param int $masterPid 主进程id
     * @return Worker
     * @throws \Exception
     */
    public static function getInstance($jobName, $onDuty = true, $masterPid = 0)
    {
        if (!isset(self::$_instance[$jobName])) {
            self::$_instance[$jobName] = new Worker($jobName, $onDuty, $masterPid);
        }
        return self::$_instance[$jobName];
    }

    /**
     * worker进程入口
     */
    public function run()
    {
        set_time_limit(0);
        ini_set('default_socket_timeout', -1);
        ini_set('memory_limit', -1);

        //设置用户组
        $userName = $this->_conf['user'];
        $userInfo = posix_getpwnam($userName);
        if (empty($userInfo)) {
            Log::error("start worker failure, get userinfo failure. user={$userName}");
            return;
        }
        posix_setuid($userInfo['uid']);
        posix_setgid($userInfo['gid']);
        $pid = posix_getpid();

        //clear log
//        if (is_file($this->_conf['log'])) {
//            file_put_contents($this->_conf['log'], '');
//        }

        //获取该工作队列的配置信息
        $config = $this->_conf['workerConf'][$this->_jobName];
        $this->_workerConf = $config;
        $progName = "workerServer.php task-worker: {$this->_workerQueueName}";

        //修改进程名称。 等同于\Swoole\Process::name($progName);
        \swoole_set_process_name($progName);

        //启动时间
        $startTime = time();
        //当前worker处理任务数
        $currentExcutedTasks = 0;
        $this->_flgWorkerExit = false;
        while (!$this->_flgWorkerExit) {
            Log::setRequireId();
            $s = microtime(true);
            $currentTime = time();

            if (($currentTime - $startTime) > $config['lifeTime']) {
                //超出存活时间，自动退出
                $this->_flgWorkerExit = true;
                Log::info("worker (jobName={$this->_jobName}) run time exceed lifetime,pid:".$pid." exit worker.");
                break;
            }

            //超出最大任务处理次数, 自动退出
            if ($currentExcutedTasks >= $config['maxHandleNum']) {
                $this->_flgWorkerExit = true;
                Log::info("worker (jobName={$this->_jobName}) done tasks exceed maxHandleNum,pid:".$pid." exit worker.");
                break;
            }

            //每10分钟检查主进程是否存活
            if($this->_masterPid && ($currentTime - $this->_checkMasterTimestamp) > 600) {
                //检查主进程心跳
                if (!Process::kill($this->_masterPid, SIG_DFL)) {
                    $this->_flgWorkerExit = true;
                    Log::info("master shutdown,pid:".$pid." exit worker.");
                    break;
                }
                $this->_checkMasterTimestamp = $currentTime;
            }

            //检测是否有新的信号等待dispatching。
            pcntl_signal_dispatch();
            if ($this->_flgWorkerExit) {
                break;
            }

            App::run();
            //处理任务
            $this->_doWorkerTask($this->_workerQueueName);
            App::end(false);

            if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                $this->_log('pid:'.$pid.',use time:'.(microtime(true) - $s));
            }

            $currentExcutedTasks++;
            //检测是否有新的信号等待dispatching。
            pcntl_signal_dispatch();
        }
    }

    /**
     * 处理进程信号
     * @param int $sig  - 信号类型
     */
    public function doSignal($sig) {
        switch ($sig) {
            case SIGTERM:
                $pid = posix_getpid();
                //进程退出处理
                $this->_flgWorkerExit = true;
                Log::info("worker recv terminate signal. pid=" . $pid);
                //收到信号16分钟后没退出，强制退出
                if (!$this->_recvTerminateSignalTimestamp) {
                    $this->_recvTerminateSignalTimestamp = time();
                } elseif ((time() - $this->_checkMasterTimestamp) > 960) {
                    Log::info("worker (jobName={$this->_jobName}) timeout exit,pid:".$pid." force to exit worker.");
                    exit(0);
                }
                break;
        }
    }

    /**
     * 处理worker任务
     * @param string $workerMsgQueueName - 队列名
     * @throws \RedisException
     */
    private function _doWorkerTask($workerMsgQueueName)
    {
        $pid = posix_getpid();
        $response = null;
        try {
            $response = MessageServer::getInstance($this->_conf['driver'])->receive($workerMsgQueueName);
            if ($response === false) {
                //进程不值守
                if (!$this->_onDuty) {
                    $maxFreeTime = isset($this->_workerConf['maxFreeTime'])?$this->_workerConf['maxFreeTime']:$this->_conf['maxfreetime'];
                    if (!$this->_noReceiveMsgTimestamp) {
                        $this->_noReceiveMsgTimestamp = time();
                    } elseif((time() - $this->_noReceiveMsgTimestamp) > $maxFreeTime) {
                        //标记出让的进程
                        Redis::getInstance()->lPush('worker-exit-flag:' . $this->_jobName, 1, 6);
                        //超过5分钟没有获取过消息，退出进程
                        $this->_flgWorkerExit = true;
                        Log::info("worker (jobName={$this->_jobName}) no message was retrieved for more than 5 minutes,pid:".$pid." exit worker.");
                    }
                }
                //没有消息休眠1秒
                sleep(1);
                return;
            }
            if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                Log::info("worker recv message, msg={$response['msgBody']}, pid={$pid}");
            }

            $workerMsg = new WorkerMessage($response['msgBody']);
            $workerType = $workerMsg->getWorkerType();
            if (!isset($this->_conf['workers'][$workerType])) {
                Log::error("invalid message, worker config not found. worker type={$workerType}");
//                MessageServer::getInstance($this->_conf['driver'])->delete($workerMsgQueueName, $response['msgBody']);
                return;
            }

            $config = $this->_conf['workers'][$workerType];
            if ($this->_workerConf['preConsume']) {
                //预先删除消息
                MessageServer::getInstance()->delete($workerMsgQueueName, $response['msgBody']);
                if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                    Log::debug("pre delete message. msg={$response['msgBody']}");
                }
            }

            //执行队列的处理方法
            $hander = $config['handler'];

            $srvObj = ServiceFactory::getService($hander[0]);

            if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                Log::debug("worker execute message handler=" . json_encode($hander) . ", msg={$response['msgBody']}");
            }

            $s = microtime(true);

            $ret = call_user_func_array([$srvObj, $hander[1]], $workerMsg->getParams());

            if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                $this->_log('workerType:'.$workerType.',use time:'.(microtime(true) - $s));
            }

            if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                Log::debug("worker execute message handler result=" . json_encode($ret) .", msg={$response['msgBody']}");
            }

            unset($workerMsg);

            if (!empty($ret) && !$this->_workerConf['preConsume']) {
                $res = MessageServer::getInstance($this->_conf['driver'])->delete($workerMsgQueueName, $response['msgBody']);
                if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                    Log::debug("finish task delete message. response=".json_encode($response).',result='.json_encode($res));
                }
            }

        } catch (WorkerMessageInvalidException $e) {
            //消息格式不正确
            Log::error("worker Message Invalid, error={$e->getMessage()}:".json_encode($response));
            if ($response && isset($response['token'])) {
                //删除消息
                MessageServer::getInstance($this->_conf['driver'])->delete($workerMsgQueueName, $response['msgBody']);
            }
        } catch (\RedisException $e) {
            Log::error("worker redis error, error={$e->getMessage()}:".json_encode($response));
            //redis异常直接抛出异常退出worker
            throw $e;
        } catch (\Exception $e) {
            Log::error("worker error1, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            //异常休眠1秒
            sleep(1);
        } catch (\Error $e) {
            Log::error("worker error2, error=" . $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
        } catch (\Swoole\Error $e) {
            Log::error("worker error3, error=" . $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
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