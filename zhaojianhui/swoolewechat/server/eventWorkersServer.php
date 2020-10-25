<?php
//echo json_encode(['type'=>'Hello','data'=>['name'=>'zhaojianhui','sex'=>'男']]);exit();
//跑任务的脚本
/**
 * 配置文件：apps/configs/event.php
 * 事件列表：apps/events/*.php
 * 处理代码：apps/classes/Handler/*.php
 */
//载入初始化文件
require_once __DIR__ . '/initServer.php';
//执行脚本
//Swoole::$php->event->runWorker(10);
class Event
{
    private static $_instance;
    static $optionKit;
    static $pidFile;
    static $defaultOptions = array(
        'd|daemon' => '启用守护进程模式',
        'help' => '显示帮助界面',
        'w|worker?' => '设置Worker进程的数量',
    );
    /**
     * 设置PID文件
     * @param $pidFile
     */
    static function setPidFile($pidFile)
    {
        self::$pidFile = $pidFile;
        Swoole::$php->myevent->setPidFile($pidFile);
    }
    /**
     * 显示命令行指令
     */
    static function start()
    {
        /*if (empty(self::$pidFile))
        {
            throw new \Exception("require pidFile.");
        }*/
        $pid_file = self::$pidFile;
        if (is_file($pid_file))
        {
            $server_pid = file_get_contents($pid_file);
        }
        else
        {
            $server_pid = 0;
        }

        if (!self::$optionKit)
        {
            Swoole\Loader::addNameSpace('GetOptionKit', LIBPATH . '/module/GetOptionKit/src/GetOptionKit');
            self::$optionKit = new \GetOptionKit\GetOptionKit;
        }

        $kit = self::$optionKit;
        foreach(self::$defaultOptions as $k => $v)
        {
            //解决Windows平台乱码问题
            if (PHP_OS == 'WINNT')
            {
                $v = iconv('utf-8', 'gbk', $v);
            }
            $kit->add($k, $v);
        }
        global $argv;
        $opt = $kit->parse($argv);
        //存储上一次的操作参数
        $lastOptFile = WEBPATH . '/server/pid/eventOpt.pid';
        if ($opt){
            file_put_contents($lastOptFile, json_encode($opt));
        }
        //合并参数
        if (!empty($argv[1]) && $argv[1] == 'restart'){
            $lastOpt = file_get_contents($lastOptFile);
            $lastOpt && $lastOpt = json_decode( $lastOpt, true);
            $opt = array_merge((array)$opt, (array)$lastOpt);
        }

        try{
            //默认创建进程数量
            $workNum = isset($opt['worker']) && $opt['worker'] ? (int) $opt['worker'] : 2;
            $daemon = isset($opt['daemon']) && $opt['daemon'] ? $opt['daemon'] : false;
            if (empty($argv[1]) or isset($opt['help']))
            {
                goto usage;
            }
            elseif ($argv[1] == 'stop')
            {
                Swoole::$php->myevent->stopWorker();
                exit;
            }
            elseif ($argv[1] == 'start')
            {
                Swoole::$php->myevent->runWorker($workNum, $daemon);
            }elseif ($argv[1] == 'reload'){
                Swoole::$php->myevent->stopWorker();
                Swoole::$php->myevent->runWorker($workNum, true);
            }
            else
            {
                usage:
                $kit->specs->printOptions("php {$argv[0]} start|stop|reload");
                exit;
            }
        }catch (Exception $e){
            echo $e->getMessage()."\r\n";
            exit;
        }

    }
}
//执行事件服务
Event::setPidFile(WEBPATH . '/server/pid/eventServer.pid');
Event::start();