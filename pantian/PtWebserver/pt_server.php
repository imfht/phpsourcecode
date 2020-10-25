<?php
include 'PTHttpServer.php';
include 'ptServer.php';
define('PT_WEB_VERSION' , '2.0');
class pt_server
{
    /**
     * @var \PTHttpServer
     */
    static $PTHttpServer = '';

    /**
     * 服务器配置信息
     *
     * @var array
     */
    private $swoole_http_config = [];

    public $httpConf = [];
    /**
     * 访问主文件
     *
     * @var string
     */
    private $access_index = 'index.php';
    /**
     * 配置文件
     *
     * @var string
     */
    private $confFile = './conf/httpConf.php';
    /**
     * 网站根目录
     *
     * @var string
     */
    private $document_root = 'html';
    /**
     * 日志目录
     *
     * @var string
     */
    static $logPath = '/var/log/pt_server/';
    /**
     * 进程名称
     *
     * @var string
     */
    static $BaseProcessName = 'pt_server_';
    /**
     * 默认监听所有ip
     *
     * @var string
     */
    static $default_listen_ip = '0.0.0.0';
    /**
     * 默认监听端口
     *
     * @var int
     */
    static $default_port = 80;
    /**
     * 配置文件被修改的时间
     *
     * @var int
     */
    static $confFileReviseTime = 0;
    /**
     * 当前网站配置数据
     *
     * @var null
     */
    static $currentWebConf = null;
    /**
     * 请求文件类型
     *
     * @var null
     */
    static $requestFileType = null;

    /**
     * 请求加载的文件
     *
     * @var string
     */
    static $loadFileName = '';
    /**
     * 会话名称
     *
     * @var string
     */
    static $session_name = 'PHPSESSID';
    /**
     * 在$_SERVER 下专用这个保存与应用程序的交互
     *
     * @var string
     */
    static $pt_http_server_key = 'pt_http_server';
    /**
     * 网站配置数组
     *
     * @var array
     */
    public $webConf = [];

    static $ContentType = 'html';
    /**
     * 工作进程 Id
     *
     * @var int
     */
    public $CurrentWorkerId = 0;

    public $worker_info = [];
    /**
     * 管理端口
     * @var int
     */
    private static $admin_port=29929;

    private static $pidFile='/var/pt_server_pid';
    /**
     * @var \swoole_http_response
     */
    public $response = null;
    function __construct ()
    {
        if(isset($argv[1])){
            self::$pidFile = $argv[1];
        }
    }
    /**
     * 加载配置文件
     *
     * @param bool $forceLoad 是否强制加载
     * @return bool
     */
    function LoadHttpConf ($forceLoad = FALSE)
    {
        try{
            clearstatcache();
            if (file_exists($this->confFile)) {
                if (filemtime($this->confFile) > self::$confFileReviseTime || $forceLoad) {
                    $this->httpConf = include $this->confFile;
                    /*$userConf = lib\web::getHttpConfig();
                    if($userConf){
                        $this->httpConf = array_merge($this->httpConf, $userConf);
                    }*/
                    if (isset($this->httpConf['web']) && $this->httpConf['web']) {
                        $this->parseWebConfig();
                    } else {
                        $this->access_log('not web config data');
                    }
                    return TRUE;
                } else {
                    $this->httpConf=self::$PTHttpServer->getHttpConf();
                }
            } else {
                throw new Exception($this->confFile.',file of web config is not find'.PHP_EOL);
            }

            return FALSE;
        }catch(\Exception $e){
            echo '异常：'. $e->getMessage().PHP_EOL;
        }
    }
    /**
     * 域名与端口序列化
     *
     * @param null $hostPort
     * @return string
     */
    static function HostPortKey ($hostPort = null)
    {
        $hostPort || $hostPort = $_SERVER['HOST'] . $_SERVER['SERVER_PORT'];
        return md5($hostPort);
    }

