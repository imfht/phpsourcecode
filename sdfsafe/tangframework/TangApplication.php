<?php
namespace Tang;
use Tang\Event\IEvent;
use Tang\Exception\SystemException;
use Tang\Services\EventService;
use Tang\Services\I18nService;
use Tang\Services\RequestService;
use Tang\Services\ConfigService;
use Closure;
use Tang\Web\Controllers\Controller;

/**
 * 自动加载类
 * @author 吉兵
 *
 */
class Autoload
{
    private static $applicationDirectory;

    /**
     * 设置应用根目录
     * @param $directory
     */
    public static function setApplicationDirectory($directory)
    {
        static::$applicationDirectory = $directory;
    }

    /**
     * 加载框架里面的类
     * @param $class
     * @return bool
     */
    public static function loadFramework($class)
	{
		$classFile =__DIR__.DIRECTORY_SEPARATOR.$class. '.php';
		return static::load($classFile);
	}

    /**
     * 加载应用里面的类
     * @param $class
     * @return bool
     */
    public static function loadApplication($class)
	{
		$classFile = $class. '.php';
		return static::load(static::$applicationDirectory.$classFile);
	}

    /**
     * 加载类文件
     * @param $classFile
     * @return bool
     */
    private static function load($classFile)
	{
		$classFile = str_replace('\\', DIRECTORY_SEPARATOR, $classFile);
		if(!$classFile || !file_exists($classFile))
		{
			return false;
		} else
		{
            include $classFile;
			return true;
		}
	}
}
spl_autoload_register(array('Tang\Autoload','loadFramework'));
spl_autoload_register(array('Tang\Autoload','loadApplication'));

/**
 * 项目类
 * @package Tang
 */
class TangApplication
{
	const VERSION = '1.0';
	const EVENT_LOAD_CONFIG = 'loadConfig';
    const EVENT_RUN = 'run';
	/**
	 * request对象
	 * @var \Tang\Request\IRequest
	 */
	protected $request;
	/**
	 * 事件
	 * @var IEvent
	 */
	protected $event;
    /**
     * @var \Tang\Config\IConfig
     */
    protected $config;
	protected static $applicationPath;

    /**
     * 传入项目路径
     * @param $applicationDirectory
     */
    public function __construct($applicationDirectory)
	{
        $applicationDirectory = rtrim($applicationDirectory,'/\\');
        $applicationDirectory .= DIRECTORY_SEPARATOR;
		static::$applicationPath = $applicationDirectory;
        Autoload::setApplicationDirectory($applicationDirectory);
		$this->register($applicationDirectory);
	}

    /**
     * 增加加载完配置文件事件
     * @param callable $handler
     */
    public function onLoadConfig(Closure $handler)
	{
		$this->event->addListener(TangApplication::EVENT_LOAD_CONFIG,$handler);
	}

    /**
     * 增加运行事件
     * 接受 TangApplication 和Router对象
     * @param callable $handler
     */
    public function onRun(Closure $handler)
    {
        $this->event->addListener(TangApplication::EVENT_RUN,$handler);
    }

	/**
	 * 增加一个事件处理
	 * @param $name
	 * @param callable $handler
	 */
	public function on($name,Closure $handler)
	{
		$this->event->addListener('on'.ucfirst($name),$handler);
	}
    /**
     * 运行项目
     *
     */
    public function run()
    {
		$this->event->attach(TangApplication::EVENT_LOAD_CONFIG,$this);
		$router = $this->request->getRouter();
        $router->router();
        $this->event->attach(TangApplication::EVENT_RUN,$this,$this->request);
		$this->event->attach('on'.$router->getType().$router->getModuleValue(),$this,$this->request);
		Controller::loadRun($router->getModuleValue(),$router->getControllerValue(),$router->getActionValue(),$router->getType());
	}

    /**
     * 获取项目根目录
     * @return mixed
     */
    public static function getApplicationPath()
	{
		return static::$applicationPath;
	}

    /**
     * 注册一些服务
     * @param $applicationDirectory
     */
    protected function register($applicationDirectory)
	{
        $this->config = ConfigService::getService();
        $this->config->setApplicationDirectory($applicationDirectory);
        EventService::setConfig($this->config);
        I18nService::loadFrameworkLanguage();
        $this->config->set('applicationDirectory',$applicationDirectory);
        $this->config->set('frameworkDirectory',__DIR__.DIRECTORY_SEPARATOR.'Tang'.DIRECTORY_SEPARATOR);
        $dataDirctory = ucfirst(trim($this->config->get('dataDirctory','data'),'/\\'));
        $this->config->set('dataDirctory',$applicationDirectory.$dataDirctory.DIRECTORY_SEPARATOR);
        $this->event = EventService::newService();
		$this->request = RequestService::getService();
        $this->request->setApplicationPath($applicationDirectory);
		if($this->config->get('debug'))
		{
			error_reporting(E_ALL &~ E_NOTICE);
		} else
		{
			error_reporting(0);
		}
        set_error_handler(array($this,'phpErrorHandle'),E_ALL &~ E_NOTICE);
        register_shutdown_function(function()
        {
            $error = error_get_last();
            if($error['type'] == E_NOTICE)
            {
                return;
            }
            if($error)
            {
                throw new ErrorException($error['message'],$error['type'],$error['file'],$error['line']);
            }
        });
        set_exception_handler(function(\Exception $e)
        {
            if(!$e instanceof SystemException)
            {
                echo new SystemException($e->getMessage(),null,$e->getCode(),null,$e->getFile(),$e->getLine());
            }
            echo $e;
        });
        date_default_timezone_set($this->config->get('timezone'));
	}

    /**
     * 错误处理
     * @param $errorNo
     * @param $errorMessage
     * @param $errorFile
     * @param $errorLine
     * @throws ErrorException
     */
    public function phpErrorHandle($errorNo,$errorMessage, $errorFile, $errorLine)
    {
        if($errorNo != E_ERROR && $errorNo !=E_WARNING && $errorNo !=E_PARSE)
        {
            return;
        }
        throw new ErrorException($errorMessage,$errorNo,$errorFile,$errorLine);
        exit;
    }
}

/**
 * 针对错误的异常
 * Class ErrorException
 * @package Tang
 */
class ErrorException extends SystemException
{
    public function __construct($message,$errorNo,$errorFile,$errorLine)
    {
        $constants = get_defined_constants();
        $level = array_search($errorNo,$constants,true);
        parent::__construct($message,null,$errorNo,$level,$errorFile,$errorLine);
    }
}

/**
 * 扩展检查
 * Class ExtensionCheck
 * @package Tang
 */
class ExtensionCheck
{
    public static function check($extension)
    {

    }
}