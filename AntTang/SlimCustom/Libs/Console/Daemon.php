<?php
/**
 * @package     Daemon.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年6月7日
 */

namespace SlimCustom\Libs\Console;

use SlimCustom\Libs\App;
use Clio\Daemon as ClioDaemon;
use SlimCustom\Libs\Console\Console;

/**
 * Daemon
 * 
 * @author Jing Tang <tangjing3321@gmail.com>
 */
class Daemon
{
    /**
     * group
     * 
     * @var $group
     */
    private $group = 'default';
    
    /**
     * callables
     *
     * @var array
     */
    private $callables;
    
    /**
     * commands 命令集
     * 
     * @var array
     */
    protected $commands = [
        'kill' => [
            'kill',
            '关闭任务          kill 任务名称'
        ],
        'killall' => [
            'killAll',
            '关闭所有任务'
        ],
        'start' => [
            'start',
            '启动任务          start 任务名称'
        ],
        'startall' => [
            'startAll',
            '启动所有任务'
        ],
        'list' => [
            'tasks',
            '任务列表'
        ],
        'help' => [
            'help',
            '帮助'
        ],
    ];
    
    /**
     * Run 启动任务
     * 
     * @throws \Exception
     * @return boolean
     */
    public function run()
    {
        list($pidPath, $pidFile) = $this->pidPathInfo('master');
        
        if (count($_SERVER['argv']) > 1) {
            try {
                $this->processCommands();
            }
            catch (\Exception $e) {
                Console::error($e);
            }
        }
        else {
            if (ClioDaemon::isRunning($pidFile)) {
                return true;
            }
            else {
                try {
                    is_dir($pidPath) ?: filesystem()->makeDirectory($pidPath, 0755, true, true);
                    pcntl_signal(SIGCHLD, SIG_DFL);
                    ClioDaemon::work([
                        'pid' => $pidFile,
                        'stdin' => '/dev/null',
                        'stdout' => $pidPath . 'stdout.txt',
                        'stderr' => $pidPath . 'stderr.txt'
                    ], function () {
                        while (true) {
                            //$res = [];
                            if (! empty($this->callables)) {
                                foreach ($this->callables as $name => $callable) {
                                    if (! $callable instanceof \Closure) {
                                        throw new \Exception('Invalid callable to process');
                                    }
                                    $callable(time());
                                    //$res[$name] = $callable(time()) ? true : false;
                                }
                            }
                            sleep(1);
                        }
                    });
                    return true;
                }
                catch (\Exception $e) {
                    logger()->error($e);
                    return false;
                }
                catch (\Throwable $e) {
                    logger()->error($e);
                    return false;
                }
            }
        }
    }

    public function group($name, \Closure $callable)
    {
        $this->group = $name;
        $callable = $callable->bindTo($this);
        return $callable();
    }
    
    /**
     * Call 任务回调
     * 
     * @param integer $schedule 时间计划(单位秒)
     * @param string $name 任务名称                      
     * @param callable $callable 任务回调
     * @return \SlimCustom\Libs\Console\Daemon  
     */
    public function call($schedule, $name, \Closure $callable)
    {
        $Daemon = $this;
        $this->callables[$name] = function($time) use ($schedule, $name, $callable, $Daemon) {
            // 时间计划
            if (isset($this->lastRunTime)) {
                if (($time - $this->lastRunTime) <= $schedule) {
                    return false;
                }
            }
            // 检测任务是否运行
            list($pidPath, $pidFile) = $Daemon->pidPathInfo($name);
            if (is_file($pidPath . 'info.txt')) {
                $info = json_decode(filesystem()->get($pidPath . 'info.txt'), true);
                if (isset($info['stop']) && $info['stop']) {
                    return false;
                }
            }
            
            if (ClioDaemon::isRunning($pidFile)) {
                return true;
            }
            else {
                is_dir($pidPath) ?: filesystem()->makeDirectory($pidPath, 0777, true, true);
                // 忽略信号处理，防止僵尸进程
                pcntl_signal(SIGCHLD, SIG_IGN);
                ClioDaemon::work([
                    'pid' => $pidFile, // required
                    'stdin' => '/dev/null', // defaults to /dev/null
                    'stdout' => $pidPath . 'stdout.txt', // defaults to /dev/null
                    'stderr' => $pidPath . 'stderr.txt'
                ], $callable);
                
                $this->lastRunTime = time();
                $info = [];
                if (is_file($pidPath . 'info.txt')) {
                    $info = json_decode(filesystem()->get($pidPath . 'info.txt'), true);
                }
                $info['lastRunTime'] = $this->lastRunTime;
                filesystem()->put($pidPath . 'info.txt', json_encode($info));
                   
                return true;
            }
        };
        $this->callables[$name] = $this->callables[$name]->bindTo(new \stdClass());
        return $this;
    }
    
    /**
     * 命令处理
     *
     * @return boolean
     */
    private function processCommands()
    {
        Console::initEnvironment();
        array_shift($_SERVER['argv']);
        $command = array_shift($_SERVER['argv']);
        if (! isset($this->commands[$command])) {
            Console::error("无效命令，使用 %ghelp%n 命令查看提示");
            return false;
        }
        if (! call_user_func_array([$this, $this->commands[$command][0]], $_SERVER['argv'])) {
            Console::error("%g{$command} %r执行失败%n");
            return false;
        }
        return true;
    }
    
