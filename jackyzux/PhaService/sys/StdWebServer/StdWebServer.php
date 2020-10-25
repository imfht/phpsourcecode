<?php

require_once BASE_PATH . '/vendor/jakub-onderka/php-console-color/src/ConsoleColor.php';

class StdWebServer
{

    public static $instance;
    public        $consoleColor;
    private       $http;
    private       $application;

    /**
     * 运行时状态 / 是否后台运行
     * @var boolean
     */
    private $isRunStatus = FALSE;

    /**
     * 初始化
     *
     * @param $running
     * @param $log_file
     */
    public function __construct($running, $log_file)
    {
        // 保存运行时状态
        $this->isRunStatus = $running;


        $this->consoleColor = new JakubOnderka\PhpConsoleColor\ConsoleColor();

        $config = include BASE_PATH . '/sys/config/std_web_server.php';

        if (NULL == $config['log_file']) {
            $config['log_file'] = $log_file;
        }

        echo PHP_EOL;
        $this->cout('┌' . str_repeat('─', 78), 'f159', TRUE);
        $this->cout('│', 'f159');
        $this->cout(sprintf(' Create Web Server on: %s:%d ...', $config['address'], $config['port']), 'bold,f2', TRUE);
        $this->cout('├' . str_repeat('─', 78), 'f159', TRUE);

        $this->cout('│ Configure:', 'bold,f159', TRUE);
        $this->cout('├' . str_repeat('─ ', 39), 'f159', TRUE);

        foreach ($config as $k => $v) {
            $this->cout('│ ', 'f159');
            $this->cout($k . ' : ', 'f2');
            $this->cout($v, 'f3', TRUE);
        }
        $this->cout('└' . str_repeat('─', 78), 'f159', TRUE);
        echo PHP_EOL;
        $this->cout(str_repeat(' ', 28) . 'Server Listening ...', 'bold,f1', TRUE);
        $this->cout(str_repeat(' .', 38), 'bold,f166', TRUE);
        echo PHP_EOL;


        // 创建swoole_http_server对象
        $this->http = new swoole_http_server($config['address'], $config['port']);

        // 设置参数
        unset($config['address']);
        unset($config['port']);
        $this->http->set($config);

        // 服务器被启动
        $this->http->on('Start', [$this, 'onStart']);

        // 绑定WorkerStart
        $this->http->on('WorkerStart', [$this, 'onWorkStart']);

        // 绑定request
        $this->http->on('request', [$this, 'onRequest']);

        // 绑定task
        $this->http->on('task', [$this, 'onTask']);

        // 绑定finish
        $this->http->on('finish', [$this, 'onFinish']);

        // 开启服务器
        $this->http->start();

    }

    /**
     * Console Color Print
     *
     * @param string $text
     * @param string $styles
     * @param bool   $newLine
     *
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function cout(string $text, string $styles = 'f255', $newLine = FALSE)
    {
        $_style = [];
        if ('f255' == $styles) {
            $_style[] = 'color_255';
        } else {
            $styleAr = explode(',', $styles);
            foreach ($styleAr as $style) {
                $style = trim($style);
                if ($style{0} == 'f' && is_numeric($style{1})) {
                    $_style[] = 'color_' . substr($style, 1);
                } elseif ($style{0} == 'b' && is_numeric($style{1})) {
                    $_style[] = 'bg_color_' . substr($style, 1);
                } else {
                    $_style[] = $style;
                }
            }
        }
        echo $this->consoleColor->apply($_style, $text);
        if (TRUE == $newLine) echo PHP_EOL;
    }//end

    /**
     * 进程启动
     *
     * @param $serv
     *
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function onStart($serv)
    {
        // 如果是后台运行就重新保存pid
        $this->isRunStatus && file_put_contents(RUN_PID_FILE, $serv->master_pid);
        $consoleMsg = sprintf("[SERVER START][%s] master_pid:%s\n", date('Y/m/d H:i:s'), $serv->master_pid);
        $this->cout($consoleMsg, 'f2', TRUE);
    }

    /**
     * WorkStart 回调
     */
    public function onWorkStart($serv, $worker_id)
    {
        $consoleMsg = sprintf("[WORKER START:%s][%s] master_pid:%s",$worker_id,
            date('Y/m/d H:i:s'),$serv->master_pid);

        $this->cout($consoleMsg, 'f2', TRUE);


        $di     = new Phalcon\Di\FactoryDefault();
        $server = $this->http;
        $di->setShared('server', function () use ($server) {
            return $server;
        });

        include APP_PATH . '/config/router.php';
        include APP_PATH . '/config/services.php';
        //$config = $di->getConfig();
        include APP_PATH . '/config/loader.php';
        $this->application = new \Phalcon\Mvc\Application($di);
        $this->application->setDI($di);

    }//end

