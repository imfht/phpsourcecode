<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-05-05
 * Time: 12:41
 */

namespace inhere\gearman\traits;

use inhere\gearman\Helper;

/**
 * Class ProcessManageTrait
 * @package inhere\gearman\traits
 */
trait ProcessManageTrait
{
    /**
     * The worker id
     * @var int
     */
    protected $id = 0;

    ///////// process control //////////

    /**
     * The PID of the current running process. Set for parent and child processes
     */
    protected $pid = 0;

    /**
     * The PID of the parent(master) process, when running in the forked helper,worker.
     */
    protected $masterPid = 0;

    /**
     * @var bool
     */
    protected $isMaster = false;

    /**
     * @var bool
     */
    protected $isWorker = false;

    /**
     * @var string
     */
    protected $pidFile;

    /**
     * When true, workers will stop look for jobs and the parent process will kill off all running workers
     * @var boolean
     */
    protected $stopWork = false;

    /**
     * workers
     * @var array
     * [
     *  pid => [
     *      'id' => [],
     *      'jobs' => [],
     *      'start_time' => int,
     *      'start_times' => int
     *  ],
     *  ... ...
     * ]
     */
    protected $workers = [];

    /**
     * Number of times this worker has run job
     * @var int
     */
    protected $jobExecCount = 0;

    /**
     * Daemon, detach and run in the background
     */
    protected function runAsDaemon()
    {
        $pid = pcntl_fork();

        if ($pid > 0) {// at parent
            // disable trigger stop event in the __destruct()
            $this->isMaster = false;
            $this->clear();
            $this->stdout("Run the worker manager in the background(PID: $pid)");
            $this->quit();
        }

        $this->pid = getmypid();
        posix_setsid();

        return true;
    }

    /**
     * Bootstrap a set of workers and any vars that need to be set
     */
    protected function startWorkers()
    {
        $lastWorkerId = 0;
        $workersCount = [];

        // If we have "doAllWorkerNum" workers, start them first do_all workers register all functions
        if (($num = $this->doAllWorkerNum) > 0) {
            $jobAry = [];// not focus_on jobs

            foreach ($this->getJobs() as $job) {
                if (!$this->jobsOpts[$job]['focus_on']) {
                    $jobAry[] = $job;
                    $workersCount[$job] = $num;
                }
            }

            for ($x = 0; $x < $num; $x++) {
                $this->startWorker($jobAry, $lastWorkerId++);

                // Don't start workers too fast. They can overwhelm the gearmand server and lead to connection timeouts.
                usleep(500000);
            }
        }

        // Next we loop the workers and ensure we have enough running for each worker
        foreach ($this->handlers as $job => $handler) {
            // If we don't have 'doAllWorkerNum' workers, this won't be set, so we need to init it here
            if (!isset($workersCount[$job])) {
                $workersCount[$job] = 0;
            }

            $workerNum = $this->jobsOpts[$job]['worker_num'];

            while ($workersCount[$job] < $workerNum) {
                $workersCount[$job]++;

                $this->startWorker($job, $lastWorkerId++);

                usleep(500000);
            }
        }

        $this->log(sprintf(
            "Started workers number: %s, Jobs assigned workers info:\n%s",
            Helper::color($lastWorkerId, 'green'),
            Helper::printR($workersCount)
        ), self::LOG_DEBUG);
    }