    /**
     * help 帮助
     *
     * @return boolean
     */
    private function help()
    {
        $tpl = '%y命令      描述              使用' . PHP_EOL;
        foreach ($this->commands as $command => $commandItem) {
            $tplitem = "\033[0;32m%s\x1B[0m" . countSpace($command) . "%s" . PHP_EOL;
            $tpl .= sprintf($tplitem, $command, $commandItem[1]);
        }
        Console::output(trim($tpl, PHP_EOL));
        return true;
    }
    
    /**
     * Kill 关闭任务
     *
     * @param string $name  任务名称
     * @param string $delete  是否删除运行文件
     * @return boolean
     */
    private function kill($name = '', $delete = false)
    {
        if (! $name) {
            return false;
        }
        list($pidPath, $pidFile) = $this->pidPathInfo($name);
        if (ClioDaemon::isRunning($pidFile)) {
            if (! ClioDaemon::kill($pidFile, $delete)) {
                return false;
            }
        }
        $info = [];
        if (is_file($pidPath . 'info.txt')) {
            $info = json_decode(filesystem()->get($pidPath . 'info.txt'), true);
        }
        $info['stop'] = true;
        if (! filesystem()->put($pidPath . 'info.txt', json_encode($info))) {
            return false;
        }
        return true;
    }
    
    /**
     * 关闭所有任务
     * 
     * @return boolean
     */
    private function killAll()
    {
        $tpl = 'task      result' . PHP_EOL;
        foreach (array_keys($this->callables) as $name) {
            $tplitem = "\033[0;32m%s\x1B[0m" . countSpace($name) . "%s" . PHP_EOL;
            list($pidPath, $pidFile) = $this->pidPathInfo($name);
            $tpl .= sprintf($tplitem, $name, $this->kill($name) ? 1 : 0);
        }
        Console::output(trim($tpl, PHP_EOL));
        return true;
    }
    
    /**
     * 启动任务
     *
     * @param string $name
     * @return boolean
     */
    private function start($name = '')
    {
        if (! $name) {
            return false;
        }
        list($pidPath, $pidFile) = $this->pidPathInfo($name);
        if (ClioDaemon::isRunning($pidFile)) {
            if (! ClioDaemon::kill($pidFile)) {
                return false;
            }
        }
        $info = [];
        if (is_file($pidPath . 'info.txt')) {
            $info = json_decode(filesystem()->get($pidPath . 'info.txt'), true);
        }
        $info['stop'] = false;
        if (! filesystem()->put($pidPath . 'info.txt', json_encode($info))) {
            return false;
        }
        
        list($pidPath, $pidFile) = $this->pidPathInfo('master');
        if (! ClioDaemon::isRunning($pidFile)) {
            if (! $this->run()) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 开启所有任务
     *
     * @return boolean
     */
    private function startAll()
    {
        $tpl = 'task      result' . PHP_EOL;
        foreach (array_keys($this->callables) as $name) {
            $tplitem = "\033[0;32m%s\x1B[0m" . countSpace($name) . "%s" . PHP_EOL;
            list($pidPath, $pidFile) = $this->pidPathInfo($name);
            $tpl .= sprintf($tplitem, $name, $this->start($name) ? 1 : 0);
        }
        Console::output(trim($tpl, PHP_EOL));
        return true;
    }
    
    /**
     * 任务列表
     * 
     * @return boolean
     */
    private function tasks()
    {
        $tpl = '%ytask     process         lastRunTime                   stop' . PHP_EOL;
        foreach (array_keys($this->callables) as $name) {
            list($pidPath, $pidFile) = $this->pidPathInfo($name);
            $pid = filesystem()->get($pidFile);
            if (! ClioDaemon::isRunning($pidFile)) {
                $pid = 0;
            }
            $lastRunTime = 0;
            $stop = 1;
            if (is_file($pidPath . 'info.txt')) {
                $info = json_decode(filesystem()->get($pidPath . 'info.txt'), true);
                if (isset($info['lastRunTime'])) {
                    $lastRunTime = date('Y-m-d H:i:s', $info['lastRunTime']);
                }
                if (isset($info['stop']) && ! $info['stop']) {
                    $stop = 0;
                }
            }
            $tplitem = "\033[0;32m%s\x1B[0m" . countSpace($name) . "%s" . countSpace($pid, 15) . "%s" . countSpace($lastRunTime, 30) . "%s" . PHP_EOL;
            $tpl .= sprintf($tplitem, $name, $pid, $lastRunTime, $stop);
        }
        Console::output(trim($tpl, PHP_EOL));
        return true;
    }
    
    /**
     * pid地址信息
     * 
     * @param string $name
     * @return string[]
     */
    protected function pidPathInfo($name)
    {
        $pidPath = App::$instance->dataPath() . 'daemon/'. $this->group . '/' . $name . '/';
        if (! is_dir($pidPath)) {
            filesystem()->makeDirectory($pidPath, 0755, true, true);
        }
        $pidFile = $pidPath . 'pid.txt';
        return [
            $pidPath,
            $pidFile
        ];
    }
}