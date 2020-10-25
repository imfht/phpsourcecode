<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/21
 * Time: 下午7:52
 */

namespace inhere\gearman\traits;

use inhere\gearman\Helper;

/**
 * Class OptionAndConfigTrait
 * @package inhere\gearman\traits
 */
trait OptionAndConfigTrait
{
    /**
     * @var string
     */
    private $fullScript;

    /**
     * @var string
     */
    private $script;

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $cliOpts = [];

    /**
     * handle CLI command and load options
     */
    protected function parseCommandAndConfig()
    {
        $this->parseCliOptions();

        $command = $this->command;
        $supported = ['start', 'stop', 'restart', 'reload', 'status'];

        if (!in_array($command, $supported, true)) {
            $this->showHelp("The command [{$command}] is don't supported!");
        }

        // load CLI Options
        $this->loadCliOptions($this->cliOpts);

        // init Config And Properties
        $this->initConfigAndProperties($this->config);

        // Debug option to dump the config and exit
        if (isset($this->cliOpts['D']) || isset($this->cliOpts['dump'])) {
            $val = isset($this->cliOpts['D']) ? $this->cliOpts['D'] : (isset($this->cliOpts['dump']) ? $this->cliOpts['dump'] : '');
            $this->dumpInfo($val === 'all');
        }
    }

    /**
     * parseCliOptions
     */
    protected function parseCliOptions()
    {
        if (!$this->cliOpts) {
            $this->cliOpts = Helper::parseOptArgs([
                'd', 'daemon', 'w', 'watch', 'h', 'help', 'V', 'version', 'no-test', 'watch-status'
            ]);
        }

        $this->fullScript = implode(' ', $GLOBALS['argv']);
        $this->script = strpos($this->cliOpts[0], '.php') ? "php {$this->cliOpts[0]}" : $this->cliOpts[0];
        $this->command = $command = isset($this->cliOpts[1]) ? $this->cliOpts[1] : 'start';

        unset($this->cliOpts[0], $this->cliOpts[1]);
    }

    /**
     * @param $command
     * @return bool
     */
    protected function dispatchCommand($command)
    {
        $masterPid = $this->getPidFromFile($this->pidFile);
        $isRunning = $this->isRunning($masterPid);

        // start: do Start Server
        if ($command === 'start') {
            // check master process is running
            if ($isRunning) {
                $this->stderr("The worker manager has been running. (PID:{$masterPid})\n", true, -__LINE__);
            }

            return true;
        }

        // check master process
        if (!$isRunning) {
            // now, restart is alias of the start
            if ($command === 'restart') {
                return true;
            }

            $this->stderr("The worker manager is not running. cannot execute the command: {$command}\n", true, -__LINE__);
        }

        // switch command
        switch ($command) {
            case 'stop':
            case 'restart':
                // stop: stop and exit. restart: stop and start
                $this->stopMaster($masterPid, $command === 'stop');
                break;
            case 'reload':
                // reload workers
                $this->reloadWorkers($masterPid);
                break;
            case 'status':
                $cmd = isset($result['cmd']) ? $result['cmd'] : 'status';
                $this->showStatus($cmd, isset($result['watch-status']));
                break;
            default:
                $this->showHelp("The command [{$command}] is don't supported!");
                break;
        }

        return true;
    }

