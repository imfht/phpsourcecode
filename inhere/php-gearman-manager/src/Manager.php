<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-28
 * Time: 17:03
 */

namespace inhere\gearman;

use inhere\gearman\tools\Telnet;

/**
 * Class Manager - gearman worker manager
 * @package inhere\gearman
 */
class Manager extends LiteManager
{
    /**
     * The PID of the helper process
     * @var int
     */
    protected $helperPid = 0;

    /**
     * @var bool
     */
    protected $isHelper = false;

    /**
     * wait response for process signal
     * @var bool
     */
    private $waitForSignal = false;

    /**
     * @var \Closure
     */
    private $handlersLoader;

    /**
     * @param array $config
     */
    protected function initConfigAndProperties(array $config)
    {
        $this->config['loader_file'] = trim($config['loader_file']);

        $this->config['watch_modify'] = (bool)$config['watch_modify'];
        $this->config['watch_modify_interval'] = (int)$config['watch_modify_interval'];

        // parent ...
        parent::initConfigAndProperties($config);

        if ($file = $this->config['loader_file']) {
            if (!file_exists($file)) {
                $this->stderr("The handlers loader file(loader_file) not exists, file: $file");
            }

            $this->handlersLoader = function (self $mgr) {
                require $mgr->get('loader_file');
            };
        }
    }

    /**
     * beforeStart
     */
    protected function beforeStart()
    {
        // load all job handlers
        if ($loader = $this->handlersLoader) {
            $loader($this);
        }
    }

    /**
     * beforeStartWorkers
     */
    protected function beforeStartWorkers()
    {
        // fork a Helper process
        $this->startHelper();
    }

    /**
     * afterStart
     */
    protected function afterStart()
    {
        // stop Helper
        $this->stopHelper();

        parent::afterStart();
    }

    /**
     * Forks the process and runs the given method. The parent then waits
     * for the worker child process to signal back that it can continue
     *
     * param   string $method Class method to run after forking @see `startWatcher()`
     *
     * @old forkOwner()
     */
    protected function startHelper()
    {
        $this->waitForSignal = true;
        $pid = pcntl_fork();

        switch ($pid) {
            case 0: // at workers(helper process)
                $this->setProcessTitle(sprintf("php-gwm: helper process%s", $this->getShowName()));
                $this->isMaster = false;
                $this->isHelper = true;
                $this->masterPid = $this->pid;
                $this->pid = getmypid();

                $this->registerSignals(false);
                $this->validateJobServers();
                $this->startWatchModify();
                break;
            case -1:
                $this->log('Failed to fork helper process', self::LOG_ERROR);
                $this->stopWork();
                break;
            default: // at parent
                $this->log("Started helper process with PID $pid", self::LOG_PROC_INFO);
                $this->helperPid = $pid;

                while ($this->waitForSignal && !$this->stopWork) {
                    $this->dispatchSignal();

                    pcntl_waitpid($pid, $status, WNOHANG);
                    $exitCode = pcntl_wexitstatus($status);

                    if (self::CODE_CONNECT_ERROR === $exitCode) {
                        $servers = $this->getServers(false);
                        $this->log("Error validating job servers, please check server address.(job servers=$servers)");
                        $this->quit($exitCode);
                    } elseif (self::CODE_NORMAL_EXITED !== $exitCode) {
                        $this->log("Helper process exited with non-zero exit code [$exitCode].");
                        $this->quit($exitCode);
                    }

                    usleep(10000);
                }
                break;
        }
    }

    /**
     * Validates the job servers status
     */
    protected function validateJobServers()
    {
        foreach ($this->getServers() as $s) {
            $result = '';
            $this->log("Testing job server $s", self::LOG_DEBUG);

            try {
                list($h, $p) = strpos($s, ':') ? explode(':', $s) : [$s, 4730];
                $telnet = new Telnet($h, $p, [
                    'timeout' => 3,
                ]);
                $result = $telnet->command('status');
                $telnet->close();
            } catch (\Exception $e) {
                $this->log('Exception: ' . $e->getMessage(), self::LOG_ERROR);
                $this->stopWork();
                $this->quit(self::CODE_CONNECT_ERROR);
            }

            if (0 === strpos($result, 'ERR')) {
                $this->log("Failed to get status: $s, result: $result, server: $s", self::LOG_ERROR);
                $this->stopWork();
                $this->quit(self::CODE_CONNECT_ERROR);
            }
        }

        // Since we got here, all must be ok, send a CONTINUE
        $this->log("Server address verify success. Sending SIGCONT(continue) to master(PID:{$this->masterPid}).", self::LOG_PROC_INFO);
        $this->sendSignal($this->masterPid, SIGCONT);
    }

