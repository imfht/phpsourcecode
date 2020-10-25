<?php
namespace app\timer\lib;

use Exception;
use think\Db;

class Worker
{

    public static $daemonize = false;

    public static $pid_file = '';

    public static $log_file = '';

    public static $status_file = '';

    public static $master_pid = 0;

    public static $stdoutFile = '/dev/null';

    public static $workers = array();

    /**
     * @var array
     * 创建多个worker，请在此处配上worker名称
     */
    public static $worker_names = array('timer-worker','db-worker');

    public $worker_name = '';

    public static $status = 0;

    public static $runner = null;

    public static $task_workers = [];

    public $onWorkerStart = null;
    public $onTask = null;

    const STATUS_RUNNING = 1;
    const STATUS_SHUTDOWN = 2;

    public function __construct()
    {
        static::$runner = $this;
    }

    public static function runAll()
    {
        self::checkEnv();
        self::init();
        self::parseCommand();
        self::daemonize();
        self::installSignal();
        self::saveMasterPid();
        self::resetStd();
        self::forkWorkers();
        self::monitorWorkers();
    }

    protected static function checkEnv()
    {
        if (php_sapi_name() != 'cli') {
            exit('only run in command line mode!');
        }

        if(!function_exists('posix_kill')){
            exit('请先安装posix扩展'."\n");
        }

        if(!function_exists('pcntl_fork')){
            exit('请先安装pcntl扩展'."\n");
        }

    }

    protected static function init()
    {
        $temp_dir = TEMP_PATH;
        //$temp_dir = sys_get_temp_dir() . '/jtimer';

        if (!is_dir($temp_dir) && !mkdir($temp_dir)) {
            exit('mkdir runtime fail');
        }
        $test_file = $temp_dir . 'test';
        if(touch($test_file)){
            @unlink($test_file);
        }else{
            exit('permission denied: dir('.$temp_dir.')');
        }

        if (empty(self::$status_file)) {
            self::$status_file = $temp_dir . 'status_file.status';
        }

        if (empty(self::$pid_file)) {
            self::$pid_file = $temp_dir . 'worker.pid';
        }

        if (empty(self::$log_file)) {
            self::$log_file = LOG_PATH . 'worker.log';
        }

        Timer::init();
    }

    protected static function parseCommand()
    {
        global $argv;

        $command_index = 0;

        if (!isset($argv[$command_index])) {
            exit("Usage: php yourfile.php {start|stop|restart|reload|status}\n");
        }
        $command = $argv[$command_index];
        $command2 = $argv[$command_index+1];
        //检测master进程是否存货
        $master_id = @file_get_contents(self::$pid_file);
        $master_is_alive = $master_id && posix_kill($master_id, 0);

        if ($master_is_alive) {
            if ($command == 'start' && posix_getpid() != $master_id) {
                exit('jtimer worker is already running!' . PHP_EOL);
            }
        } else {
            if ($command != 'start') {
                exit('jtimer worker not run!' . PHP_EOL);
            }
        }
        switch ($command) {
            case 'start':
                if($command2 == '-d'){
                    static::$daemonize = true;
                }
                break;
            case 'status':
                if (is_file(self::$status_file)) {
                    @unlink(self::$status_file);
                }
                posix_kill($master_id, SIGUSR2);
                usleep(300000);
                @readfile(self::$status_file);
                exit(0);
            case 'stop':
                //向主进程发出stop的信号
                self::log('jtimer worker[' . $master_id . '] stopping....');
                $master_id && $flag = posix_kill($master_id, SIGINT);
                while ($master_id && posix_kill($master_id, 0)) {
                    usleep(300000);
                }
                self::log('jtimer worker[' . $master_id . '] stop success');
                exit(0);
                break;
            default:
                exit("Usage: php yourfile.php {start|stop|restart|reload|status}\n");
                break;
        }


    }