    /**
     * load the command line options
     * @param array $opts
     */
    protected function loadCliOptions(array $opts)
    {
        $map = [
            'c' => 'conf_file',   // config file
            's' => 'servers', // server address

            'n' => 'worker_num',  // worker number do all jobs
            'u' => 'user',
            'g' => 'group',

            'l' => 'log_file',
            'p' => 'pid_file',

            'r' => 'max_run_jobs', // max run jobs for a worker
            'x' => 'max_lifetime',// max lifetime for a worker
            't' => 'timeout',
        ];

        // show help
        if (isset($opts['h']) || isset($opts['help'])) {
            $this->showHelp();
        }
        // show version
        if (isset($opts['V']) || isset($opts['version'])) {
            $this->showVersion();
        }

        // load opts values to config
        foreach ($map as $k => $v) {
            if (isset($opts[$k]) && $opts[$k]) {
                $this->config[$v] = $opts[$k];
            }
        }

        // load Custom Config File
        if ($file = $this->config['conf_file']) {
            if (!file_exists($file)) {
                $this->showHelp("Custom config file {$file} not found.");
            }

            $config = require $file;
            $this->setConfig($config);
        }

        // watch modify
        if (isset($opts['w']) || isset($opts['watch'])) {
            $this->config['watch_modify'] = $opts['w'];
        }

        // run as daemon
        if (isset($opts['d']) || isset($opts['daemon'])) {
            $this->config['daemon'] = true;
        }

        // no test
        if (isset($opts['no-test'])) {
            $this->config['no_test'] = true;
        }

        // disable focus
        if (isset($opts['disable-focus'])) {
            $this->config['disable_focus'] = true;
        }

        // only added jobs
        if (isset($opts['jobs']) && ($added = trim($opts['jobs'], ','))) {
            $this->config['added_jobs'] = strpos($added, ',') ? explode(',', $added) : [$added];
        }

        if (isset($opts['v'])) {
            $opts['v'] = $opts['v'] === true ? '' : $opts['v'];

            switch ($opts['v']) {
                case '':
                    $this->config['log_level'] = self::LOG_INFO;
                    break;
                case 'v':
                    $this->config['log_level'] = self::LOG_PROC_INFO;
                    break;
                case 'vv':
                    $this->config['log_level'] = self::LOG_WORKER_INFO;
                    break;
                case 'vvv':
                    $this->config['log_level'] = self::LOG_DEBUG;
                    break;
                case 'vvvv':
                    $this->config['log_level'] = self::LOG_CRAZY;
                    break;
                default:
                    // $this->config['log_level'] = self::LOG_INFO;
                    break;
            }
        }
    }

    /**
     * @param array $config
     */
    protected function initConfigAndProperties(array $config)
    {
        // init config attributes

        $this->config['daemon'] = (bool)$config['daemon'];
        $this->config['pid_file'] = trim($config['pid_file']);
        $this->config['worker_num'] = (int)$config['worker_num'];
        $this->config['servers'] = str_replace(' ', '', $config['servers']);

        $this->config['log_level'] = (int)$config['log_level'];
        $logFile = trim($config['log_file']);

        if ($logFile === 'syslog') {
            $this->config['log_syslog'] = true;
            $this->config['log_file'] = '';
        } else {
            $this->config['log_file'] = $logFile;
        }

        $this->config['timeout'] = (int)$config['timeout'];
        $this->config['max_lifetime'] = (int)$config['max_lifetime'];
        $this->config['max_run_jobs'] = (int)$config['max_run_jobs'];
        $this->config['restart_splay'] = (int)$config['restart_splay'];

        $this->config['user'] = trim($config['user']);
        $this->config['group'] = trim($config['group']);

        // config value fix ... ...

        if ($this->config['worker_num'] <= 0) {
            $this->config['worker_num'] = self::WORKER_NUM;
        }

        if ($this->config['max_lifetime'] < self::MIN_LIFETIME) {
            $this->config['max_lifetime'] = self::MAX_LIFETIME;
        }

        if ($this->config['max_run_jobs'] < self::MIN_RUN_JOBS) {
            $this->config['max_run_jobs'] = self::MAX_RUN_JOBS;
        }

        if ($this->config['restart_splay'] <= 100) {
            $this->config['restart_splay'] = self::RESTART_SPLAY;
        }

        if ($this->config['timeout'] <= self::MIN_JOB_TIMEOUT) {
            $this->config['timeout'] = self::JOB_TIMEOUT;
        }

        if ($this->config['watch_modify_interval'] <= self::MIN_WATCH_INTERVAL) {
            $this->config['watch_modify_interval'] = self::WATCH_INTERVAL;
        }

        // init properties

        $this->name = trim($config['name']) ?: substr(md5(microtime()), 0, 7);
        $this->doAllWorkerNum = $this->config['worker_num'];
        $this->maxLifetime = $this->config['max_lifetime'];
        $this->verbose = $this->config['log_level'];
        $this->pidFile = $this->config['pid_file'];

        unset($config);
    }

    /**
     * @return string
     */
    public function getFullScript()
    {
        return $this->fullScript;
    }

    /**
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * setCliOpts
     * @param array $opts
     */
    public function setCliOpts(array $opts)
    {
        $this->cliOpts = $opts;
    }

    /**
     * @return array
     */
    public function getCliOpts()
    {
        return $this->cliOpts;
    }
}
