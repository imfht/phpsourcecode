<?php
/**
 * 页面简易分发器核心
 * Simple Control Handler 60 
 *
 */

namespace SCH60\Kernel;

/**
 * 配置Class
 *
 */
class Config{
    
    protected $cfg = null;
    
    public function read(){
        
        $conf = array();
        
        $sourceFiles = array(
            D_APP_DIR. '/Config/Default.php',
            D_APP_DIR. '/Config/Default_'. D_CONTROLLER_NAME. '.php',
            D_APP_DIR. '/Config/Default_'. D_CONTROLLER_NAME. '_'. D_ENV. '.php',
        );
        
        foreach($sourceFiles as $file){
        
            if(!file_exists($file)){
                continue;
            }
        
            $newConf = require $file;
            if(is_array($newConf) && !empty($newConf)){
                $conf = KernelHelper::array_merge($conf, $newConf);
            }
        }
        
        return $conf;
    }
    
    public function get($idx = null, $def = null){
        if(null === $this->cfg){
            $this->cfg = $this->read();
        }
        
        if(empty($idx)){
            return $this->cfg;
        }
        
        return isset($this->cfg[$idx]) ? $this->cfg[$idx] : $def;
    }
    
}


class StrHelper{

    static public function O($s){
        return htmlspecialchars($s);
    }
    
    static public function url($route = "", $param = null, $absolute = true){
        $url = '';
        
        if(!empty($param)){
            if(!is_array($param)){
                parse_str($param, $param_new);
                $param = $param_new;
            }
        }else{
            $param = array();
        }
        
        if(!empty($route)){
            $param = array_merge(array('r' => $route), $param);
        }
        
        if(!empty($param)){
            $url .=  '?'. http_build_query($param);
        }
        
        $url = App::$app->request->getEntryFilename(). $url;
        
        if($absolute){
            $url = App::$app->request->getBaseUrl(). '/'. $url;
        }
        
        return $url;
        
    }
    
    static public function urlStatic($str){
        return App::$app->request->getBaseUrl(). '/'. $str;
    }
    
    
    
}


class Request{
    
    protected $ip = null;
    
    protected $entryFilename = null;
    
    protected $baseUrl = null;
    
    protected $isAjax = null;
    
    /**
     * 获取一个cookies
     * @param string $name
     * @param string $usePre 是否使用pre？默认true
     * @return mixed
     */
    public function getCookie($name, $usePre = true){
        if($usePre){
            $name = App::$app->config->get('cookiePre'). $name;
        }
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }
    
    public function input($arr, $idx, $default = null){
        if(!is_array($idx)){
            return isset($arr[$idx]) ? $arr[$idx] : $default;
        }
        
        $return = array();
        foreach ($idx as $i){
            $return[$i] = isset($arr[$i]) ? $arr[$i] : $default;
        }
    
        return $return;
    }
    
    
    public function isRobot(){
    
        static $isRobot = null;
        if($isRobot !== null){
            return $isRobot;
        }
    
        if(empty($_SERVER['HTTP_USER_AGENT']) || preg_match("/bot|spider|crawl|nutch|lycos|robozilla|slurp|search|seek|archive|curl/i", $_SERVER['HTTP_USER_AGENT'])){
            $isRobot = true;
        }else{
            $isRobot = false;
        }
    
        return $isRobot;
    
    }
    
    /**
     * 获取URL入口文件名
     * @return string
     */
    public function getEntryFilename(){
        if(null === $this->entryFilename){
            $this->entryFilename = basename(D_ENTRY_FILE);
        }
        return $this->entryFilename;
    }
    
    /**
     * 获取基础URL路径
     * @return string
     */
    public function getBaseUrl(){
        if(null === $this->baseUrl){
            $this->baseUrl = ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? 'http' : 'https'). '://'. $_SERVER['HTTP_HOST']. substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
        }
    
