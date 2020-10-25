<?php
namespace Alpaca;

//Alpaca类，创建路由，调用事件，执行动作
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Alpaca
{
    /**
     * 支持JSONP
     * Whether support Jsonp . Support if true, nonsupport if false.
     */
    protected $_isJsonp = true;

    //配置文件
    public $config;

    //路由router
    public $router;

    //日志对象
    public $log;

    //单例
    private static $instance;

    //构造函数
    public function __construct(array $config = null)
    {
        $this->config = $config;
        return $this;
    }

    //单例
    public static function app(array $config = null)
    {
        return self::getInstance($config);
    }

    //创建 - 单例
    private static function getInstance(array $config = null)
    {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    //路由 单例
    public static function router()
    {
        return self::$instance->router;
    }

    //日志 单例
    public static function log()
    {
        return self::$instance->log;
    }

    //运行 alpaca
    public function run()
    {
        //加载配置文件
        $this->config = \Environment::env()->config();

        //创建日志
        $this->createLog();

        //过滤用户输入参数
        $this->paramFilter();

        //调用bootstrap
        $this->bootstrap();

        //启动路由
        Router::router()->application = $this;
        $this->router = Router::router()->init();

        //创建模块实例
        $moduleClassName = $this->router->ModuleClassName;
        $module          = new $moduleClassName();

        //创建控制器实例
        $controllerClassName = $this->router->ControllerClassName;
        $controller          = new $controllerClassName();

        //执行事件init,release
        $init    = "init";
        $release = "release";
        $result  = null;
        //执行模块中的init方法，如果该方法存在
        if (method_exists($module, $init)) {
            $result = $module->$init();
        }
        //执行控制器中的init方法，如果该方法存在
        if (method_exists($controller, $init) && $result !== false) {
            $result = $controller->$init();
        }

        //执行action方法
        $action = $this->router->ActionName;
        if (method_exists($controller, $action) && $result !== false) {
            $controller->$action();
        }

        //执行控制器中的release方法
        if (method_exists($controller, $release)) {
            $controller->$release();
        }

        //执行模块中的release方法
        if (method_exists($module, $release)) {
            $module->$release();
        }
    }

    //创建日志
    public function createLog()
    {
        if($this->config['log']){
            $this->log = new Logger('[LOG]');
            $dir = $this->config['log']['dir'].date('Ymd');
            if (!is_dir($dir)){
                @mkdir($dir, '0777');
            }
            $file = $dir.'/'.$this->config['log']['file'];

            $levelName = $this->config['log']['levels']?$this->config['log']['levels']:"INFO";
            $levelCode = 100;
            if($levelName =="INFO"){
                $levelCode = 200;
            }elseif($levelName =="ERROR"){
                $levelCode = 300;
            }
            $this->log->pushHandler(new StreamHandler($file,$levelCode));
        }
    }


    //运行 bootstrap, bootstrap中_init开头的方法回依次被执行
    public function bootstrap()
    {

        require_once APP_PATH . '/application/Bootstrap.php';

        $bootstrap = new \Bootstrap();

        $methods = get_class_methods($bootstrap);
        if (!$methods) {
            return $this;
        }

        foreach ($methods as $method) {
            if (preg_match("/(^(_init))/", $method)) {
                $bootstrap->$method();
            }
        }
        return $this;
    }

    //获取模块
    public function getModules()
    {
        if (empty($this->config['application']['modules'])) {
            return null;
        }
        return array_map("trim", explode(',', $this->config['application']['modules']));
    }

    //过滤传入参数，防止xss注入，SQL注入
    public function paramFilter()
    {
        if (isset($_GET) && !empty($_GET)) {
            $_GET = $this->filterChars($_GET);
        }
        if (isset($_POST) && !empty($_POST)) {
            $_GET = $this->filterChars($_GET);
        }
        if (isset($_REQUEST) && !empty($_REQUEST)) {
            $_GET = $this->filterChars($_GET);
        }
        if (isset($_COOKIE) && !empty($_COOKIE)) {
            $_GET = $this->filterChars($_GET);
        }
    }

    /**
     * Strips slashes from input data.
     * This method is applied when magic quotes is enabled.
     * @param mixed $data input data to be processed
     * @return mixed processed data
     */
    public function filterChars(&$data)
    {
        return is_array($data) ? array_map(array($this, 'filterChars'), $data) : addslashes(htmlspecialchars($data));
    }

    //获取输入参数 get，post
    public function getParam($name, $defaultValue = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
    }

    //获取输入参数 get
    public function getQuery($name, $defaultValue = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
    }

    //获取输入参数 post
    public function getPost($name, $defaultValue = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
    }

    /**
     * 返回json
     * @author Chengcheng
     * @date 2016年10月21日 17:04:44
     * @param array $jsonData
     * @return boolean
     */
    public function toJson($jsonData)
    {
        header('Content-Type: application/json;charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        if ($this->_isJsonp) {
            //JSONP格式-支持跨域
            $cb = isset($_GET['callback']) ? $_GET['callback'] : null;
            if ($cb) {
                $result = "{$cb}(" . json_encode($jsonData, JSON_UNESCAPED_UNICODE) . ")";
            } else {
                $result = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
            }
        } else {
            //JSON格式-普通
            $result = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
        }

        //打印结果
        echo $result;

        return $result;
    }
}
