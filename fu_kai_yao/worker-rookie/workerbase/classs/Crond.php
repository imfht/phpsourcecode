<?php
namespace workerbase\classs;
use Swoole\Process;
use Swoole\Timer;
use Swoole\Coroutine as Co;
use system\commons\base\traits\Utility;
/**
 * 定时任务服务
 * 协程模式在swoole4.4环境编写，要求使用swoole4.2.9版本以上
 * 进程模式在swoole1.10.5环境编写，高版本对下兼容
 * @author fukaiyao 2019-12-24
 */
class Crond
{
    use Utility;

    /**
     * 当前实例
     * @var Crond
     */
    private static $_instance = null;

    /**
     * 定时任务配置
     */
    private $_conf;

    private $_runningTasks = [];

    //进程id=>jodId
    private $_pidMapToJobId = [];

    /**
     * 退出状态
     * @var bool
     */
    private $_flgExit = false;

    /**
     * 监控cron worker的Timer ID
     */
    private $_monitorTimerId;


    private function __construct()
    {
        $this->_conf = Config::read("", "cron");

        if (isset($this->_conf['schema']) && $this->_conf['schema'] == 'c') { //协程模式
            //协程模式使用swoole4.2.9版本以上
            if (version_compare(swoole_version(), '4.2.9', '<')) {
                throw new \Exception('Coroutine mode version error');
            }
        }
        else { //进程模式
            //swoole4.0.1版本后Server和Timer会自动创建协程，4.2.10后协程中不能fork创建进程
            //关闭协程，采用异步进程(必须放在服务初始化最前面)
            if (version_compare(swoole_version(), '4.0.1', '>=')) {
                \swoole_async_set([
                    'enable_coroutine' => false
                ]);
            }

            //注册子进程回收信号处理
            Process::signal(SIGCHLD, [$this, 'doSignal']);
        }

        //注册主进程回收信号处理
        Process::signal(SIGTERM, [$this, 'doSignal']);
    }