        return $this->baseUrl;
    }
    
    /**
     * 获取用户ipv4
     * @return string
     */
    public function getIp(){
        if(null !== $this->ip){
            return $this->ip;
        }
    
        $ip = $_SERVER['REMOTE_ADDR'];
    
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
            $this->ip = $ip;
        }else{
            $this->ip = '0.0.0.0';
        }
    
        return $this->ip;
    }
    
    
    protected $is_session_started = false;
    public function session_get($idx = null, $def = null){
        if(defined('D_DISABLE_SESSION')){
            return null;
        }
        $prefix = App::$app->config->get('cookiePre', 'pre_');
        
        if(!$this->is_session_started){
            session_start();
            if(empty($_SESSION[$prefix. '_authses']) || $this->getCookie('sessionauth') != $_SESSION[$prefix. '_authses']){
                if(!empty($_SESSION)){
                    session_regenerate_id(true);
                }
                $_SESSION = array();
                $_SESSION[$prefix. '_authses'] = md5('sess_asdfasdf_'. uniqid(). "_". App::$app->config->get('hashSalt', '_____________________'));
                App::$app->response->setCookie('sessionauth', $_SESSION[$prefix. '_authses'], 0,  true, null, null,  false, true);
            }
            $this->is_session_started = true;
        }
        
        if(null == $idx){
            return $_SESSION;
        }
        $idx = $prefix. $idx;
        return isset($_SESSION[$idx]) ? $_SESSION[$idx] : $def;
    }
    
    /**
     * 是否处于ajax中？
     * @return boolean
     */
    public function isAjax(){
        if(null !== $this->isAjax){
            return $this->isAjax;
        }
    
        $this->isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        return $this->isAjax;
    }
    
}



class Response{

    public function sendResponse($code = 200){
        static $headerStatusCode = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
        );

        if("200" != $code){
            if(isset($headerStatusCode[$code])){
                $headerLine = 'HTTP/1.1 '. $code. ' '. $headerStatusCode[$code];
            }else{
                $headerLine = 'HTTP/1.1 501 '. $headerStatusCode[501];
            }
            header($headerLine);
        }
    }
    
    public function error($tip, $responseCode = 403, $redirectUrl = "", $isHtml = false){
        $this->sendResponse($responseCode);
        
        if(App::$app->request->isAjax()){
            $this->json(array(
                'error' => $tip,
                'redirectUrl' => $redirectUrl
            ), 1, $tip);
            
            exit();
        }
        
        $ctrlViewFile = D_APP_DIR. '/tpl/'. strtolower(D_CONTROLLER_NAME). '/common/error_tip.php';
        if(file_exists($ctrlViewFile)){
            require $ctrlViewFile;
        }else{
            require D_APP_DIR. '/tpl/common/error_tip.php';
        }
        
        exit();
    }
    
    public function msg($tip, $redirectUrl = "", $isHtml = false){
        if(App::$app->request->isAjax()){
            $this->json(array(
                'redirectUrl' => $redirectUrl
            ), 0);
            
            exit();
        }
        
        $ctrlViewFile = D_APP_DIR. '/tpl/'. strtolower(D_CONTROLLER_NAME). '/common/msg.php';
        if(file_exists($ctrlViewFile)){
            require $ctrlViewFile;
        }else{
            require D_APP_DIR. '/tpl/common/msg.php';
        }
        
        exit();
    }
    
    public function render($__filename, $__data = array(), $____return = false){
        if($____return){
            ob_start();
            ob_implicit_flush(false);
        }
        extract($__data, EXTR_SKIP);
        require  D_APP_DIR. '/tpl/'. strtolower(D_CONTROLLER_NAME. '/'. $__filename). '.php';
        if($____return){
            return ob_get_clean();
        }
    }
    
    public function rawJson($data){
        header('Content-Type: application/json;charset=UTF-8');
        echo json_encode($data);
        exit();
    }
    
    public function json($rst, $code = 0, $err = "", $errdetail = null){
        $str = array('rst' => $rst, 'code' => $code,);
        if($code != 0){
            $str['err'] = $err;
            $str['errdetail'] = $errdetail;
        }
        $this->rawJson($str);
    }
    
    /**
     * 设置一个cookies（注意和php不同，多了一个参数，有一个参数有更改）
     * @param string $name
     * @param string $value
     * @param int $expire 过期时间。（与PHP不同）
     *     当为正数时，表示设置该cookies并规定其在多久后过期。比如：3600表示3600秒后将过期。
     *     当为负数时，表示：设置该cookie过期并删除
     *     当为0时，表示：设置为session cookies
     * @param boolean $usePre 是否使用pre？（与PHP不同，为新增参数）默认true
     * @param string $path
     * @param string $domain
     * @param string $secure
     * @param string $httponly
     */
    public function setCookie($name, $value, $expire = 0, $usePre = true, $path = null, $domain = null, $secure = false, $httponly = false){
        if($usePre){
            $name = App::$app->config->get('cookiePre'). $name;
        }

        if($expire > 0){
            $expire = time() + $expire;
        }elseif($expire < 0){
            $value = "";
            $expire = 1;
        }

        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
    
    public function session_set($idx, $val){
        if(defined('D_DISABLE_SESSION')){
            return null;
        }
        App::$app->request->session_get('test');
        $idx = App::$app->config->get('cookiePre', 'pre_'). $idx;
        $_SESSION[$idx] = $val;
    }
    
    public function redirect($url){
        $this->sendResponse(302);
        header("Location: ". $url);
        exit();
    }
    
}