    /**
     * Start a worker do there are assign jobs. If is in the parent, record worker info.
     *
     * @param string|array $jobs Jobs for the current worker.
     * @param int $workerId The worker id
     * @param bool $isFirst True: Is first start by manager. False: is restart by monitor `startWorkerMonitor()`
     */
    protected function startWorker($jobs, $workerId, $isFirst = true)
    {
        $timeouts = [];
        $jobAry = is_string($jobs) ? [$jobs] : $jobs;
        $defTimeout = $this->get('timeout', 0);

        foreach ($jobAry as $job) {
            $timeouts[$job] = (int)$this->getJobOpt($job, 'timeout', $defTimeout);
        }

        if (!$isFirst) {
            // clear file info
            clearstatcache();
        }

        // fork process
        $pid = pcntl_fork();

        switch ($pid) {
            case 0: // at workers
                $this->isWorker = true;
                $this->isMaster = false;
                $this->masterPid = $this->pid;
                $this->id = $workerId;
                $this->pid = getmypid();
                $this->meta['start_time'] = time();

                if (($jCount = count($jobAry)) > 1) {
                    // shuffle the list to avoid queue preference
                    shuffle($jobAry);
                }

                $this->setProcessTitle(sprintf(
                    "php-gwm: worker process%s (%s)",
                    $this->getShowName(),
                    ($jCount === 1 ? "focus on:{$jobAry[0]}" : 'do all jobs')
                ));
                $this->registerSignals(false);

                if (($splay = $this->get('restart_splay')) > 0) {
                    $this->maxLifetime += mt_rand(0, $splay);
                    $this->log("The worker adjusted max run time to {$this->maxLifetime} seconds", self::LOG_DEBUG);
                }

                $code = $this->startDriverWorker($jobAry, $timeouts);
                $this->log("Worker #$workerId exiting(Exit-Code:$code)", self::LOG_WORKER_INFO);
                $this->quit($code);
                break;

            case -1: // fork failed.
                $this->log('Could not fork workers process! exiting');
                $this->stopWork();
                $this->stopWorkers();
                break;

            default: // at parent
                $text = $isFirst ? 'Start' : 'Restart';
                $this->log("Started worker #$workerId with PID $pid ($text) (Jobs:" . implode(',', $jobAry) . ')', self::LOG_PROC_INFO);
                $this->setWorkerInfo($workerId,[
                    'pid' => $pid,
                    'jobs' => $jobAry,
                ]);
        }
    }

    /**
     * Begin monitor workers
     *  - will monitoring workers process running status
     *
     * @notice run in the parent main process, workers process will exited in the `startWorkers()`
     */
    protected function startWorkerMonitor()
    {
        $this->log('Now, Begin monitor runtime status for all workers', self::LOG_DEBUG);

        // Main processing loop for the parent process
        while (!$this->stopWork || count($this->workers)) {
            $this->dispatchSignal();

            // Check for exited workers
            $status = null;
            $exitedPid = pcntl_wait($status, WNOHANG);

            // We run other workers, make sure this is a worker
            /*
             * If they have exited, remove them from the workers array
             * If we are not stopping work, start another in its place
             */
            if ($worker = $this->getWorkerInfoByPid($exitedPid)) {
                $workerId = $worker['id'];
                $workerJobs = $worker['jobs'];
                $exitCode = pcntl_wexitstatus($status);

                $this->logWorkerStatus($worker, $exitCode);

                if (!$this->stopWork) {
                    $this->startWorker($workerJobs, $workerId, false);
                    $this->saveStatData('worker', $workerId);
                } else {
                    // is required. because while dep `count($this->workers)`
                    unset($this->workers[$workerId]);
                }
            }

            if ($this->stopWork) {
                if (time() - $this->meta['stop_time'] > 60) {
                    $this->log('Workers have not exited, force killing.', self::LOG_PROC_INFO);
                    $this->stopWorkers(SIGKILL);
                }
            } else {
                // If any workers have been running 150% of max run time, forcibly terminate them
                foreach ($this->workers as $id => $worker) {
                    if (!empty($worker['start_time']) && time() - $worker['start_time'] > $this->maxLifetime * 1.5) {
                        $this->logWorkerStatus($worker, self::CODE_MANUAL_KILLED);
                        $this->killProcess($worker['pid'], SIGKILL);
                    }
                }
            }

            // php will eat up your cpu if you don't have this
            usleep(10000);
        }

        $this->log('All workers stopped', self::LOG_PROC_INFO);
    }