    /**
     * 获取定时任务服务
     * @return Crond
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Crond();
        }
        return self::$_instance;
    }

    public function start()
    {
        ini_set('memory_limit', -1);
        //根据 -d 参数确认是否后台运行
        $options = getopt('d');
        $this->_log("start cron server...");
        if (isset($options['d'])) {
            Process::daemon();//后台运行
            file_put_contents($this->_conf['pid'], posix_getpid());
        }

        //修改主进程名
//        \swoole_set_process_name('php crond.php -d:master');

        //每秒执行一次定时器，执行定时任务
        $this->_monitorTimerId = Timer::tick(1000, [$this, 'doTask']);
        //10s 加载一次配置
        Timer::tick(10000, function () {
            if (isset($this->_conf['schema']) && $this->_conf['schema'] == 'c') { //协程模式
                go(function () {
                    //执行外部命令，重载配置
                    $res = Co::exec(Config::read("phpbin") . ' ' . $this->_conf['cmd'] . ' CronWorkerConfig setCronConfig');

                    Co::defer(function () {
                    });
                });

            }
            else { //进程模式
                //执行外部命令，重载配置
                $process = new Process(function (Process $worker) {
                    $worker->exec(Config::read("phpbin"),  [$this->_conf['cmd'], 'CronWorkerConfig', 'setCronConfig']);
                }, false, 0);
                $pid = $process->start();
            }


            //初始化配置参数
            $getCofig = new ConfigStorage();
            $cronConfg = $getCofig->getConfig('cron');
            if (!empty($cronConfg)) {
                $this->_conf = $cronConfg;
            }
            unset($getCofig);
        });
    }

    /**
     * 定时器每秒回调函数
     * @param int $timer_id     - 定时器的ID
     * @param mixed $params
     */
    public function doTask($timer_id, $params = null)
    {
        //开始任务
        $currentTime = time();
        if (isset($this->_conf['jobs']) && !empty($this->_conf['jobs'])) {
            //轮询执行定时任务
            foreach ($this->_conf['jobs'] as $jobId => $job) {
                if (!isset($job['title']) || !isset($job['cron']) || !isset($job['command']) || !isset($job['id'])) {
                    $this->_log("crontab job config error");
                    continue;
                }

                //当前时间在可执行时间范围
                if ($this->_isTimeByCron($currentTime, $job['cron'])) {
                    //该任务目前有多少个cron进程在执行(最新的定时任务还未退出，阻塞不执行)
                    $crons = $this->_getCrons($job['id']);
                    if ($crons > 0) {
                        continue;
                    }

                    if (isset($job['runType']) && $job['runType'] == 'R' && (!isset($job['jobName']) || !isset($this->_conf['cronConf'][$job['jobName']]))) {
                        $this->_log("remote crontab cronConf error,task={$job['title']}, jobId={$job['id']}");
                        continue;
                    }

                    $s = microtime(true);
                    if (isset($this->_conf['schema']) && $this->_conf['schema'] == 'c') { //协程模式
                        go(function () use($job, $s) {
                            //注册cron
                            $this->_addCron($job['id'], Co::getCid());
                            //clear log
//                            if (is_file($this->_conf['log'])) {
//                                file_put_contents($this->_conf['log'], '');
//                            }

                            Log::setRequireId();
                            App::run();

                            $this->_log("cron worker running task={$job['title']}, jobId={$job['id']},cid:".Co::getCid());
                            try{
                                $cmdArgs = explode(' ',  $job['command']);
                                if (isset($job['runType']) && $job['runType'] == 'R') {
                                    //远程任务
                                    $cronConf = $this->_conf['cronConf'][$job['jobName']];
                                    $url = $cronConf['host'].':'.$cronConf['port'].$cronConf['path'];
                                    $arguments = array(
                                        'class_name' => $cmdArgs[0],
                                        'func_name' => $cmdArgs[1]
                                    );

                                    $reqParams = $this->encryptRequest($cronConf['publicKey'], $arguments);

                                    $http = new CoHttpClient();
                                    $env = Config::read('env');
                                    if (in_array($env, ['dev', 'test'])) {
                                        $res = $http->post($url, $reqParams, false, Co::getCid());
                                        $this->_log($http->response);
                                    } else {
                                        $http->createHttp($url, "POST", $reqParams, Co::getCid());
                                        $http->setDefer(Co::getCid(), 0);//延迟收包，为了效率，只发包不收包
                                        $res = $http->exec(Co::getCid());
                                    }

                                }
                                else { //本地任务
                                    //使用外部命令执行任务，防止协程致命错误终止整个程序运行
//                                    $res = Co::exec($this->_conf['cmd'] .' '. $job['command']);
//                                    if (isset($res['output']) && !empty($res['output']) && is_string($res['output'])) {
//                                        $this->_log($res['output']);
//                                    }

                                    set_time_limit(0);
                                    $cmdConfig = Config::read("cmd_path", "cron");
                                    $res = cliRun(
                                        WORKER_PROJECT_PATH . $cmdConfig['path'],
                                        $cmdConfig['namespace'],
                                        $cmdConfig['suffix'],
                                        $cmdArgs[0],
                                        $cmdArgs[1]
                                    );
                                    if (isset($res['code']) && $res['code'] == -1) {
                                        $this->_log('cron error=' . $res['msg']);
                                    }
                                    $res = true;
                                }
                            }
                            catch (\Exception $e) {
                                $this->_log($e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
                                $res = true;
                            }
                            catch (\Error $e) {
                                $this->_log($e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
                                $res = true;
                            }

                            //本地调试
                            if (WK_ENV == 'local_debug') {
                                $res && $this->_log('cid:'.Co::getCid().',use time:'.(microtime(true) - $s));

                            }

                            Co::defer(function () {
                                if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                                    $this->_log("回收协程资源, cid=".Co::getCid());
                                }
                                $this->_delCronByPid(Co::getCid());
                            });

                            App::end();
                        });


                        //本地调试
                        if (WK_ENV == 'local_debug') {
                            $this->_log("coroutine num：" . Co::stats()['coroutine_num']);
                        }

                    }
                    else { //进程模式
                        //启动cron
                        $cronWorker =  new Process(function (Process $worker) use($job, $s) {
                            //子进程名
                            $worker->name('php crond.php:child');

                            //设置用户组
                            $userName = $this->_conf['user'];
                            $userInfo = posix_getpwnam($userName);
                            if (empty($userInfo)) {
                                $this->_log("start crontab failure, get userinfo failure. user={$userName}");
                                return;
                            }
                            posix_setuid($userInfo['uid']);
                            posix_setgid($userInfo['gid']);

                            //clear log
//                            if (is_file($this->_conf['log'])) {
//                                file_put_contents($this->_conf['log'], '');
//                            }
                            Log::setRequireId();
                            App::run();

                            if (in_array(WK_ENV, ['dev', 'local_debug'])) {
                                $this->_log("cron worker running task={$job['title']}, jobId={$job['id']},pid:".posix_getpid());
                            }

                            $cmdArgs = explode(' ',  $job['command']);

                            if(isset($job['runType']) && $job['runType'] == 'R') {
                                //远程任务
                                $cronConf = $this->_conf['cronConf'][$job['jobName']];
                                $url = $cronConf['host'].':'.$cronConf['port'].$cronConf['path'];
                                $arguments = array(
                                    'class_name' => $cmdArgs[0],
                                    'func_name' => $cmdArgs[1]
                                );

                                $reqParams = $this->encryptRequest($cronConf['publicKey'], $arguments);

                                $http = new HttpClient();
                                $res = $http->post($url, $reqParams);
                                if ($res->isSuccess()) {
                                    //本地调试
                                    if (WK_ENV == 'local_debug') {
                                        $this->_log('pid:'.posix_getpid().',use time:'.(microtime(true) - $s));
                                    }                                }
                            }
                            else {//本地任务
                                try {
//                                    $worker->exec($this->_conf['cmd'],  $cmdArgs);
                                    set_time_limit(0);
                                    $cmdConfig = Config::read("cmd_path", "cron");
                                    $res = cliRun(
                                        WORKER_PROJECT_PATH . $cmdConfig['path'],
                                        $cmdConfig['namespace'],
                                        $cmdConfig['suffix'],
                                        $cmdArgs[0],
                                        $cmdArgs[1]
                                    );
                                    if (isset($res['code']) && $res['code'] == -1) {
                                        $this->_log('cron error=' . $res['msg']);
                                    }
                                    $res = true;
                                }catch (\Exception $e) {
                                    $this->_log($e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
                                    $res = true;
                                }
                                catch (\Error $e) {
                                    $this->_log($e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
                                    $res = true;
                                }

                                //本地调试
                                if (WK_ENV == 'local_debug') {
                                    $res && $this->_log('pid:'.posix_getpid().',use time:'.(microtime(true) - $s));
                                }
                            }

                            App::end(false);

                        }, false, 0);

                        $pid = $cronWorker->start();
                        if ($pid === false) {
                            $this->_log("start cron worker failure.");
                            continue;
                        }
                        //注册cron
                        $this->_addCron($job['id'], $pid);
                    }

                }

            }
        }
    }

    /**
     * 处理进程信号
     * @param int $sig  - 信号类型
     */
    public function doSignal($sig) {
        switch ($sig) {
            case SIGCHLD: //子进程退出（协程模式没有子进程）
                //必须为false，非阻塞模式
                while($ret =  Process::wait(false)) {
                    $exitPid = $ret['pid'];
                    $this->_delCronByPid($exitPid);
                    if ($this->_delCronByPid($exitPid) && in_array(WK_ENV, ['dev', 'local_debug'])) {
                        $this->_log("回收进程资源, pid={$exitPid}");
                    }
                }

                if ($this->_flgExit && $this->_getTotalCrons() < 1) {
                    $this->_log("cron server shutdown...");
                    //收到主进程退出信号，当子进程都退出后，结束master进程
                    @unlink($this->_conf['pid']);
                    exit(0);
                }

                break;
            case SIGTERM: //终止信号(异常退出，或者kill命令)，子进程全都退出
                $this->_log("recv terminate signal, exit crond.");
                //关闭监控
                if ($this->_monitorTimerId) {
                    Timer::clear($this->_monitorTimerId);
                }

                if ($this->_conf['schema'] == 'p') { //进程模式
                    //主进程退出信号标记
                    $this->_flgExit = true;
                    if (!empty($this->_pidMapToJobId)) {
                        foreach (array_keys($this->_pidMapToJobId) as $pid) {
                            Process::kill($pid, SIGTERM);
                        }
                    }
                    elseif ($this->_getTotalCrons() < 1) {
                        $this->_log("cron server shutdown...");
                        //子进程都退出，结束master进程
                        @unlink($this->_conf['pid']);
                        exit(0);
                    }

                }
                elseif ($this->_conf['schema'] == 'c') { //协程模式
                    if (!Timer::exists($this->_monitorTimerId) && $this->_getTotalCrons() < 1) {
                        @unlink($this->_conf['pid']);
                        $this->_log("cron server shutdown...");
                        exit(0);
                    }
                    go(function () {
                        Co::sleep(1);
                        Process::kill(posix_getpid(), SIGTERM);
                        Co::defer(function () {
                            $this->_log("剩余协程数量:". $this->_getTotalCrons());
                        });
                    });
                }
                break;
        }
    }

    /**
     * 添加cron worker
     * @param string $jobId
     * @param  int $pid - 进程id
     */
    private function _addCron($jobId, $pid)
    {
        if (!isset($this->_runningTasks[$jobId])) {
            $this->_runningTasks[$jobId] = [];
        }
        $this->_runningTasks[$jobId][$pid] = true;
        $this->_pidMapToJobId[$pid] = $jobId;
    }

    /**
     * 根据jobId返回指定cron worker目前正在运行的cron数量
     * @param string $jobId
     * @return int
     */
    private function _getCrons($jobId)
    {
        if (!isset($this->_runningTasks[$jobId])) {
            return 0;
        }
        return count($this->_runningTasks[$jobId]);
    }

    /**
     * 删除cron worker
     * @param int $pid      - 进程id
     * @return bool
     */
    private function _delCronByPid($pid) {
        if (!isset($this->_pidMapToJobId[$pid])) {
            return false;
        }
        $jobId = $this->_pidMapToJobId[$pid];
        unset($this->_pidMapToJobId[$pid]);
        if (isset($this->_runningTasks[$jobId]) && isset($this->_runningTasks[$jobId][$pid])) {
            unset($this->_runningTasks[$jobId][$pid]);
        }
        return true;
    }

    /**
     * 返回cron Task总数
     * @return int
     */
    private function _getTotalCrons()
    {
        if (empty($this->_runningTasks)) {
            return 0;
        }
        $total = 0;
        foreach (array_keys($this->_runningTasks) as $jobId) {
            $total += count($this->_runningTasks[$jobId]);
        }
        return $total;
    }

    /**
     * 根据定时任务时间配置，检测当前时间是否在指定时间内
     * @param int $time     - 当前时间
     * @param string $cron  - 定时任务配置
     * @return bool 不在指定时间内返回false, 否则返回true
     */
    private function _isTimeByCron($time, $cron)
    {
        $cronParts = explode(' ', $cron);
        if (count($cronParts) != 6) {
            return false;
        }

        list($sec, $min, $hour, $day, $mon, $week) = $cronParts;

        $checks = array('sec' => 's', 'min' => 'i', 'hour' => 'G', 'day' => 'j', 'mon' => 'n', 'week' => 'w');

        $ranges = array(
            'sec' => '0-59',
            'min' => '0-59',
            'hour' => '0-23',
            'day' => '1-31',
            'mon' => '1-12',
            'week' => '0-6',
        );

        foreach ($checks as $part => $c) {
            $val = $$part;
            $values = array();

            /*
                For patters like 0-23/2
            */
            if (strpos($val, '/') !== false) {
                //Get the range and step
                list($range, $steps) = explode('/', $val);

                //Now get the start and stop
                if ($range == '*') {
                    $range = $ranges[$part];
                }
                list($start, $stop) = explode('-', $range);

                for ($i = $start; $i <= $stop; $i = $i + $steps) {
                    $values[] = $i;
                }
            } /*
                For patters like :
                2
                2,5,8
                2-23
            */
            else {
                $k = explode(',', $val);

                foreach ($k as $v) {
                    if (strpos($v, '-') !== false) {
                        list($start, $stop) = explode('-', $v);

                        for ($i = $start; $i <= $stop; $i++) {
                            $values[] = $i;
                        }
                    } else {
                        $values[] = $v;
                    }
                }
            }

            if (!in_array(date($c, $time), $values) and (strval($val) != '*')) {
                return false;
            }
        }

        return true;
    }


    /**
     * 输出日志
     * @param $msg
     */
    private function _log($msg)
    {
        $dateStr = date("Y-m-d H:i:s");
        echo "[{$dateStr}] {$msg}\n";
    }
}