<?php
include 'HTTPERROR.php';
final class ptServer {

    private static $instance=null;
    /**
     * @var \swoole_http_request
     */
    public $request=null;
    /**
     * @var \swoole_http_response
     */
    public $response=null;
    /**
     * @var pt_server
     */
    public $httpServer=null;
    /**
     * 网站配置数据
     *
     * @var array
     */
    public $webConfig=[];
    /**
     * 请求的文件名
     * @var string
     */
    private $requestFile='';
    /**
     * 网站根目录
     * @var string
     */
    private $document_root='';
    /**
     * 加载的文件
     *
     * @var string
     */
    private $loadFile = '';
    /**
     * index文件
     * @var string
     */
    private $access_index = 'index.php';
    /**
     * 请求的文件类型
     */
    static $requestFileType;

    static $server_key = 'pt_http_server';

    /**
     * 会话名称
     *
     * @var string
     */
    static $session_name = 'PHPSESSID';
    /**
     * 链接信息
     *
     * @var string
     */
    private $ConnectInfo='';

    public $session_id='';
    //实例化对象
    static function instance()
    {
        if(is_null(self::$instance)){
            self::$instance=new self();
        }

        return clone self::$instance;
    }

    function __construct(){

    }

    /**
     * 设置网站配置数据
     * @param $config
     *
     * @return bool
     * @throws \Exception
     */
    function setWebConfig( $config )
    {
        if ( !empty($config) ) {
            $this->webConfig = $config;
            return true;
        }else{
            $this->httpServer->ExceptionLog( '配置为空' );
            return false;
            //throw new Exception( '配置为空' );
        }
    }
    /**
     * 获取网站根目录
     *
     * @return string
     */
    function getDocumentRoot ()
    {
        try{
            $document_root='';
            if ($this->webConfig) {
                $document_root= pt_server::getArrVal('document_root',$this->webConfig);
                $document_root || $document_root=pt_server::getArrVal( 'HTTP_DOCUMENT_ROOT' , $_SERVER );
            } else if (empty($_SERVER['HTTP_DOCUMENT_ROOT'])) {
                //默认网站根目录
                $document_root= __DIR__ . '/html';
            }
            //去掉最后 '/'
            if(substr($document_root,-1,1)=='/'){
                $document_root =substr($document_root,0,-1);
            }
            $this->document_root=$document_root;
            $_SERVER['HTTP_DOCUMENT_ROOT'] = $this->document_root;
            return $document_root;
        }catch(\Exception $e){
            $this->httpServer->ExceptionLog( $e->getMessage() );
        }

    }