    /**
     * Do shutdown Manager
     * @param  int $pid Master Pid
     * @param  boolean $quit Quit, When stop success?
     */
    protected function stopMaster($pid, $quit = true)
    {
        $this->stdout("Stop the manager(PID:$pid)", false);

        // do stop
        // 向主进程发送此信号(SIGTERM)服务器将安全终止；也可在PHP代码中调用`$server->shutdown()` 完成此操作
        if (!$this->killProcess($pid, SIGTERM)) {
            $this->stdout("\nSend stop signal fail! stop failed.");
        }

        $startTime = time();
        $timeout = 30;
        $this->stdout(' .', false);

        // wait exit
        while (true) {
            if (!$this->isRunning($pid)) {
                break;
            }

            if (time() - $startTime > $timeout) {
                $this->stdout(" Failed\nStop the manager process(PID:$pid) failed(timeout)!", true, -2);
                break;
            }

            $this->stdout('.', false);
            sleep(1);
        }

        // stop success
        $this->stdout(" Stopped.\n");

        if ($quit) {
            $this->quit();
        }

        // clear file info
        clearstatcache();

        $this->stdout("Begin restart manager ...");
    }

    /**
     * reloadWorkers
     * @param $masterPid
     */
    protected function reloadWorkers($masterPid)
    {
        $this->stdout("Workers reloading ...");

        $this->sendSignal($masterPid, SIGHUP);

        $this->quit();
    }

    /**
     * Stops all running workers
     * @param int $signal
     * @return bool
     */
    protected function stopWorkers($signal = SIGTERM)
    {
        if (!$this->workers) {
            $this->log('No child process(worker) need to stop', self::LOG_PROC_INFO);
            return false;
        }

        $signals = [
            SIGINT => 'SIGINT(Ctrl+C)',
            SIGTERM => 'SIGTERM',
            SIGKILL => 'SIGKILL',
        ];

        $this->log("Stopping workers({$signals[$signal]}) ...", self::LOG_PROC_INFO);

        foreach ($this->workers as $id => $worker) {
            $pid = $worker['pid'];
            $this->log("Stopping worker #{$worker['id']}(PID:$pid)", self::LOG_PROC_INFO);

            // send exit signal.
            $this->killProcess($pid, $signal);
        }

        return true;
    }

    /**
     * @param array $worker
     * @param int $statusCode
     */
    protected function logWorkerStatus($worker, $statusCode)
    {
        $wid = $worker['id'];
        $pid = $worker['pid'];
        $jobStr = implode(',', $worker['jobs']);

        switch ((int)$statusCode) {
            case self::CODE_MANUAL_KILLED:
                $message = "Worker #$wid(PID:$pid) has been running too long. Forcibly killing process. (Jobs:$jobStr)";
                break;
            case self::CODE_NORMAL_EXITED:
                $message = "Worker #$wid(PID:$pid) normally exited. (Jobs:$jobStr)";
                break;
            case self::CODE_CONNECT_ERROR:
                $message = "Worker #$wid(PID:$pid) connect to job server failed. exiting";
                $this->stopWork();
                break;
            default:
                $message = "Worker #$wid(PID:$pid) died unexpectedly with exit code $statusCode. (Jobs:$jobStr)";
                break;
        }

        $this->log($message, self::LOG_PROC_INFO);
    }

    /**
     * @param string $pidFile
     * @return int
     */
    protected function getPidFromFile($pidFile)
    {
        if ($pidFile && file_exists($pidFile)) {
            return (int)trim(file_get_contents($pidFile));
        }

        return 0;
    }

    /**
     * savePidFile
     */
    protected function savePidFile()
    {
        if ($this->pidFile && !file_put_contents($this->pidFile, $this->pid)) {
            $this->showHelp("Unable to write PID to the file {$this->pidFile}");
        }
    }