    protected static function daemonize()
    {
        if(static::$daemonize == false){
            return;
        }
        umask(0);
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new Exception("fork fail");
        } elseif ($pid > 0) {
            exit(0);
        } else {
            if (-1 === posix_setsid()) {
                throw new Exception("setsid fail");
            }
            self::setProcessTitle('jtimer worker: master');
        }

    }

    protected static function saveMasterPid()
    {
        self::$master_pid = posix_getpid();
        if (false === @file_put_contents(self::$pid_file, self::$master_pid)) {
            throw new Exception('fail to save master pid: ' . self::$master_pid);
        }
    }

    protected static function forkWorkers()
    {
        $worker_num = count(self::$worker_names);
        while (count(self::$workers) < $worker_num) {
            $curr_name = current(self::$worker_names);
            if (!in_array($curr_name, array_values(self::$workers))) {
                self::forkOneWorker(static::$runner,$curr_name);
                next(self::$worker_names);
            }
        }
    }

    protected static function installSignal()
    {
        pcntl_signal(SIGINT, array(__CLASS__, 'signalHandler'), false);
        pcntl_signal(SIGUSR2, array(__CLASS__, 'signalHandler'), false);
        pcntl_signal(SIGPIPE, SIG_IGN, false);
        pcntl_signal(SIGHUP, SIG_IGN, false);
    }

    public static function signalHandler($signal)
    {
        switch ($signal) {
            case SIGINT: // Stop.
                self::stopAll();
                break;
            case SIGUSR1:
                break;
            case SIGUSR2: // Show status.
                self::writeStatus();
                break;
        }
    }

    protected static function writeStatus()
    {
        $pid = posix_getpid();
        if (self::$master_pid == $pid) {
            $master_alive = self::$master_pid && posix_kill(self::$master_pid, 0);
            $master_alive = $master_alive ? 'is running' : 'die';
            $result = file_put_contents(self::$status_file, 'master[' . self::$master_pid . '] ' . $master_alive . PHP_EOL, FILE_APPEND | LOCK_EX);
            self::log('status:'.$result);
            foreach (self::$workers as $pid => $worker_name) {
                posix_kill($pid, SIGUSR2);
            }
        } else {
            $name = 'worker[' . $pid . ']';
            $alive = $pid && posix_kill($pid, 0);
            $alive = $alive ? 'is running' : 'die';
            file_put_contents(self::$status_file, $name . ' ' . $alive . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    protected static function forkOneWorker(Worker $runner,$worker_name)
    {

        $pid = pcntl_fork();
        if ($pid > 0) {
            self::$workers[$pid] = $worker_name;
        } elseif ($pid == 0) {
            $runner->worker_name = $worker_name;
            self::log($worker_name . ' jtimer worker start');
            self::setProcessTitle('jtimer worker');
            $runner->run();
        } else {
            throw new Exception('fork one worker fail');
        }
    }

    protected static function resetStd()
    {
        if(static::$daemonize == false){
            return;
        }
        global $STDOUT, $STDERR;
        $handle = fopen(self::$stdoutFile, "a");
        if ($handle) {
            unset($handle);
            @fclose(STDOUT);
            @fclose(STDERR);
            $STDOUT = fopen(self::$stdoutFile, "a");
            $STDERR = fopen(self::$stdoutFile, "a");
        } else {
            throw new Exception('can not open stdoutFile ' . self::$stdoutFile);
        }
    }

    protected static function monitorWorkers()
    {
        self::$status = self::STATUS_RUNNING;
        while (1) {
            pcntl_signal_dispatch();
            $status = 0;
            $pid = pcntl_wait($status, WUNTRACED);
            self::log("worker[ $pid ] exit with signal:".pcntl_wstopsig($status));
            pcntl_signal_dispatch();
            //child exit
            if ($pid > 0) {
                if (self::$status != self::STATUS_SHUTDOWN) {
                    $worker_name = self::$workers[$pid];
                    unset(self::$workers[$pid]);
                    self::forkOneWorker(static::$runner,$worker_name);
                }
            }
        }

    }

    protected function run()
    {
        if($this->onWorkerStart){
            try {
                call_user_func($this->onWorkerStart, $this);
            } catch (\Exception $e) {
                static::log($e);
                // Avoid rapid infinite loop exit.
                sleep(1);
                exit(250);
            } catch (\Error $e) {
                static::log($e);
                // Avoid rapid infinite loop exit.
                sleep(1);
                exit(250);
            }
        }
        while (1) {
            pcntl_signal_dispatch();
            sleep(1);
        }
    }

    public function task($data){
        if($this->onTask){
            $pid = pcntl_fork();
            if ($pid > 0) {
                static::$task_workers[$pid] = $pid;
                if(count(static::$task_workers)){
                    foreach (static::$task_workers as $pid){
                        $pid = pcntl_waitpid($pid,$status,WNOHANG);
                        if( $pid > 0 ){
                            unset(static::$task_workers[$pid]);
                        }
                    }
                }
            } elseif ($pid == 0) { //子进程
                if (function_exists('cli_set_process_title')) {
                    @cli_set_process_title('jtimer task');
                }
                call_user_func($this->onTask, $this,$data);
                exit(0);
            } else {
                throw new Exception('fork one worker fail');
            }
        }
    }

    protected static function setProcessTitle($title)
    {
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        }
    }

    protected static function stopAll()
    {
        $pid = posix_getpid();
        if (self::$master_pid == $pid) { //master
            self::$status = self::STATUS_SHUTDOWN;
            foreach (self::$workers as $pid => $worker_name) {
                //停止worker进程
                posix_kill($pid, SIGINT);
            }
            //停止master进程
            @unlink(self::$pid_file);
            exit(0);
        } else { //child
            self::log('push worker ' . self::$worker_name . ' pid: ' . $pid . ' stop');
            exit(0);
        }
    }

    protected static function log($message)
    {
        $message = '['.date('Y-m-d H:i:s,') .']['. $message . "]\n";
        file_put_contents((string)self::$log_file, $message, FILE_APPEND | LOCK_EX);
    }

}
