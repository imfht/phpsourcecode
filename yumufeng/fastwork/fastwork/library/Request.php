<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:05
 */

namespace fastwork;

class Request
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    protected $server = [];

    public $cookie = [];

    protected $get = [];

    protected $post = [];

    protected $files = [];

    protected $request = [];

    public $fd = 0;

    public $method = '';
    /**
     * 当前模块名
     * @var string
     */
    protected $module;

    /**
     * 当前控制器名
     * @var string
     */
    protected $controller;

    /**
     * 当前操作名
     * @var string
     */
    protected $action;

    /**
     * 全局过滤规则
     * @var array
     */
    protected $filter;
    /**
     * @var \swoole_http_request
     */
    protected $httpRequest;

    protected $host;

    /**
     * 架构函数
     * @access public
     * @param  array $options 参数
     */
    public function __construct(array $options = [])
    {
        $this->init($options);
    }

    public function init(array $options = [])
    {
        $this->config = array_merge($this->config, $options);

        if (is_null($this->filter) && !empty($this->config['default_filter'])) {
            $this->filter = $this->config['default_filter'];
        }
    }

    public static function __make(Config $config)
    {
        $request = new static($config->pull('app'));

        return $request;
    }

    /**
     * @param \swoole_http_request $httpRequest
     */
    public function setHttpRequest(\swoole_http_request $httpRequest): void
    {
        $this->httpRequest = $httpRequest;

        foreach ($this->httpRequest->server as $k => $v) {
            $this->server[str_replace('-', '_', strtoupper($k))] = $v;
        }
        foreach ($this->httpRequest->header as $k => $v) {
            $this->server['HTTP_' . str_replace('-', '_', strtoupper($k))] = $v;
        }
        $this->fd = $this->httpRequest->fd;
        $this->cookie = $this->httpRequest->cookie ?: [];
        $this->get = $this->httpRequest->get ?: [];
        $this->post = $this->httpRequest->post ?: [];
        $this->files = &$this->httpRequest->files ?: [];
        $this->request = $this->get + $this->post;
    }

    /**
     * @return \swoole_http_request
     */
    public function getHttpRequest(): \swoole_http_request
    {
        return $this->httpRequest;
    }

    public function id()
    {
        return \uuid();
    }

    /**
     * 获取当前域名
     * @return string
     */
    public function domain()
    {
        $url = ($this->server('SERVER_PORT') && $this->server('SERVER_PORT') == '443') ? 'https://' : 'http://';
        $url .= $this->server('HTTP_HOST');
        return $url;
    }

    /**
     * 获取服务头请求
     * @param $name
     * @return mixed|null
     */
    public function server($name = null, $default = null)
    {
        if (empty($name)) {
            return $this->server;
        } else {
            $name = strtoupper($name);
        }

        return isset($this->server[$name]) ? $this->server[$name] : $default;
    }

    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile()
    {
        if ($this->header('user-agent') && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $this->header('user-agent'))) {
            return true;
        } elseif ($this->server('HTTP_VIA') && stristr($this->server('HTTP_VIA'), "wap")) {
            return true;
        } elseif ($this->server('HTTP_ACCEPT') && strpos(strtoupper($this->server('HTTP_ACCEPT')), "VND.WAP.WML")) {
            return true;
        } elseif ($this->server('HTTP_X_WAP_PROFILE') || $this->server('HTTP_PROFILE')) {
            return true;
        } elseif ($this->server('HTTP_USER_AGENT') && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $this->server('HTTP_USER_AGENT'))) {
            return true;
        }

        return false;
    }

    /**
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public function isSsl()
    {
        if ($this->header('Scheme') == 'https') {
            return true;
        } elseif ($this->server('HTTPS') && ('1' == $this->server('HTTPS') || 'on' == strtolower($this->server('HTTPS')))) {
            return true;
        } elseif ('https' == $this->server('REQUEST_SCHEME')) {
            return true;
        } elseif ('443' == $this->server('SERVER_PORT')) {
            return true;
        } elseif ('https' == $this->server('HTTP_X_FORWARDED_PROTO')) {
            return true;
        }

        return false;
    }

    /**
     * 当前请求URL地址中的query参数
     * @access public
     * @return string
     */
    public function query()
    {
        return $this->server('QUERY_STRING');
    }

    /**
     * 获取服务头请求，兼容TP
     * @param null $name
     * @param null $default
     * @return mixed|null
     */
    public function header($name = null, $default = null)
    {
        return $this->server('HTTP_' . $name, $default);
    }

    /**
     * 获取UA
     * @return mixed|null
     */
    public function user_agent()
    {
        return $this->header('USER_AGENT');
    }

    /**
     * 是否为GET请求
     * @access public
     * @return bool
     */
    public function isGet()
    {
        return $this->method() == 'GET';
    }

    /**
     * 是否为POST请求
     * @access public
     * @return bool
     */
    public function isPost()
    {
        return $this->method() == 'POST';
    }

    /**
     * 是否为PUT请求
     * @access public
     * @return bool
     */
    public function isPut()
    {
        return $this->method() == 'PUT';
    }

    /**
     * 是否为DELTE请求
     * @access public
     * @return bool
     */
    public function isDelete()
    {
        return $this->method() == 'DELETE';
    }

    /**
     * 是否为HEAD请求
     * @access public
     * @return bool
     */
    public function isHead()
    {
        return $this->method() == 'HEAD';
    }

    /**
     * 是否为PATCH请求
     * @access public
     * @return bool
     */
    public function isPatch()
    {
        return $this->method() == 'PATCH';
    }

    /**
     * 是否为OPTIONS请求
     * @access public
     * @return bool
     */
    public function isOptions()
    {
        return $this->method() == 'OPTIONS';
    }

    /**
     * 是否为cli
     * @access public
     * @return bool
     */
    public function isCli()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * 是否为cgi
     * @access public
     * @return bool
     */
    public function isCgi()
    {
        return strpos(PHP_SAPI, 'cgi') === 0;
    }

    /**
     * 获取cookie参数
     * @access public
     * @param  string $name 变量名
     * @param  string $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function cookie($name = '', $default = null, $filter = '')
    {

        if (!empty($name)) {
            $data = isset($this->cookie[$name]) ? $this->cookie[$name] : $default;
        } else {
            $data = $this->cookie;
        }

        // 解析过滤器
        $filter = $this->getFilter($filter, $default);

        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filter);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filter);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function file()
    {
        $files = [];
        foreach ($this->files as $name => $fs) {
            $keys = array_keys($fs);
            if (is_array($fs[$keys[0]])) {
                foreach ($keys as $k => $v) {
                    foreach ($fs[$v] as $name => $val) {
                        $files[$name][$v] = $val;
                    }
                }
            } else {
                $files[$name] = $fs;
            }
        }
        return $files;
    }

    /**
     * 获取请求类型
     * @return string
     */
    public function method()
    {
        return strtolower($this->server('REQUEST_METHOD'));
    }

    /**
     * 是否是json请求
     * @return bool
     */
    public function isJson()
    {
        if (strpos($this->server('HTTP_ACCEPT'), '/json') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置参数
     * @param array $params
     * @return Request
     */
    public function setParam(array $params)
    {
        if (!empty($params)) {
            $this->get = array_merge($this->get, $params);
            $this->request = $this->get + $this->post;
        }
        return $this;
    }

    /**
     * request_uri
     * @return mixed|null
     */
    public function path()
    {
        return $this->server('request_uri');
    }

    /**
     * 当前是否Ajax请求
     * @access public
     * @return bool
     */
    private function isAJax()
    {
        $value = $this->server('HTTP_X_REQUESTED_WITH');
        $result = 'xmlhttprequest' == strtolower($value) ? true : false;
        return $result;
    }

    /**
     * 原始数据
     * @return mixed
     */
    public function inputRaw()
    {
        return $this->httpRequest->rawContent();
    }

    /**
     * 获取POST参数
     * @access public
     * @param  string|false $name 变量名
     * @param  mixed $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function post($name = '', $default = null, $filter = '')
    {
        if (empty($this->post)) {
            $this->post = !empty($this->inputRaw()) ? json_decode($this->inputRaw(), true) : [];
        }

        return $this->input($this->post, $name, $default, $filter);
    }

    /**
     * 获取GET参数
     * @access public
     * @param  string|false $name 变量名
     * @param  mixed $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function get($name = '', $default = null, $filter = '')
    {
        if (empty($this->get)) {
            $this->get = !empty($this->inputRaw()) ? json_decode($this->inputRaw(), true) : [];
        }

        return $this->input($this->get, $name, $default, $filter);
    }

    /**
     * @param $data
     * @param $name
     * @param $default
     * @param $filter
     * @return array
     */
    protected function input($data = [], $name = '', $default = null, $filter = '')
    {
        if (false === $name) {
            // 获取原始数据
            return $data;
        }
        $name = (string) $name;
        if ('' != $name) {
            // 解析name
            if (strpos($name, '/')) {
                list($name, $type) = explode('/', $name);
            }

            $data = $this->getData($data, $name);

            if (is_null($data)) {
                return $default;
            }

            if (is_object($data)) {
                return $data;
            }
        }
        // 解析过滤器
        $filter = $this->getFilter($filter, $default);

        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filter);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filter);
        }

        if (isset($type) && $data !== $default) {
            // 强制类型转换
            $this->typeCast($data, $type);
        }

        return $data;
    }
    /**
     * 强制类型转换
     * @access public
     * @param  string $data
     * @param  string $type
     * @return mixed
     */
    private function typeCast(&$data, $type)
    {
        switch (strtolower($type)) {
            // 数组
            case 'a':
                $data = (array) $data;
                break;
            // 数字
            case 'd':
                $data = (int) $data;
                break;
            // 浮点
            case 'f':
                $data = (float) $data;
                break;
            // 布尔
            case 'b':
                $data = (boolean) $data;
                break;
            // 字符串
            case 's':
                if (is_scalar($data)) {
                    $data = (string) $data;
                } else {
                    throw new \InvalidArgumentException('variable type error：' . gettype($data));
                }
                break;
        }
    }
    /**
     * 获取数据
     * @access public
     * @param  array $data 数据源
     * @param  string|false $name 字段名
     * @return mixed
     */
    protected function getData(array $data, $name)
    {
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return;
            }
        }

        return $data;
    }
    /**
     * 获取任意参数
     * @param string $name
     * @param null $default
     * @param string $filter
     * @return array
     */
    public function param($name = '', $default = null, $filter = '')
    {

        $method = $this->method();

        // 自动获取请求变量
        switch ($method) {
            case 'POST':
                $vars = $this->post(false);
                break;
            default:
                $vars = $this->get(false);
        }

        if (true === $name) {
            // 获取包含文件上传信息的数组
            $file = $this->file();
            $data = is_array($file) ? array_merge($vars, $file) : $vars;

            return $this->input($data, '', $default, $filter);
        } else {
            $data = $vars;
        }


        return $this->input($data, $name, $default, $filter);
    }

    /**
     * 设置或获取当前的过滤规则
     * @access public
     * @param  mixed $filter 过滤规则
     * @return mixed
     */
    public function filter($filter = null)
    {
        if (is_null($filter)) {
            return $this->filter;
        }

        $this->filter = $filter;
    }


    protected function getFilter($filter, $default)
    {
        if (is_null($filter)) {
            $filter = [];
        } else {
            $filter = $filter ?: $this->filter;
            if (is_string($filter) && false === strpos($filter, '/')) {
                $filter = explode(',', $filter);
            } else {
                $filter = (array)$filter;
            }
        }

        $filter[] = $default;

        return $filter;
    }

    /**
     * 递归过滤给定的值
     * @access public
     * @param  mixed $value 键值
     * @param  mixed $key 键名
     * @param  array $filters 过滤方法+默认值
     * @return mixed
     */
    private function filterValue(&$value, $key, $filters)
    {
        $default = array_pop($filters);

        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                // 调用函数或者方法过滤
                $value = call_user_func($filter, $value);
            } elseif (is_scalar($value)) {
                if (false !== strpos($filter, '/')) {
                    // 正则过滤
                    if (!preg_match($filter, $value)) {
                        // 匹配不成功返回默认值
                        $value = $default;
                        break;
                    }
                } elseif (!empty($filter)) {
                    // filter函数不存在时, 则使用filter_var进行过滤
                    // filter为非整形值时, 调用filter_id取得过滤id
                    $value = filter_var($value, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $value) {
                        $value = $default;
                        break;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * @return string|null
     */
    public function ip()
    {
        $keys = ['REMOTE_ADDR', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR'];
        $arr = $this->server;
        foreach ($keys as $v) {
            if (array_get($arr, $v) !== null) {
                return array_get($arr, $v);
            }
        }
        return null;
    }

    /**
     * 设置当前请求的host（包含端口）
     * @access public
     * @param  string $host 主机名（含端口）
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * 当前请求的host
     * @access public
     * @param bool $strict true 仅仅获取HOST
     * @return string
     */
    public function host($strict = false)
    {
        if (!$this->host) {
            $this->host = $this->server('HTTP_X_REAL_HOST') ?: $this->server('HTTP_HOST');
        }

        return true === $strict && strpos($this->host, ':') ? strstr($this->host, ':', true) : $this->host;
    }

    /**
     * 当前请求URL地址中的port参数
     * @access public
     * @return integer
     */
    public function port()
    {
        return $this->server('SERVER_PORT');
    }

    /**
     * 当前请求 SERVER_PROTOCOL
     * @access public
     * @return string
     */
    public function protocol()
    {
        return $this->server('SERVER_PROTOCOL');
    }

    /**
     * 当前请求 REMOTE_PORT
     * @access public
     * @return integer
     */
    public function remotePort()
    {
        return $this->server('REMOTE_PORT');
    }

    /**
     * 当前请求 HTTP_CONTENT_TYPE
     * @access public
     * @return string
     */
    public function contentType()
    {
        $contentType = $this->server('CONTENT_TYPE');

        if ($contentType) {
            if (strpos($contentType, ';')) {
                list($type) = explode(';', $contentType);
            } else {
                $type = $contentType;
            }
            return trim($type);
        }

        return '';
    }

    /**
     * 设置当前的模块名
     * @access public
     * @param  string $module 模块名
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * 设置当前的控制器名
     * @access public
     * @param  string $controller 控制器名
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * 设置当前的操作名
     * @access public
     * @param  string $action 操作名
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * 获取当前的模块名
     * @access public
     * @return string
     */
    public function module()
    {
        return $this->module ?: '';
    }

    /**
     * 获取当前的控制器名
     * @access public
     * @param  bool $convert 转换为小写
     * @return string
     */
    public function controller($convert = false)
    {
        $name = $this->controller ?: '';
        return $convert ? strtolower($name) : $name;
    }

    /**
     * 获取当前的操作名
     * @access public
     * @param  bool $convert 转换为驼峰
     * @return string
     */
    public function action($convert = false)
    {
        $name = $this->action ?: '';
        return $convert ? $name : strtolower($name);
    }

    /**
     * 获取操作系统类型
     * @return string
     */
    public function systems()
    {
        $user_agent = PHP_SAPI == 'cli' ? $this->header('user-agent') : $this->server('HTTP_USER_AGENT');
        if (strpos($user_agent, 'iPhone')) {
            return 'iPhone';
        } else if (strpos($user_agent, 'Android')) {
            return 'Android';
        } else if (strpos($user_agent, 'iPad')) {
            return 'iPad';
        } else {
            return 'Windows';
        }
    }

    /**
     * 获取请求时的时间
     * @return int
     */
    public function time()
    {
        return $this->server('REQUEST_TIME') ?: \time();
    }

}