    /**
     * Forked method that watch the worker code and checks it if desired
     * @old validateWorkers()
     */
    protected function startWatchModify()
    {
        if ($this->config['watch_modify'] && ($loaderFile = $this->config['loader_file'])) {
            $lastCheckTime = 0;
            $checkInterval = $this->config['watch_modify_interval'];

            $this->log("Running loop to watch modify(interval:{$checkInterval}s) for loader_file: $loaderFile", self::LOG_DEBUG);

            while (!$this->stopWork) {
                $this->dispatchSignal();

                if ($this->stopWork) {
                    break;
                }

                // $maxTime = 0;
                $mdfTime = filemtime($loaderFile);
                // $maxTime = max($maxTime, $mdfTime);

                $this->log("loader_file: {$loaderFile} - MODIFY TIME: $mdfTime,LAST CHECK TIME: $lastCheckTime", self::LOG_DEBUG);

                if ($lastCheckTime && $mdfTime > $lastCheckTime) {
                    clearstatcache();
                    $this->log("New code modify found. Sending SIGHUP(reload) to master(PID:{$this->masterPid})", self::LOG_PROC_INFO);
                    $this->sendSignal($this->masterPid, SIGHUP);
                }

                $lastCheckTime = time();
                sleep($checkInterval);
            }
        }

        $this->log('Helper stopped', self::LOG_PROC_INFO);
        $this->quit();
    }

    /**
     * {@inheritDoc}
     */
    protected function startWorker($jobs, $workerId, $isFirst = true)
    {
        $this->isHelper = false;

        parent::startWorker($jobs, $workerId, $isFirst);
    }

    /**
     * stop Helper process
     */
    protected function stopHelper()
    {
        if ($pid = $this->helperPid) {
            $this->helperPid = 0;
            $this->log("Stopping helper(PID:$pid) ...", self::LOG_PROC_INFO);
            $this->killProcess($pid);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function registerSignals($isMaster = true)
    {
        parent::registerSignals($isMaster);

        if ($isMaster) {
            pcntl_signal(SIGCONT, [$this, 'signalHandler'], false);
        }
    }

    /**
     * Handles signals
     * @param int $sigNo
     */
    public function signalHandler($sigNo)
    {
        if ($this->isMaster && $sigNo === SIGCONT) {
            $this->log('Validation through, continue(SIGCONT) start manager...', self::LOG_PROC_INFO);
            $this->waitForSignal = false;
        } else {
            parent::signalHandler($sigNo);
        }
    }

    /**
     * Shows the scripts help info with optional error message
     * @param string $msg
     * @param int $code The exit code
     */
    protected function showHelp($msg = '', $code = 0)
    {
        $usage = Helper::color('USAGE:', 'brown');
        $commands = Helper::color('COMMANDS:', 'brown');
        $sOptions = Helper::color('SPECIAL OPTIONS:', 'brown');
        $pOptions = Helper::color('PUBLIC OPTIONS:', 'brown');
        $tool = Helper::color('gwm', 'green');
        $version = Helper::color(self::VERSION, 'brown');
        $script = $this->getScript();

        if ($msg) {
            $code = $code ?: self::CODE_UNKNOWN_ERROR;
            echo Helper::color('ERROR:', 'red') . "\n  " . wordwrap($msg, 108, "\n  ") . "\n\n";
        }

        echo <<<EOF
Gearman worker manager($tool) script tool. Version $version

$usage
  $script {COMMAND} -c CONFIG [-v LEVEL] [-l LOG_FILE] [-d] [-w] [-p PID_FILE]
  $script -h
  $script -D

$commands
  start             Start gearman worker manager(default)
  stop              Stop running's gearman worker manager
  restart           Restart running's gearman worker manager
  reload            Reload all running workers of the manager
  status            Get gearman worker manager runtime status

$sOptions
  start/restart
    -w,--watch         Automatically watch and reload when 'loader_file' has been modify
    -d,--daemon        Daemon, detach and run in the background
       --jobs          Only register the assigned jobs, multi job name separated by commas(',')
       --no-test       Not add test handler, when job name prefix is 'test'.(eg: test_job)
       --disable-focus Diable focus on job for worker

  status
    --cmd COMMAND      Send command when connect to the job server. allow:status,workers.(default:status)
    --watch-status     Watch status command, will auto refresh status.

$pOptions
  -c CONFIG          Load a custom worker manager configuration file
  -s HOST[:PORT]     Connect to server HOST and optional PORT, multi server separated by commas(',')

  -n NUMBER          Start NUMBER workers that do all jobs

  -u USERNAME        Run workers as USERNAME
  -g GROUP_NAME      Run workers as user's GROUP NAME

  -l LOG_FILE        Log output to LOG_FILE or use keyword 'syslog' for syslog support
  -p PID_FILE        File to write master process ID out to

  -r NUMBER          Maximum run job iterations per worker
  -x SECONDS         Maximum seconds for a worker to live
  -t SECONDS         Number of seconds gearmand server should wait for a worker to complete work before timing out

  -v [LEVEL]         Increase verbosity level by one. (eg: -v vv | -v vvv)

  -h,--help          Shows this help information
  -V,--version       Display the version of the manager
  -D,--dump [all]    Parse the command line and config file then dump it to the screen and exit.\n\n
EOF;
        $this->quit($code);
    }

    /**
     * @return string
     */
    public function getPidRole()
    {
        return $this->isMaster ? 'Master' : ($this->isHelper ? 'Helper' : 'Worker');
    }
}