    /**
     * delete pidFile
     */
    protected function delPidFile()
    {
        if ($this->pidFile && file_exists($this->pidFile) && !unlink($this->pidFile)) {
            $this->log("Could not delete PID file: {$this->pidFile}", self::LOG_WARN);
        }
    }

    /**
     * mark stopWork
     */
    protected function stopWork()
    {
        //if ()
        $this->stopWork = true;
        $this->meta['stop_time'] = time();
    }

    /**
     * exit
     * @param int $code
     */
    protected function quit($code = 0)
    {
        exit((int)$code);
    }

    /**
     * @param string $file
     * @return array|bool|mixed
     */
    protected function loadStatData($file)
    {
        if (!is_file($file)) {
            return false;
        }

        $text = trim(file_get_contents($file));

        return $text ? json_decode($text, true) : false;
    }

    /**
     * save(create/update) stat data to file
     * @param string $role
     * @param int $workerId
     * @return bool|int
     */
    protected function saveStatData($role = 'all', $workerId = -1)
    {
        if (!$file = $this->config['stat_file']) {
            return false;
        }

        if (!$data = $this->loadStatData($file)) {
            $data = [
                'master' => [],
                'workers' => [],
                'created_at' => time(),
                'updated_at' => 0,
            ];
        }

        switch ($role) {
            case 'master':
                $data['master'] = $this->meta;
                $data['updated_at'] = time();
                break;

            case 'worker':
                if (!isset($this->workers[$workerId])) {
                    return false;
                }

                $data['workers'][$workerId] = $this->workers[$workerId];
                $data['updated_at'] = time();
                break;

            // init all data. on (re)start
            default:
                $data['master'] = $this->meta;
                $data['workers'] = $this->workers;

                if (file_exists($file)) {
                    @rename($file, $file . '.' . date('YmdH'));
                }

                break;
        }

        if (!file_exists($file) && !is_dir(dirname($file))) {
            @mkdir(dirname($file), 0755, true);
        }

        return file_put_contents($file, json_encode($data));
    }

    /**
     * getWorkerId
     * @param  int $workerId
     * @return int
     */
    public function getPidByWorkerId($workerId)
    {
        return isset($this->workers[$workerId]) ? $this->workers[$workerId]['pid'] : 0;
    }

    /**
     * @param $workerId
     * @return null|array
     */
    public function getWorkerInfo($workerId)
    {
        return isset($this->workers[$workerId]) ? $this->workers[$workerId] : null;
    }

    /**
     * getWorkerIdByPid
     * @param  int $pid
     * @return int
     */
    public function getWorkerIdByPid($pid)
    {
        $workerId = -1;

        foreach ($this->workers as $wid => $item) {
            if ($pid === $item['pid']) {
                $workerId = $wid;
                break;
            }
        }

        return $workerId;
    }

    /**
     * getWorkerIdByPid
     * @param  int $pid
     * @return null|array
     */
    public function getWorkerInfoByPid($pid)
    {
        $worker = null;

        foreach ($this->workers as $item) {
            if ($pid === $item['pid']) {
                $worker = $item;
                break;
            }
        }

        return $worker;
    }

    /**
     * @param int $workerId
     * @param array $info
     */
    protected function setWorkerInfo($workerId, array $info)
    {
        if (!isset($this->workers[$workerId])) {
            $this->workers[$workerId] = array_merge([
                'id' => $workerId,
                'pid' => 0,
                'jobs' => [],
                'start_time' => time(),
                'prev_start_time' => 0,
                'start_times' => 1,
            ], $info);
        } else {
            $old = $this->workers[$workerId];
            $old['start_times']++;
            $old['prev_start_time'] = $old['start_time'];
            $old['start_time'] = time();

            $this->workers[$workerId] = array_merge($old, $info);
        }
    }

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return string
     */
    public function getPidFile()
    {
        return $this->pidFile;
    }

    /**
     * @return string
     */
    public function getPidRole()
    {
        return $this->isMaster ? 'Master' : 'Worker';
    }
}