    /**
     * 解析网站配置
     *
     * 网站的配置数据，统一放到一数组下
     *
     *
     */
    function parseWebConfig ()
    {
        try{
            $webConf = $this->httpConf['web'];

            foreach ($webConf as $wc) {
                (isset($wc['port']) && $wc['port']) || $wc['port'] = pt_server::getArrVal('port',$this->httpConf,80);
                if ($wc['server_name']) {
                    $arr_server_name = explode(',', $wc['server_name']);
                    if ($arr_server_name) {
                        foreach ($arr_server_name as $serverName) {
                            if ($serverName) {
                                $port = self::getArrVal('port', $wc);
                                $port || $port = self::getArrVal('port', $this->httpConf);
                                //echo "serverName={$serverName},port={$port} \n";
                                $webKey                 = self::HostPortKey($serverName . ':' . $port);
                                $this->webConf[$webKey] = $wc;
                            }
                        }

                    }

                }
            }
        }catch(\Exception $e){
            $this->access_log($e->getMessage());
        }

    }

    function getHttpServer(){
        return self::$PTHttpServer;
    }
    /**
     * web server access log
     */
    function access_log ()
    {
        $args    = func_get_args();
        if(self::$PTHttpServer){
            self::$PTHttpServer->task($args);
        }else{
            throw new \Exception( '服务创建失败' );
        }
        unset($args);
    }


    static function microtime_float ()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 获取所有web配置数据
     *
     * @return bool|null
     */
    function getAllWebConf ()
    {
        if ($this->httpConf) {
            return self::getArrVal('web', $this->httpConf);
        }

        return FALSE;
    }

    function getMemory($size=null)
    {
        $size || $size = memory_get_usage();
        $unit=array('b','kb','mb','gb','tb','pb');
        return round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * Fatal Error的捕获
     *
     * @codeCoverageIgnore
     */
    public function handleFatal ()
    {
        $error = error_get_last();
        if (!isset($error['type'])) return;
        switch ($error['type']) {
            case E_ERROR :
            case E_PARSE :
            case E_DEPRECATED:
            case E_CORE_ERROR :
            case E_COMPILE_ERROR :
                break;
            default:
                return;
        }
        $message = $error['message'];
        $file    = $error['file'];
        $line    = $error['line'];
        $log     = "\n异常提示：$message ($file:$line)\nStack trace:\n";
        $trace   = debug_backtrace(1);


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
            if (isset($t['object']) && is_object($t['object'])) {
                $log .= get_class($t['object']) . '->';
            }
            $log .= "{$t['function']}()\n";
        }
        if (isset($_SERVER['REQUEST_URI'])) {
            $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
        }
        $this->access_log($log);
        if($this->response){
            $this->response->status( 500 );
            $this->response->end( '程序异常' );
        }

        unset($this->response);
    }
    /**
     * 获取数组中的值
     *
     * @param      $key
     * @param      $arr
     * @param null $default
     * @return null
     */
    static function getArrVal ($key, $arr, $default = null)
    {
        $index = strpos($key, '.');
        $last  = null;
        if ($index === FALSE) {
            return !empty($arr[$key])?$arr[$key]:false;
        } else {
            $arg  = substr($key, 0, $index);
            $last = substr($key, $index + 1, strlen($key));
        }
        if (isset($arr[$arg])) {
            if ($last && is_array($arr[$arg])) {
                return self::getArrVal($last, $arr[$arg], $default);
            } else {
                return  $arr[$arg];
            }
        }
        unset($index, $last, $arg, $key);
        return $default;

    }

    /**
     * 增加网站的监听端口
     *
     * 如果网站配置设置的端口与http配置默认的
     */
    function addWebPort ()
    {
        $allWebConf = self::getAllWebConf();
        foreach ($allWebConf as $conf) {
            $web_port      = self::getArrVal('port', $conf);
            $web_listen_ip = self::getArrVal('listen_ip', $conf);
            if ($web_port && self::$default_listen_ip != $web_listen_ip && $web_port != self::$default_port) {
                //echo 'listen_ip='.$web_listen_ip.';port='.$web_port;
                self::$PTHttpServer->addlistener($web_listen_ip, $web_port, SWOOLE_TCP);
            } else if ($web_port && $web_port != self::$default_port) {
                self::$PTHttpServer->addlistener(self::$default_listen_ip, $web_port, SWOOLE_TCP);
            }
        }

    }
    /**
     * 设置工作进程
     *
     * @param $num
     */
    function setWorkerNum ($num)
    {
        $this->setHttpConf('worker_num', $num);
    }

