<?php

/**
 *
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Http;
use Madphp\Config;
use Madphp\Support\Secure;

class Request
{
    /**
     * 请求实例
     * @var object
     */
    public static $requestInstance = NULL;

    /**
     * 当前用户ip地址
     * @var string
     */
    protected $ipAddress             = FALSE;

    /**
     * 当前用户浏览器user agent
     * @var string
     */
    protected $userAgent             = FALSE;

    /**
     * 如果设置为false, $_GET被设置成空数组
     * @var bool
     */
    protected $allowGetArray         = TRUE;

    /**
     * 是否转换换行符为系统换行符
     * @var bool
     */
    protected $standardizeNewlines   = TRUE;

    /**
     * 判断是否从 $_GET，$_POST，$_COOKIE数组过滤xss字符
     * 从配置文件中取值
     * @var bool
     */
    protected $enableXss             = FALSE;

    /**
     * 是否设置防止csrf cookie
     * 从配置文件获取
     * @var bool
     */
    protected $enableCsrf            = FALSE;
    
    /**
     * 所有HTTP请求头信息
     * @var array
     */
    protected $headers               = array();

    /**
     * 请求方式
     * @var string
     */
    protected $method                = null;

    /**
     * 是否允许请求方式被参数修改
     * @var bool
     */
    protected $httpMethodParameterOverride = TRUE;

    /**
     * 初始化请求实例
     * @access   public
     * @return  object
     */
    public static function init()
    {
        if (self::$requestInstance === null) {
            self::$requestInstance = new self();
        }
    }

    /**
     * 构造函数
     * @access   private
     * @return   void
     */
    private function __construct()
    {
        writeLog('debug', "Http/Request Class Initialized");

        $this->allowGetArray  = Config::get('request', 'allowGetArray', TRUE);
        $this->enableXss      = Config::get('request', 'globalXssFiltering', FALSE);
        $this->enableCsrf     = Config::get('request', 'csrfProtection', FALSE);

        // 全局数组处理
        $this->sanitizeGlobals();
    }

    /**
     * 全局变量处理
     * 构造函数调用
     * @access   private
     * @return   void
     */
    private function sanitizeGlobals()
    {
        $protected = array(
            '_SERVER', '_GET', '_POST', '_FILES', '_REQUEST',
            '_SESSION', '_ENV', 'GLOBALS', 'HTTP_RAW_POST_DATA',
            'system_folder', 'application_folder', 'BM', 'EXT',
            'CFG', 'URI', 'RTR', 'OUT', 'IN',
        );

        foreach (array($_GET, $_POST, $_COOKIE) as $global) {
            if (!is_array($global)) {
                if (!in_array($global, $protected)) {
                    global $$global;
                    $$global = NULL;
                }
            } else {
                foreach ($global as $key => $val) {
                    if (!in_array($key, $protected)) {
                        global $$key;
                        $$key = NULL;
                    }
                }
            }
        }

        // 不允许 Get 数组，清空Get 数组
        // 处理 $_GET 数组
        if ($this->allowGetArray == FALSE) {
            $_GET = array();
        } else {
            if (is_array($_GET) AND count($_GET) > 0) {
                foreach ($_GET as $key => $val) {
                    $_GET[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
                }
            }
        }

        // 处理 $_POST 数组
        if (is_array($_POST) AND count($_POST) > 0) {
            foreach ($_POST as $key => $val) {
                $_POST[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }
        }

        // 处理 $_COOKIE 数组
        if (is_array($_COOKIE) AND count($_COOKIE) > 0) {
            unset($_COOKIE['$Version']);
            unset($_COOKIE['$Path']);
            unset($_COOKIE['$Domain']);

            foreach ($_COOKIE as $key => $val) {
                $_COOKIE[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }
        }

        $_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);

        // HTTP请求 CSRF防护检测
        if ($this->enableCsrf == TRUE && is_cli()) {
            Secure::csrfVerify();
        }

        writeLog('debug', "Global POST and COOKIE data sanitized");
    }

    /**
     * 检查非法的数组键
     * sanitizeGlobals() 方法调用
     * @access   private
     * @param    string
     * @return   string
     */
    private function cleanInputKeys($str)
    {
        if (!preg_match("/^[a-z0-9:_\/-]+$/i", $str)) {
            exit('Disallowed Key Characters.');
        }

        return $str;
    }

    /**
     * 处理输入的值
     * sanitizeGlobals() 方法调用
     * @access   private
     * @param    string
     * @return   string
     */
    private function cleanInputData($str)
    {
        if (is_array($str)) {
            $new_array = array();
            foreach ($str as $key => $val) {
                $new_array[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }
            return $new_array;
        }

        if (!is_php_version('5.4') && get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }

        // 移除不可见字符
        $str = remove_invisible_characters($str);

        // 移除xss字符
        if ($this->enableXss === TRUE) {
            $str = Secure::xssClean($str);
        }

        // 替换换行符为当前系统换行符
        if ($this->standardizeNewlines == TRUE) {
            if (strpos($str, "\r") !== FALSE) {
                $str = str_replace(array("\r\n", "\r", "\r\n\n"), PHP_EOL, $str);
            }
        }

        return $str;
    }

    /**
     * 防止克隆对象
     * @access  private
     * @return void
     */
    private function __clone() {}

    /**
     * @access  private
     * @return void
     */
    private function __wakeup() {}
}