class BaseController{
    
    /**
     * 
     * @var Request
     */
    protected $request;
    
    /**
     *
     * @var Response
     */
    protected $response;
    
    protected $layout = "";
    
    public function __construct(){
        $this->request = App::$app->request;
        $this->response = App::$app->response;
    }
    
    public function beforeRunAction(){
        
    }
    
    public function __call($name, $args){
        if(0 === strpos($name, 'action')){
            $this->response->error($name. '不存在', 404);
        }
    
        throw new BadMethodCallException('Controller Method Not Found:'. $name);
    
    }
    
    public function render($filepath = null, $data = array()){
        if(empty($filepath)){
            $router = App::$app->getRouter();
            $filepath = $router['router'];
        }
        
        if(!empty($this->layout)){
            $data['content'] = $this->response->render($filepath, $data, true);
            $this->response->render($this->layout, $data);
        }else{
            $this->response->render($filepath, $data);
        }
        
    }
    
}


class KernelHelper{
    
    public static function getInstance($name){
        return App::$app->getInstance($name);
    }
    
    public static function render($file, $data = array(), $return = false){
        return App::$app->response->render($file, $data, $return);
    }
    
    public static function config($idx = null, $def = null){
        return App::$app->config->get($idx, $def);
    }
    

    /**
     * 两个数组合并，代码来自yiiframework
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public static function array_merge($a, $b){
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::array_merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
    
        return $res;
    }
    
    
}

class App{
    
    /**
     * 
     * @var App
     */
    public static $app = null;
    
    protected $loadClassPath = array();
    
    const VERSION = '1.0.1';
    
    const BUILD_VERSION = '20150925';
    
    /**
     * 
     * @var Response
     */
    public $response;
    
    /**
     * 
     * @var Request
     */
    public $request;
    
    /**
     * @var Config
     */
    public $config;
    
    /**
     * route入口。必有：subapp，controller，action
     * @var array
     */
    protected $routeEntry = array();
    
    /**
     * 实例对象树
     * @var array
    */
    protected $instanceTree;
    
    public function __construct(){
        $this->init();
    }
    
    protected function init(){
        
        if(!defined('D_APP_DIR')){
            throw new RuntimeException('NOT DEFINE D_APP_DIR');
        }
        
        date_default_timezone_set('PRC');
        
        if(!defined('D_DEBUG')){
            define('D_DEBUG', 0);
        }
        
        if(D_DEBUG > 0){
            error_reporting(E_ALL);
        }else{
            error_reporting(0);
        }
        
        if(!defined('D_CONTROLLER_NAME')){
            define('D_CONTROLLER_NAME', "Controller");
        }
        
        $this->loadClassPath['apppath'] = D_APP_DIR. '/Class';
        
        spl_autoload_register(array(
            $this,
            'loadClass'
        ));
        
        define('D_FRAMEWORK_CORE_INIT', TRUE);
        
        $this->config = new Config();
        
        $this->request = new Request();
        $this->response = new Response();
        
        self::$app = $this;
        
    }
    
    public function addLoadClassPath($name, $path){
        $this->loadClassPath[$name] = $path;
    }
    
    /**
     * 以PSR-4标准载入类
     * @param string $className
     */
    public function loadClass($className) {
        
        $className = str_replace('\\', '/', $className);
        $className = ltrim($className, '/'). '.php';
    
        foreach($this->loadClassPath as $findpath){
            $realFilepath = $findpath. DIRECTORY_SEPARATOR. $className;
            if($this->file_exists_case($realFilepath)){
                require $realFilepath;
                return ;
            }
        }
    
    }
    