    /**
     * 保存用户自定义配置数据
     *
     * @param $data
     * @return bool
     */
    function saveHttpConf ($data)
    {
        return \lib\web::writeHttpConfig($data);
    }

    /**
     * 设置服务器配置
     *
     * @param $key
     * @param $vale
     */
    function setHttpConf ($key, $vale)
    {
        $this->swoole_http_config[$key] = $vale;
    }


    /**
     * 当前网站配置
     *
     * @param $hostPort
     * @return bool
     */
    function getCurrentWebConf ($hostPort)
    {
        $key = $this->HostPortKey($hostPort);
        if (!empty($this->webConf[$key])) {
            $config = $this->webConf[$key];
            $redirectKey = 'redirect';
            if(!empty($config[$redirectKey])){
                foreach ( $config[$redirectKey] as $key => $val ) {
                    $config[$redirectKey][md5( $key )] = $val;
                    unset($config[$redirectKey][$key]);
                }
            }
            return $config;
        } else {
            $this->ExceptionLog( '获取网站配置失败 hostPort='.$hostPort.' ;key='.$key,$this->webConf );
            return FALSE;
        }
    }

    /**
     * 获取域名
     *
     * @param \swoole_http_request $request
     *
     * @return bool|string
     */
    function getHost(swoole_http_request &$request)
    {
        $host=self::getArrVal('host',$request->header);
        if($host){
            $has = strpos( $host , ':' );
            if($has){
                $host=substr($host,0,$has);
                return $host;
            }else{
                return $host;
            }
        }
        $this->ExceptionLog('获取域名与端口失败',$request);
        return false;
    }

    /**
     * 获取服务端口
     *
     * @param \swoole_http_request $request
     *
     * @return null
     */
    function getServerPort( \swoole_http_request $request )
    {
        return self::getArrVal( 'server_port' , $request->server,80);
    }
    /**
     * 异常日志
     *
     */
    function ExceptionLog(  )
    {
        $argsArr = func_get_args();
        foreach ( $argsArr as $val ) {
            $this->access_log('异常：'.print_r($val,true),debug_backtrace(2));
        }
        unset($argsArr);
    }

    /**
     * 301 跳转
     * @param string $url 跳转目标url
     * @param \swoole_http_response $response
     *
     * @return bool
     */
    function redirect($url,swoole_http_response $response){
        if($url){
            $response->header( 'location' , $url );
            $response->status( 301 );
            $response->end('');
            return true;
        }

        return false;
    }

