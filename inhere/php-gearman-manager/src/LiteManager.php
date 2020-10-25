<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/9
 * Time: 下午8:06
 */

namespace inhere\gearman;

use GearmanJob;
use GearmanWorker;
use inhere\gearman\jobs\JobInterface;

/**
 * {@inheritDoc}
 */
class LiteManager extends BaseManager
{
    /**
     * add a job handler
     * @param string $name The job name
     * @param callable|JobInterface $handler The job handler
     * @param array $opts The job options. more @see $jobsOpts property.
     * options allow: [
     *  'timeout' => int
     *  'worker_num' => int
     *  'focus_on' => int
     * ]
     * @return bool
     */
    public function addHandler($name, $handler, array $opts = [])
    {
        if ($this->hasJob($name)) {
            $this->log("The job name [$name] has been registered. don't allow repeat add.", self::LOG_WARN);

            return false;
        }

        if (!$handler && (!is_string($handler) || !is_object($handler))) {
            throw new \InvalidArgumentException("The job [$name] handler data type only allow: string,object");
        }

        // no test handler
        if ($this->config['no_test'] && 0 === strpos($name, 'test')) {
            return false;
        }

        // only added
        if (($added = $this->get('added_jobs')) && !in_array($name, $added, true)) {
            return false;
        }

        $this->trigger(self::EVENT_BEFORE_PUSH, [$name, $handler, $opts]);

        // get handler type
        if (is_string($handler)) {
            if (function_exists($handler)) {
                $opts['type'] = self::HANDLER_FUNC;
            } elseif (class_exists($handler) && is_subclass_of($handler, JobInterface::class)) {
                $handler = new $handler;
                $opts['type'] = self::HANDLER_JOB;
            } elseif (class_exists($handler) && method_exists($handler, '__invoke')) {
                $handler = new $handler;
                $opts['type'] = self::HANDLER_INVOKE;
            } else {
                throw new \InvalidArgumentException(sprintf(
                    "The job(%s) handler(%s) must be is a function name or a class implement the '__invoke()' or a class implement the interface %s",
                    $name,
                    $handler,
                    JobInterface::class
                ));
            }
        } elseif ($handler instanceof \Closure) {
            $opts['type'] = self::HANDLER_CLOSURE;
        } elseif ($handler instanceof JobInterface) {
            $opts['type'] = self::HANDLER_JOB;
        } elseif (method_exists($handler, '__invoke')) {
            $opts['type'] = self::HANDLER_INVOKE;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The job [%s] handler [%s] must instance of the interface %s',
                $name,
                get_class($handler),
                JobInterface::class
            ));
        }

        // init opts
        $opts = array_merge(self::$defaultJobOpt, $this->getJobOpts($name), $opts);
        $opts['focus_on'] = $this->config['disable_focus'] ? false : (bool)$opts['focus_on'];

        if (!$opts['focus_on']) {
            $minCount = max($this->doAllWorkerNum, 1);

            if ($opts['worker_num'] > 0) {
                $minCount = max($opts['worker_num'], $this->doAllWorkerNum);
            }

            $opts['worker_num'] = $minCount;
        } else {
            $opts['worker_num'] = $opts['worker_num'] < 0 ? 0 : (int)$opts['worker_num'];
        }

        $this->setJobOpts($name, $opts);
        $this->handlers[$name] = $handler;

        $this->trigger(self::EVENT_AFTER_PUSH, [$name, $handler, $opts]);

        return true;
    }

    /**
     * Starts a worker for the PECL library
     *
     * @param   array $jobs List of worker functions to add
     * @param   array $timeouts list of worker timeouts to pass to server
     * @return  int The exit status code
     * @throws \GearmanException
     */
    protected function startDriverWorker(array $jobs, array $timeouts = [])
    {
        $wkrTimeout = 5;
        $gmWorker = new GearmanWorker();

        // 设置非阻塞式运行
        $gmWorker->addOptions(GEARMAN_WORKER_NON_BLOCKING);
        $gmWorker->setTimeout($wkrTimeout * 1000); // 5s

        $this->log("The gearman worker started", self::LOG_DEBUG);

        foreach ($this->getServers() as $s) {
            $this->log("Adding a job server: $s", self::LOG_DEBUG);

            // see: https://bugs.php.net/bug.php?id=63041
            try {
                $gmWorker->addServers($s);
            } catch (\GearmanException $e) {
                if ($e->getMessage() !== 'Failed to set exception option') {
                    $this->stopWork();
                    throw $e;
                }
            }
        }

        foreach ($jobs as $job) {
            $timeout = $timeouts[$job] >= 0 ? $timeouts[$job] : 0;
            $this->log("Adding job handler to worker, Name: $job Timeout: $timeout", self::LOG_CRAZY);
            $gmWorker->addFunction($job, [$this, 'doJob'], null, $timeout);
        }

        $start = time();
        $maxRun = $this->config['max_run_jobs'];

        while (!$this->stopWork) {
            $this->dispatchSignal();

            if (
                $gmWorker->work() ||
                $gmWorker->returnCode() === GEARMAN_IO_WAIT ||  // code: 1
                $gmWorker->returnCode() === GEARMAN_NO_JOBS     // code: 35
            ) {
                if ($gmWorker->returnCode() === GEARMAN_SUCCESS) { // code 0
                    continue;
                }

                // no received anything jobs. sleep 5 seconds
                if ($gmWorker->returnCode() === GEARMAN_NO_JOBS) {
                    if ($this->stopWork) {
                        break;
                    }
                    $this->log('No received anything job.(sleep 3s)', self::LOG_CRAZY);
                    sleep(3);
                    continue;
                }

                // if (!@$gmWorker->wait()) {
                if (!$gmWorker->wait()) {
                    // GearmanWorker was called with no connections.
                    if ($gmWorker->returnCode() === GEARMAN_NO_ACTIVE_FDS) { // code: 7
                        if ($this->stopWork) {
                            break;
                        }
                        $this->log('We are not connected to any servers, so wait a bit before trying to reconnect.(sleep 3s)', self::LOG_CRAZY);
                        sleep(3);
                        continue;
                    }

                    if ($gmWorker->returnCode() === GEARMAN_TIMEOUT) { // code: 47
                        $this->log("Timeout({$wkrTimeout}s). Waiting for next job...", self::LOG_CRAZY);
                        continue;
                    }

                    $this->log("Worker Error: {$gmWorker->error()}", self::LOG_DEBUG);
                    break;
                }
            }

            $runtime = time() - $start;

            // Check the worker running time of the current child. If it has been too long, stop working.
            if ($this->maxLifetime > 0 && ($runtime > $this->maxLifetime)) {
                $this->stopWork();
                $this->log("Worker have been running too long time({$runtime}s), exiting", self::LOG_WORKER_INFO);
            }

            if ($this->jobExecCount >= $maxRun) {
                $this->stopWork();
                $this->log("Ran $this->jobExecCount jobs which is over the maximum($maxRun), exiting and restart", self::LOG_WORKER_INFO);
            }
        }

        return $gmWorker->unregisterAll() ? 0 : -1;
    }

    /**
     * Wrapper function handler for all registered functions
     * This allows us to do some nice logging when jobs are started/finished
     * @param GearmanJob $job
     * @return bool
     */
    public function doJob($job)
    {
        $name = $job->functionName();

        if (!$handler = $this->getHandler($name)) {
            $this->log("doJob: $name($h) Unknown job, The job name is not registered.", self::LOG_ERROR);
            return false;
        }

        $ret = null;
        $h = $job->handle();
        $wl = $job->workload();
        $this->jobExecCount++;

        $this->log("doJob: $name($h) Starting job, executed job count: {$this->jobExecCount}", self::LOG_WORKER_INFO);
        $this->log("doJob: $name($h) Job workload: $wl", self::LOG_DEBUG);
        $this->trigger(self::EVENT_BEFORE_WORK, [$job]);

        $status = 1;
        $runTime = microtime(1);

        // Run the job handler here
        try {
            if ($handler instanceof JobInterface) {
                $jobClass = get_class($handler);
                $this->log("doJob: $name($h) Calling Job object handler($jobClass) do the job.", self::LOG_WORKER_INFO);
                $ret = $handler->run($wl, $job);
            } else {
                $jobFunc = is_string($handler) ? $handler : get_class($handler);
                $this->log("doJob: $name($h) Calling function handler($jobFunc) do the job.", self::LOG_WORKER_INFO);
                $ret = $handler($wl, $job);
            }

            $endTime = microtime(1);

            if (is_bool($ret)) {
                $status = (int)$ret;
            }

            $this->log("doJob: $name($h) This job has been completed", self::LOG_WORKER_INFO);
            $this->trigger(self::EVENT_AFTER_WORK, [$job, $ret]);
        } catch (\Exception $e) {
            $status = 0;
            $endTime = microtime(1);

            $this->trigger(self::EVENT_ERROR_WORK, [$job, $e]);
            $this->log(sprintf(
                "doJob: $name($h) Failed to do the job. Exception: %s On %s Line %s\nCode Trace:\n%s",
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            ), self::LOG_ERROR);
        }

        $this->log("doJob: $name($h) Statistics", self::LOG_WORKER_INFO, [
            'status'    => $status,
            'run_time'  => Helper::formatMicroTime($runTime),
            'end_time'  => Helper::formatMicroTime($endTime),
            'exec_count' => $this->jobExecCount,
        ]);

        return $ret;
    }
}
