<?php
require_once BASE_PATH . '/vendor/jakub-onderka/php-console-color/src/ConsoleColor.php';

if (!defined('APP_DEBUGGER')) define('APP_DEBUGGER', 1);

class StdWebSocketServer
{

    public static $instance;
    public        $application;
    public        $consoleColor = NULL;
    private       $ws;
    private       $cStore;
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
     *
     * @throws \swoole_exception
     */
    public function __construct($running, $log_file)
    {
        try {

            // 保存运行时状态
            $this->isRunStatus = $running;

            $config = include BASE_PATH . '/sys/config/std_web_socket_server.php';

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


            // 创建 swoole_websocket_server 对象
            $this->ws = new swoole_websocket_server($config['address'], $config['port'], SWOOLE_BASE);

            $this->cStore = new swoole_table(1024);
            $this->cStore->column('fd', swoole_table::TYPE_INT);
            $this->cStore->create();
            $this->ws->cStore = $this->cStore;

            // 设置参数
            unset($config['address']);
            unset($config['port']);
            $this->ws->set($config);

            //绑定 启动
            $this->ws->on('Start', [$this, 'onStart']);

            //绑定 handshake
            //$this->ws->on('handshake', [$this, 'onHandshake']);

            //绑定 open
            $this->ws->on('open', [$this, 'onOpen']);

            //绑定 message
            $this->ws->on('message', [$this, 'onMessage']);

            //绑定 close
            $this->ws->on('close', [$this, 'onClose']);

            //绑定 WorkerStart
            $this->ws->on('WorkerStart', [$this, 'onWorkStart']);

            //绑定 request
            $this->ws->on('request', [$this, 'onRequest']);

            //绑定 task
            $this->ws->on('task', [$this, 'onTask']);

            //绑定 finish
            $this->ws->on('finish', [$this, 'onFinish']);

            // 开启服务器
            $this->ws->start();
        } catch (\swoole_exception $e) {
            $this->cout($e->getMessage(), 'f1', TRUE);
        }

    }//end


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
        if (NULL == $this->consoleColor) {
            $this->consoleColor = new JakubOnderka\PhpConsoleColor\ConsoleColor();
        }

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
     * Debugger msg
     *
     * @param string $text
     * @param string $styles
     * @param bool   $newLine
     *
     * @return  void
     */
    public function dm($text, $styles = 'f255', $newLine = TRUE)
    {
        if (!APP_DEBUGGER) return;
        $this->cout('[' . date('Y/m/d H:i:s') . ']' . $text, $styles, $newLine);
    }//end


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
                    $this->response->end($this->currentFd . '_' . $log);
                default:
                    break;
            }
        }
    }//end


    /**
     * 获取实例对象
     *
     * @param $running
     * @param $log_file
     *
     * @return StdWebSocketServer
     * @throws \swoole_exception
     */
    public static function getInstance($running, $log_file)
    {
        if (!self::$instance) {

            self::$instance = new self($running, $log_file);
        }

        return self::$instance;
    }//end


    /**
     * 进程启动
     *
     * @param $serv
     */
    public function onStart(swoole_websocket_server $ws)
    {
        // 如果是后台运行就重新保存pid
        $this->isRunStatus && file_put_contents(RUN_PID_FILE, $ws->master_pid);
        $this->dm('[SERVER START] MASTER_PID:' . $ws->master_pid, 'f2');
    }//end


    /**
     * WorkStart 回调
     */
    public function onWorkStart(swoole_websocket_server $ws, $worker_id)
    {
        $consoleMsg = sprintf("[ON_WORK_START] WORKER_ID:%d, MASTER_PID:%d", $worker_id, $ws->master_pid);
        $this->dm($consoleMsg, 'f2');

        $this->application = require BASE_PATH . "/cli/bootstrap_sws.php";
        $this->application->di->setShared('ws', $this->ws);

        //全局定时任务
        //$ws->tick(2000, function ($id) use ($ws) {});


    }//end


    /**
     * @param swoole_websocket_server $ws
     * @param swoole_http_request     $request
     */
    public function onOpen(swoole_websocket_server $ws, swoole_http_request $request)
    {
        $ws->cStore->set($request->fd, ['fd' => $request->fd]);

        $this->dm("[ON_OPEN]SERVER#{$ws->worker_pid},HANDSHAKE SUCCESS WITH FD#{$request->fd}");
        //var_dump($ws->exist($request->fd), $ws->getClientInfo($request->fd));
    }//end


    /**
     * @param swoole_websocket_server $ws
     * @param                         $frame
     */
    public function onMessage(swoole_websocket_server $ws, $frame)
    {
        $fd   = $frame->fd;
        $data = $frame->data;

        $this->dm('[ON_MESSAGE] RECEIVED, FROM: ' . $fd
            . ', RECEIVED DATA:' . $data . ', LEN:'
            . strlen($data), 'f3', TRUE);

        //处理请求
        try {
            if ('online' == $data) {
                $ws->push($fd, count($this->cStore));
            } else {
                $args = json_decode($data, JSON_OBJECT_AS_ARRAY);
                if (0 == json_last_error_msg()) {
                    $action              = explode('.', $args['cmd']);
                    $arguments           = [];
                    $arguments['task']   = $action[0] ?? 'main';
                    $arguments['action'] = $action[1] ?? 'main';
                    $arguments['params'] = ['fd' => $fd, 'data' => $args['argv']];
                    s($arguments);
                    $this->application->handle($arguments);
                }else{
                    $ws->push($fd, 'COMMAND ERROR');
                }
            }
        } catch (Exception $e) {
            $ws->push($fd, $e->getMessage());
        }

    }//end


    /**
     * @param swoole_websocket_server $ws
     * @param                         $fd
     */
    public function onClose(swoole_websocket_server $ws, $fd)
    {
        $this->cStore->del($fd);
        $this->dm("[ON_CLOSE] CLOSED, CLIENT_ID:{$fd}", 'f1');
    }//end


    /**
     * @param swoole_websocket_server $ws
     * @param                         $worker_id
     * @param                         $task_id
     * @param                         $data
     *
     * @return array
     */
    public function onTask(swoole_websocket_server $ws, $worker_id, $task_id, $data)
    {
        $consoleMsg = sprintf(
            "[ON_TASK] WORKER_ID:%d, TASK_ID:%d, DATA:%s",
            $worker_id,
            $task_id,
            json_encode($data));
        $this->dm($consoleMsg, 'f3');
        $ret = ['WORKER_ID' => $worker_id, 'TASK_ID' => $task_id];
        //$ws->push((int)$data['fd'], json_encode($ret));
        return $ret;
    }//end


    /**
     * @param swoole_websocket_server $ws
     * @param                         $task_id
     * @param                         $result
     */
    public function onFinish(swoole_websocket_server $ws, $task_id, $result)
    {
        $consoleMsg = sprintf(
            "[ON_FINISH] TASK_ID:%d, RESULT:%s",
            $task_id,
            json_encode($result));
        $this->dm($consoleMsg, 'f3');
    }//end


    /**
     * 广播消息给所有客户端
     *
     * @param string $data
     */
    public function broadcast(string $data)
    {
//        foreach ($this->ws->connections as $fd) {
//            $this->ws->push($fd, $data);
//        }
        foreach ($this->cStore as $u) {
            $this->ws->push($u['fd'], $data);
        }
    }//end

    /**
     * @param swoole_http_request  $request
     * @param swoole_http_response $response
     */
    function onRequest(swoole_http_request $request, swoole_http_response $response)
    {
        //注册捕获错误函数
        register_shutdown_function([$this, 'handleFatal']);
        if ($request->server['request_uri'] == '/favicon.ico' || $request->server['path_info'] == '/favicon.ico') {
            return $response->end();
        }

        $response->end(<<<HTML
<h1>PhaService.StdWebSocketServer Welcome!</h1>
<script>
var ws = new WebSocket('ws://127.0.0.1:8090');
ws.onopen = function (evt) {
    console.log("Connected to PhaService.StdWebSocketServer.");
    ws.send('{"cmd":"main.whoami","argv":"no data"}');
}
ws.onclose = function (evt) {console.log("disconnected.");}
ws.onmessage = function (evt) {console.log('rev:' + evt.data);}
ws.onerror = function (evt, e) {console.log('err: ' + evt.data);}
</script>
HTML
        );
    }//end

}//end