    /**
     * 严格按照大小写，判断本地文件是否存在
     * 部分代码来自ThinkPHP，并进行适当裁剪
     * @param string $file
     * @return bool
     */
    public function file_exists_case($filename){
        static $iswin = null;
        if(null === $iswin){
            $iswin = 0 === stripos(PHP_OS, 'win');
        }
        if (file_exists($filename)) {
            if ($iswin && D_DEBUG){
                if (basename(realpath($filename)) != basename($filename)){
                    $realpath = realpath($filename);
                    $errorStr = 'File_name_case_sensitive_error_on_linux_emulator_for_win!';
                    $errorStr .= ' [PASS PARAM FILE basename] '. basename($filename) . ' != [REAL FILE basename] '. basename($realpath);
                    $errorStr .= ' [PASS PARAM FILE] '. $filename. ' ;[REAL FILE]'. $realpath;
                    throw new \InvalidArgumentException($errorStr);
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    
    
    public function run(){
        $this->createDispatchEnvironment();
        $this->runhook();
        $this->dispatchController();
    }
    
    
    protected function runhook(){
        $hookCfg = $this->config->get('hooks');
        if(!is_array($hookCfg)){
            return ;
        }
        
        foreach($hookCfg as $hook){
            if($hook instanceof Closure){
                $hook();
            }else{
                $this->getInstance($hook[0])->{$hook[1]}();
            }
        }
    }
    
    public function createDispatchEnvironment(){
        $r = isset($_POST['r']) ? $_POST['r'] : (isset($_GET['r']) ? $_GET['r'] : '');
        if(empty($r)){
            $r = $this->config->get('defaultRoute', 'index/index/index');
        }
        $this->routeEntry = $this->createRouterEntry($r);

        
    }
    
    public function dispatchController(){

        $controlerName = D_CONTROLLER_NAME. '\\'. $this->routeEntry['subapp']. '\\'. $this->routeEntry['controller'];
        if(!class_exists($controlerName)){
            $this->response->error($controlerName. '未定义');
        }
        
        $controller = new $controlerName();
        $controller->beforeRunAction();
        
        $actionName = 'action'. $this->routeEntry['action'];
        $controller->$actionName();
        
    }
    
    /**
     * 检查路由是否符合格式，并创建最终路由入口。格式如下：
     * subapp：仅允许第一个为字母，其余为“字母+数字+下划线”的形式。如：aa，aa0
     *     最终格式化为首字母大写的app存放文件夹路径，如：Aa，Aa0
     * controller：仅允许第一个为字母，其余为“字母+数字+下划线”的形式。如：aa，ab_c0
     *     最终格式化为首字母大写大写的controller名称，如：Aa，Ab_C0
     * action：仅允许“字母+数字+下划线”的形式。如：aa，0bc
     *     最终格式化为首字母大写的action名称。如：Aa，0bc
     * @param string $route
     * @return array 解析的RouterEntry。必有
     */
    public function createRouterEntry($route){
        if(!preg_match('/^(?P<subapp>[a-z][a-z0-9_]*)\/(?P<controller>[a-z][a-z0-9_]*)\/(?P<action>[a-z0-9_]+)$/iU', $route, $res)){
            $this->response->error("Route Validate Fail", 406);
        }
        
        $return = array();
        $return['subapp'] = ucfirst($res['subapp']);
        $return['controller'] = ucfirst($res['controller']);
        $return['action'] = ucfirst($res['action']);
        $return['router'] = $res['subapp']. '/'. $res['controller']. '/'.$res['action'];
        $return['runRouter'] = $return['subapp']. '/'. $return['controller']. '/'.$return['action'];
        
        return $return;
    
    }

    public function getRouter(){
        return $this->routeEntry;
    }
    
    /**
     * 获取一个无构造方法的单例。
     * @param string $name 名称。前后请勿添加“\”！为效率，此处不进行处理！
     * @return object Object of class $name
     */
    public function getInstance($name){
        if(isset($this->instanceTree[$name])){
            return $this->instanceTree[$name];
        }
        
        $instance = new $name();
        $this->instanceTree[$name] = $instance;
        return $instance;
    }
    
}