    /**
     * 处理http请求
     */
    public function onRequest($request, $response)
    {
        //注册捕获错误函数
        register_shutdown_function([$this, 'handleFatal']);
        if ($request->server['request_uri'] == '/favicon.ico' || $request->server['path_info'] == '/favicon.ico') {
            return $response->end();
        }

        $_SERVER = $request->server;

        //构造url请求路径,phalcon获取到$_GET['_url']时会定向到对应的路径，否则请求路径为'/'
        $_GET['_url'] = $request->server['request_uri'];

        if ($request->server['request_method'] == 'GET' && isset($request->get)) {
            foreach ($request->get as $key => $value) {
                $_GET[$key]     = $value;
                $_REQUEST[$key] = $value;
            }
        }
        if ($request->server['request_method'] == 'POST' && isset($request->post)) {
            foreach ($request->post as $key => $value) {
                $_POST[$key]    = $value;
                $_REQUEST[$key] = $value;
            }
        }
        //处理请求
        ob_start();
        try {

            echo $this->application->handle()->getContent();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $result = ob_get_contents();
        ob_end_clean();
        $response->end($result);
    }

    /**
     * 处理task任务
     */
    public function onTask($serv, $task_id, $from_id, $data)
    {
        echo "[" . microtime(TRUE) . "]This Task {$task_id} from Worker {$from_id}\n";
        echo "[" . date('Y-m-d H:i:s') . "] This Task {$task_id} from Worker {$from_id}\n";
        echo "This data {$data} from Worker {$from_id}\n";
    }

    /**
     * task 完成回调
     */
    public function onFinish($serv, $taskId, $data)
    {
        echo "Task {$taskId} finish\n";
        echo "Result: {$data}\n";
    }

    /**
     * 获取实例对象
     */
    public static function getInstance($running, $log_file)
    {
        if (!self::$instance) {

            self::$instance = new StdWebServer($running, $log_file);
        }

        return self::$instance;
    }

    /**
     * 捕获Server运行期致命错误
     */
    public function handleFatal()
    {
        $error = error_get_last();
        if (isset($error['type'])) {
            switch ($error['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                    $message = $error['message'];
                    $file    = $error['file'];
                    $line    = $error['line'];
                    $log     = "$message ($file:$line)\nStack trace:\n";
                    $trace   = debug_backtrace();
                    foreach ($trace as $i => $t) {
                        if (!isset($t['file'])) {
                            $t['file'] = 'unknown';
                        }
                        if (!isset($t['line'])) {
                            $t['line'] = 0;
                        }
                        if (!isset($t['function'])) {
                            $t['function'] = 'unknown';
                        }
                        $log .= "#$i {$t['file']}({$t['line']}): ";
                        if (isset($t['object']) and is_object($t['object'])) {
                            $log .= get_class($t['object']) . '->';
                        }
                        $log .= "{$t['function']}()\n";
                    }
                    if (isset($_SERVER['REQUEST_URI'])) {
                        $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
                    }
                    //error_log($log);
                    //$serv->send($this->currentFd, $log);
                    $this->application->logger->info('error log: ' . $log);
                    $this->response->end($this->currentFd . '_' . $log);
                default:
                    break;
            }
        }
    }
}