    /**
     * 请求处理
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return bool
     */
    function request( \swoole_http_request $request , \swoole_http_response $response )
    {
        unset($_SERVER);
        unset($_COOKIE);
        $start_time = self::microtime_float();
        try{
            $this->response = $response;
            $this->access_log('fd='.$response->fd);
            $m_st = memory_get_usage();
            $domain=$this->getHost( $request );
            $hostPort = $domain . ':' . $this->getServerPort( $request );
            $webConfig=$this->getCurrentWebConf($hostPort);

            $redirect = pt_server::getArrVal( 'redirect' , $webConfig );
            $redirectUrl = pt_server::getArrVal( md5($domain ), $redirect );
            if( $this->redirect( $redirectUrl , $response )){
                return true;
            }
            $server = ptServer::instance();
            $server->httpServer=$this;

            $server->setRequestInfo( $request , $response );
            $server->saveAccessInfo();

            if(!$hostPort){
                $this->ExceptionLog( 'hostPort 为空' );
                $this->responseErrCode($response,500);
                return false;
            }
            if($server->setWebConfig($webConfig)){
                unset($webConfig);
                $server->runTo();
            }else{
                //获取网站配置失败
                $this->responseErrCode( $response ,500, 'Get website configuration failed');
            }

            $ent_time=self::microtime_float();
            $this->access_log('执行时间 '.($ent_time-$start_time).' ms');
            $this->access_log(' 耗内存：'.$this->getMemory());
            if($this->getArrVal('exit_process',$server->webConfig)){
                exit;
            }
            unset($server);
            unset($request,$response);

            $this->access_log( '新增内存：'.(memory_get_usage()-$m_st).'  最后：'.memory_get_usage());
            unset($m_st);
            unset($this->response);
            $maxMemory = intval($this->getArrVal('max_memory', $this->httpConf,5));
            if($maxMemory){
                $maxMemory=$maxMemory*1048576;
                if(memory_get_usage()>$maxMemory){
                    $this->access_log('process exit ,进程内存超出限制：');
                    exit;
                };
            }

        }catch(Exception $e){
            $this->ExceptionLog( '执行异常'.$e->getMessage());
            $this->responseErrCode($response,500);
        }

    }
    /**
     * 响应错误代码
     *
     * @param $response
     * @param $code
     * @param $data
     */
    function responseErrCode (\swoole_http_response $response,$code, $data = null)
    {
        if (!is_integer($code) || !array_key_exists($code,HTTPERROR::$HttpStatus)) {
            $code = 501;
        }
        $content = '<H1>' . HTTPERROR::$HttpStatus[$code] . '</H1> ';
        if ($data) {
            $content .= $data;
        }
        $content .= '<hr> brower by PTwebserver v' . PT_WEB_VERSION;
        $response->status( $code );
        $response->end($content);
    }


    /**
     * 连接事件
     *
     * @param $server
     * @param $fd
     */
    function onConnect ($server, $fd)
    {
        $this->chkSwooleTable(PTHttpServer::$ConnectNameKey);
    }

    function onStart($server)
    {
        file_put_contents(self::$pidFile, $server->master_pid);
    }

    /**
     * 连接关闭
     * @param $server
     * @param $fd
     */
    function onClose ($server,$fd)
    {
        $this->access_log('closed fd = '.$fd);
        $this->chkSwooleTable(PTHttpServer::$ConnectNameKey,'close');
    }


    public $T_token = array();


    /**
     * worker 进程启动
     * @param $server
     * @param $worker_id
     */
    function onWorkerStart ($server,$worker_id)
    {
        cli_set_process_title(self::$BaseProcessName . 'worker');
    }

    /**
     * 服务关闭事件
     */
    function onShutdown()
    {
        file_put_contents(self::$pidFile, '');
        echo '服务器关闭'.PHP_EOL;
    }

    /**
     * worker 进程结束
     * @param $server
     * @param $worker_id
     */
    function onWorkerStop ($server, $worker_id)
    {
        //这里不能再写日志，因为在关闭时有警告信息
        //$this->access_log('process stop , worker_id = '.$worker_id);
    }

    /**
     * 工作进程异常错误处理
     * @param \swoole_server $serv
     * @param                $worker_id
     * @param                $worker_pid
     * @param                $exit_code
     */
    function onWorkerError(swoole_server $serv, $worker_id, $worker_pid, $exit_code)
    {
        $this->access_log('worker_id = '.$worker_id.'异常错误，pid='.$worker_pid.'; exit_code='.$exit_code);
    }
    /**
     * 对内存表的链接与请求数的增减操作
     * @param        $key
     * @param string $type
     */
    function chkSwooleTable ($key,$type='connect')
    {
        try{
            if ( !empty(pt_server::$PTHttpServer) ) {
                $arr=pt_server::$PTHttpServer->sw_table->get($key);
            }
            if($type=='connect'){
                $arr[$key]++;
            }else if($type=='close'){
                $arr[$key]--;
            }

            $this->access_log($key.' Number='.$arr[$key]);
            if ( !empty(pt_server::$PTHttpServer) ) {
                pt_server::$PTHttpServer->sw_table->lock();
                pt_server::$PTHttpServer->sw_table->set($key, $arr);
                pt_server::$PTHttpServer->sw_table->unlock();
            }
        }catch(Exception $e){

        }

    }