    /**
     * 检测被拒绝访问的文件类型，
     *
     * @return bool
     */
    function chk_access_denied ($type)
    {
        $access_denied = pt_server::getArrVal( 'access_denied' , $this->webConfig );
        if (!empty($access_denied)) {
            $deniedList = explode(' ', $access_denied);
            if ($deniedList && in_array($type, $deniedList)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * cookie 设置
     * @return bool
     */
    function parseCookie ()
    {
        $request=$this->request;
        if(!empty($request->cookie)){
            $_COOKIE = $request->cookie;
            return true;
        }
        return false;
    }

    /**
     * 本页url
     *
     * @return string
     */
    function getSelfUrl ()
    {

        $url=pt_server::getArrVal( 'host' , $this->request->header ).pt_server::getArrVal('request_uri',$this->request->server);
        $query_string = pt_server::getArrVal( 'query_string' , $this->request->server );
        $query_string && $url.='?'.$query_string;
        return $url;
    }
    /**
     * 响应错误代码
     *
     * @param $code
     * @param $data
     */
    function responseErrCode ($code, $data = null)
    {
        if (!is_integer($code) || !array_key_exists($code,HTTPERROR::$HttpStatus)) {
            $code = 501;
        }
        $content = '<H1>' . HTTPERROR::$HttpStatus[$code] . '</H1> ';
        if ($data) {
            $content .= $data;
        }
        $content .= '<hr> brower by PTwebserver v' . PT_WEB_VERSION;
        $this->send($content, $code);
    }

    function setCookie( $key , $val , $expires = 0 , $path = '/',$domain='' )
    {
        if(!$key) return false;
        $_COOKIE[$key]=$val;
        $this->response->cookie($key, $val,$expires,$path,$domain);
    }

    public $sessionExpire=0;
    public $sessionSavePath='session/';
    /**
     * 获取会话数据
     * @param $SessionKey
     * @return array
     */
    function getSession($SessionKey)
    {
        $file=$this->getSessionPath().$SessionKey;
        //$sessionData=pt_server::$PTHttpServer->sessionTable->get($SessionKey);
        if(!is_file($file)){
            return false;
        }
        $sessionData=file_get_contents($file);
        if($sessionData){
            $sessionData = json_decode($sessionData, true);
        }
        if($this->sessionExpire){
            $lastTime = pt_server::getArrVal('time',$sessionData);
            if((time()-$lastTime)>$this->sessionExpire){
                $this->delSession($SessionKey);
                return false;
            }
        }
        $data = pt_server::getArrVal(PTHttpServer::$sessionKey, $sessionData);
        if($data){
            return $data;
        }
        return false;
    }

    function delSession($SessionKey)
    {
        if($SessionKey){
            $file=$this->sessionSavePath.$SessionKey;
            //pt_server::$PTHttpServer->sessionTable->del($SessionKey);
            if(is_file($file)){
                unlink($file);
            }
        }
    }
    /**
     * 开始会话
     */
    function sessionStart ()
    {
        $this->sessionExpire= pt_server::getArrVal('session_expire', $this->webConfig,0);
        //session 过期时间session_expire
        !empty($_COOKIE) && $this->session_id= pt_server::getArrVal(self::$session_name, $_COOKIE);

        if(empty($this->session_id)){
            $this->session_id=md5(time().uniqid());
            $this->setCookie(self::$session_name, $this->session_id);
        }
        $_SESSION = $this->getSession($this->session_id);
    }

    /**
     * 保存会话
     */
    function saveSession(){

        $sessionData[PTHttpServer::$sessionKey] = $_SESSION;
        $sessionData['time']=time();
        $path=$this->getSessionPath();
        //pt_server::$PTHttpServer->sessionTable->set($this->session_id, $sessionData);
        $file=$path.$this->session_id;
        //$sessionData=pt_server::$PTHttpServer->sessionTable->get($SessionKey);
        if(!is_dir($path))mkdir($path, '755', true);
        file_put_contents($file,json_encode($sessionData));

    }

    /**
     * session 路径
     * @return string
     */
    function getSessionPath()
    {
        return dirname(__FILE__).'/'.$this->sessionSavePath;
    }

    /**
     * 执行php程序
     *
     * @param $loadFileName
     */
    function excPHP ($loadFileName)
    {
        $this->sessionStart();
        $this->setNoCacheControl( );

        if (file_exists($loadFileName)) {
            try {
                ob_start();
                require $loadFileName;
                $content = ob_get_contents();
                ob_end_clean();
                $cookie = $this->getServerCookie();
                if($cookie){
                    foreach ($cookie as $key=>$val) {
                        $this->setCookie($key, $val[$key],$val['expires'],$val['path'],$val['domain']);
                    }
                }
                //输出内容类型处理，1用户自定义的类型优先;2 accept 指定的类型 ;3 系统默认
                $this->send($content);
                $this->saveSession();
            } catch (\Exception $e) {
                ob_end_clean();
                $this->responseErrCode(500,$e->getMessage());
                $this->saveSession();
            }
        } else {
            $this->httpServer->access_log('url:' . $this->getSelfUrl() . '; 文件不存在:' . $loadFileName);
            $this->responseErrCode(404);
        }

    }

    /**
     *
     * @param $request
     * @param $response
     */
    function setRequestInfo( $request , $response )
    {
            $this->request = $request;
            $this->response = $response;
    }
    /**
     * server变量解析
     */
    function parseServer ()
    {
        try{
            $request = $this->request;
            if($this->request){
                $_SERVER = array_merge($request->server, $request->header);
                foreach ($_SERVER as $key => $val) {
                    $_SERVER[strtoupper($key)] = $val;
                    unset($_SERVER[$key]);
                }
                $_SERVER['PHP_SELF']=$this->getSelfUrl();
            }else{
                $this->httpServer->access_log('request对象为空');
            }
        }catch(\Exception $e){
            $this->httpServer->ExceptionLog( $e->getMessage() );
        }
    }


    /**
     * 请求文件
     * @return null
     */
    function chkRequestFile()
    {
        $this->requestFile=pt_server::getArrVal( 'request_uri' , $this->request->server );
        //过虑 './' ，以防安全
        $this->requestFile = str_replace('../', '', $this->requestFile);
    }

    /**
     * 获取加载文件名
     *
     * @return string
     */
    function getLoadFile()
    {
        $this->loadFile=$this->document_root.$this->requestFile;
        return $this->loadFile;
    }
    /**
     * 文件后缀
     *
     * @param $file
     * @return bool|string
     */
    function getFileExf (&$file)
    {
        $indexN = strrchr($file, '.');
        $type   = FALSE;
        if ($indexN) {
            $type = substr($indexN, 1);
        }

        return $type;
    }


    /**
     * 设置文件内容类型
     *
     * @param string $type 类型
     * @return bool
     */
    function setContentType ($type='')
    {
        try{
            if($this->response){
                if($type){
                    $type = pt_server::getArrVal($type, HTTPERROR::$contentType);
                    if($type ){
                        $this->response->header('Content-Type', $type.';charset=utf-8');
                        return true;
                    }
                }
            }else{
                $this->httpServer->access_log('response 为空');
            }
            return false;

        }catch(\Exception $e){
            $this->httpServer->ExceptionLog('setContentType 异常');
        }

    }

    /**
     * 请求方法
     */
    function getRequestMethod()
    {
        return pt_server::getArrVal( 'request_method' , $this->request->server );
    }

    function ConnectInfo()
    {
        $this->ConnectInfo = pt_server::$PTHttpServer->connection_info( $this->request->fd );
    }

    /**
     * 记录访问信息
     */
    function saveAccessInfo()
    {
        $this->ConnectInfo();
        $logInfo='['.$this->getRequestMethod().'] '.$this->getSelfUrl().',  来源IP='.pt_server::getArrVal('remote_ip',$this->ConnectInfo).':'.pt_server::getArrVal('remote_port',$this->ConnectInfo);
        if($this->getRequestMethod()=='POST'){
            $logInfo .= print_r( $this->request->header , true );
        }
        $this->httpServer->access_log($logInfo);
        unset($logInfo);
    }

    /**
     * 类型检测
     * @param $accept_str
     * @return bool
     */
    function chkContentTypeByAccept($accept_str)
    {
        if($accept_str){
            $arr = explode(',', $accept_str);
            if(!empty($arr[0])){
                $arr2 = explode('/', $arr[0]);
                if(!empty($arr2[1])){
                    return $this->setContentType($arr2[1]);
                }
            }
        }
        return false;
    }
    /**
     * 执行网站程序
     */
    function runTo(){

        $this->parseServer();
        $this->parseCookie();
        $this->getDocumentRoot();
        //pt_server::$PTHttpServer->set(array('chroot' => '/data/server/'));
        //chroot($this->document_root);
        $this->chkRequestFile();
        //$this->httpServer->access_log($this->request);
        if(!empty($this->request->post)){
            $_POST = $this->request->post;
        }
        if(!empty($this->request->get)){
            $_GET = $this->request->get;
        }
        if(!empty($this->request->files)){
            $_FILES=$this->request->files;
        }

        //echo 'requestFile='.$this->requestFile.PHP_EOL;
        $loadFile = $this->getLoadFile();
        //echo 'loadFile='.$loadFile.PHP_EOL;
        //以accept 优先检测 content-type 类型
        $this->chkContentTypeByAccept(pt_server::getArrVal('accept', $this->request->header));
        if(is_file($loadFile) && file_exists($loadFile)){
            //检测被拒绝访问的文件类型
            if($this->chk_access_denied($this->getFileExf($loadFile))){
                $this->responseErrCode( 403 );
            }else{
                //检测是不是可执行文件
                if($this->chkPHPMap($this->loadFile)){
                    //$this->httpServer->access_log('可执行文件：'.$this->loadFile);
                    $this->excPHP( $this->loadFile );
                }else{

                    $lastUploadTime = filemtime( $this->loadFile );
                    $fileType = $this->getFileExf( $this->loadFile );
                    if($fileType){
                        $this->setContentType($fileType);
                    }
                    $ifModifiedSince = pt_server::getArrVal( 'if-modified-since' , $this->request->header );
                    $IsCache = pt_server::getArrVal( 'is_cache' , $this->webConfig );
                    $IsCache && $CacheArr = pt_server::getArrVal( 'cache' , $this->webConfig );
                    if($IsCache && $ifModifiedSince && $CacheArr && in_array($fileType,$CacheArr['type']) && $lastUploadTime<=$ifModifiedSince){
                        $this->httpServer->access_log('出缓存：'.$this->loadFile);
                        $content='';
                        $this->send($content,304);
                    }else{
                        $this->CacheBrowse( $fileType,$lastUploadTime );
                        $this->httpServer->access_log('静态文件：'.$this->loadFile);
                        $fileContent = file_get_contents( $this->loadFile );
                        $this->send($fileContent);
                    }
                }
            }
        }else{
            //重写

            if(!$this->rewrite( $this->loadFile )){
                $this->httpServer->access_log('重写无匹配');
                $this->responseErrCode( 404 );
            }else{
                $this->httpServer->access_log('重写成功');
            }
        }

    }

    /**
     * 设置浏览器缓存
     *
     * @param $FileType
     */
    function CacheBrowse( $FileType ,$lastTime)
    {
        if($FileType){
            $CacheArr = pt_server::getArrVal( 'cache' , $this->webConfig );
            if($CacheArr){
                    if(in_array($FileType,$CacheArr['type'])){
                        //$this->response->header( 'Expires' , $this->getGMTTime($CacheArr['time']) );
                        $this->response->header( 'Last-Modified' , $lastTime);
                        $this->setCacheControl($CacheArr['time'],'must-revalidate');
                    }
            }
        }
    }

    /**
     *
     */
    function setNoCacheControl()
    {
        $this->response->header('cache-control','no-cache,no-store, pre-check=0, post-check=0');
    }

    /**
     * 浏览器缓存控制设置
     *
     * @param        $time
     * @param string $option 相关配置项
     */
    function setCacheControl( $time , $option = '' )
    {
        !empty($option) && $option = ', ' . $option;
        $this->response->header( 'Cache-Control' , 'max-age='.$time.$option);

    }

    /**
     * 获取 GMT 模式时间
     * @param $time
     *
     * @return string
     */
    function getGMTTime($time=0){
        return gmdate('l, d F Y H:i:s',time()+$time).' GMT';
    }
    /**
     * 检测执行文件类型
     *
     * @param $file
     * @return bool
     */
    function chkPHPMap (&$file)
    {
        $key = 'php_map';
        if (!empty($this->webConfig[$key])) {
            $map = $this->webConfig[$key];
        } else if (!empty($this->webConfig[$key])) {
            $map = $this->webConfig[$key];
        } else {
            $map = 'php';//默认
        }
        $type = $this->getFileExf($file);
        //修改成php后缀文件
        if ($type && $map == $type) {
            $grep = "^(.*?)\.{$type}$";
            $file = preg_replace("/{$grep}/i", '$1.php', $file);
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 获取目录index文件
     * @param string $path
     * @return string
     */
    public function getAccessIndex ($path = '')
    {
        try{
            $access_index = empty($this->webConfig['index']) ? $this->access_index :$this->webConfig['index'];
            $arr          = explode(' ', $access_index);
            if ($arr) {
                foreach ($arr as $file) {
                    $indexFile =$path.$file;
                    if (file_exists($indexFile)) {
                        return $indexFile;
                    }
                }
                $indexFile =$path. $arr[0];
                return $indexFile;
            }
            return 'index.php';
        }catch(\Exception $e){
            $this->httpServer->ExceptionLog($e->getMessage());
        }

    }
    /**
     * 获取请求的php文件名
     *  针对 index.php/a/ba/这类的，提取 index.php 文件名出来
     * @param $file
     * @return string
     */
    function getRequestFileName (&$document_root,$file)
    {
        $request_fileName = $_SERVER['REQUEST_URI'];
        $returnFile = '';
        if (is_file($file)) {
            $returnFile = $file;
        } else if (is_dir($file)) {
            $returnFile = $this->getAccessIndex($file);
        } else if ($request_fileName) {
            preg_match_all('/^(.*?\.\w+)[\?\/]?.*?$/i', $request_fileName, $math);
            if (!empty($math[1])) {
                $this->httpServer->access_log('---- ');
                $this->httpServer->access_log($math);
                $file       = $math[1][0];
                $returnFile = $document_root . $file;
            } else {
                $returnFile = $this->getAccessIndex($file);
            }
        }
        self::$requestFileType = $this->getFileExf($returnFile);

        return $returnFile;
    }
    /**
     * 重写
     * @param $loadFileName
     * @return bool
     */
    function rewrite ($loadFileName)
    {
        //重写功能,如果文件不存在，则进入重写，
        //原理，匹配的结果，再赋予 $_SERVER['PATH_INFO']

        if (!empty($this->webConfig['rewrite']) && $this->webConfig['rewrite'] && !empty($this->webConfig['rewrite_route'])) {

            foreach ($this->webConfig['rewrite_route'] as $grep => $route) {
                //$this->httpServer->access_log('重写：'.$grep.'=>'.$route);
                $this->httpServer->access_log('request_uri='.$_SERVER['REQUEST_URI']);
                if (preg_match($grep, $_SERVER['REQUEST_URI'])) {
                    if ($new_info = preg_replace("$grep", trim($route), trim($_SERVER['REQUEST_URI']))) {
                        $_SERVER['REQUEST_URI']     = $new_info;
                        $this->loadFile = $this->getRequestFileName($this->document_root,$loadFileName);
                        $this->httpServer->access_log($this->loadFile);
                        $arr          = parse_url($new_info);
                        parse_str( $arr['query'] ,$P_arr);
                        $_SERVER['U'] = $P_arr['U'];
                        unset($P_arr['U']);
                        empty($_GET) && $_GET = [];
                        $_GET = array_merge( $_GET , $P_arr );
                        $this->httpServer->access_log('重写：loadFile = ' . $this->loadFile);
                        $this->httpServer->access_log('newFile= ' . $new_info);

                        if ($this->chkPHPMap($this->loadFile)) {
                            //   print_r(PTHttpServer::$loadFileName);
                            $this->httpServer->access_log('rewrite success to file :' . $this->loadFile);
                            $this->excPHP($this->loadFile);
                            return TRUE;
                        }
                    }
                }else{
                    //$this->httpServer->access_log('没有配置');
                }
            }
        }

        return FALSE;
    }

    /**
     * 获取APP 设定的 http header 信息
     * @return array
     */
    function getServerHeader()
    {
        return pt_server::getArrVal( self::$server_key.'.header' , $_SERVER );
    }
    /**
     * 获取APP 设定的 http cookie数组
     * @return array
     */
    function getServerCookie()
    {
        return pt_server::getArrVal( self::$server_key.'.cookie' , $_SERVER );
    }
    /**
     * 发送内容
     *
     * @param     $content
     * @param int $code
     */
    function send($content,$code=200)
    {
        try{
            $statusCode = pt_server::getArrVal( 'StatusCode' , $_SERVER );
            if($statusCode){
                $this->response->status( $statusCode );
            }else{
                $this->response->status( $code );
            }
            //$this->httpServer->access_log('statusCode='.($statusCode?$statusCode:$code));
            $pt_http_server_header = pt_server::getArrVal( 'pt_http_server.header' , $_SERVER );
            $contentType = pt_server::getArrVal('Content-Type', $pt_http_server_header);
            //如果设置contentType,则以这为准
            if($contentType){
                $this->setContentType($contentType);
                if(!empty($http_response_header['Content-Type']))unset($http_response_header['Content-Type']);
            }
            if($pt_http_server_header){
                foreach ( $pt_http_server_header as $key => $val ) {
                    $this->response->header( $key , $val );
                    unset($val);
                }
            }
            $this->response->end( $content);
            unset($content,$_SERVER,$this->response,$this->request,$pt_http_server_header);
            $_GET=null;
            $_POST=null;

        }catch(Exception $e){
            $this->response->end( $e->getMessage());
        }
    }

    function __destruct(){


    }
}