    /**
     * 运行
     */
    function run ()
    {
        cli_set_process_title(self::$BaseProcessName . 'Master');

        register_shutdown_function(array($this, 'handleFatal'));

        if ($this->LoadHttpConf(TRUE)) {

            $listen_ip = self::getArrVal('listen_ip', $this->httpConf);
            if ($listen_ip) {
                self::$default_listen_ip = $listen_ip;
            } else {
                $listen_ip = self::$default_listen_ip;
            }
            $port = self::getArrVal('port', $this->httpConf);
            if ($port) {
                self::$default_port = $port;
            } else {
                $port = self::$default_port;
            }

            $port or $port = 80;//默认是80端口
            self::$PTHttpServer = new PTHttpServer($listen_ip, $port);
            //self::$PTHttpServer->addlistener($listen_ip,self::$admin_port,SWOOLE_TCP);
            //检测网站的端口
            $this->addWebPort();

            //工作进程数量
            $this->setWorkerNum(self::getArrVal('worker_process', $this->httpConf, 8));

            self::$PTHttpServer->setHttpConf($this->httpConf);
            $this->setHttpConf('task_worker_num', self::getArrVal('task_worker_num',$this->httpConf,4));
            $this->setHttpConf('log_file', self::getArrVal('log_file',$this->httpConf,'/tmp/swoole.log'));
            $this->setHttpConf('daemonize', self::getArrVal('daemonize',$this->httpConf,false));
            $this->setHttpConf('server','ptserver');

            self::$PTHttpServer->set($this->swoole_http_config);
            //回调事件
            self::$PTHttpServer->on('request', function (\swoole_http_request $request, \swoole_http_response $response) {
                $this->request($request, $response);
            });

            self::$PTHttpServer->on('ManagerStart', function () {
                cli_set_process_title(self::$BaseProcessName . 'Manager');
            });
            self::$PTHttpServer->on('Task', function (\swoole_server $serv, $task_id, $from_id, $data) {

                $args    = $data;
                $logPath = pt_server::$logPath;
                $file    = $logPath . date('Y-m-d');
                if (!is_dir($logPath)) {
                    mkdir($logPath, 0755, TRUE);
                }

                $content = date('Y-m-d H:i:s') . ' worker_id='.$from_id.': ';
                if (is_string($args)) {
                    $content .= $args . "\n";
                } else {
                    foreach ($args as $arg) {
                        $content .= print_r($arg, TRUE) . "\n";
                    }
                }
                $fh = fopen($file, 'a+');
                if ($fh) {
                    fwrite($fh, $content . "\r\n");
                    fclose($fh);
                }
                unset($content);

            });
            self::$PTHttpServer->on('Finish', function (\swoole_server $serv, $task_id, $data) {
                //echo 'onFinish2 task_id' . $task_id;
                unset($data);
            });

            self::$PTHttpServer->on('Connect', array($this, 'onConnect'));
            self::$PTHttpServer->on('Close', array($this, 'onClose'));
            self::$PTHttpServer->on('Start', array($this, 'onStart'));
            self::$PTHttpServer->on('WorkerStart', array($this, 'onWorkerStart'));
            self::$PTHttpServer->on('WorkerStop', array($this, 'onWorkerStop'));
            self::$PTHttpServer->on('WorkerError', array($this, 'onWorkerError'));
            self::$PTHttpServer->on('Shutdown', array($this, 'onShutdown'));
            self::$PTHttpServer->start();
        } else {
            echo 'load config file fail';
        }
    }

}

date_default_timezone_set('PRC');


$server = new pt_server();
$server->run();