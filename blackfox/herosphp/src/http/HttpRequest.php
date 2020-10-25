<?php
/**
 * http 请求解析类, 将url解析成合法格式如下
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */
namespace herosphp\http;


class HttpRequest {

    /**
     * 本次请求访问的模块
     * @var string
     */
    private $module;

    /**
     * 本次请求访问的action
     * @var string
     */
    private $action;

    /**
     * 本次请求调用的主方法
     * @var string
     */
    private $method;

    /**
     * 本次请求的url
     * @var string
     */
    private $requestUri;

    /**
     * 上次请求的url
     * @var
     */
    private $referer;

    /**
     * URL映射规则,数组,键为目标链接,值为源链接
     * @var array
     */
    private static $urlMappingRules = array();

    /**
     * 请求参数
     * @var array
     */
    private $parameters = array();

    public function __construct() {

        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->referer = $_SERVER['HTTP_REFERER'];

    }

    /**
     * 解析url成pathinfo形式，并获取参数
     * http://www.herosphp.my/admin/member-login-index.html?id=12
     */
    public function parseURL() {

        $appConfigs = getConfigs(); //获取app配置

        self::$urlMappingRules = $appConfigs['url_mapping_rules'];
        //优先处理短链接映射
        $this->requestUri = self::url2source($this->requestUri);
        $_SERVER['REQUEST_URI'] = $this->requestUri;

        $defaultUrl = $appConfigs['default_url'];
        $urlInfo = parse_url($this->requestUri);

        if ( $urlInfo['path'] && $urlInfo['path'] != '/' ) {
            $filename = str_replace(EXT_URI, '', $urlInfo['path']);
            $filename = rtrim($filename, "/");
            $pathInfo = explode('/', $filename);
            array_shift($pathInfo);
            if ( $pathInfo[0] ) $this->setModule($pathInfo[0]);
            if ( $pathInfo[1] ) $this->setAction($pathInfo[1]);
            if ( $pathInfo[2] ) $this->setMethod($pathInfo[2]);

            //提取pathinfo参数
            if ( count($pathInfo) > 3 ) {
                if ( isset($pathInfo[3]) ) {
                    $params = explode('-', $pathInfo[3]);
                    for ( $i = 0; $i < count($params); $i++ ) {
                        if ( $i % 2 == 0 ) {
                            if ( trim($params[$i]) == '' ) {
                                continue;
                            }
                            $_GET[$params[$i]] = $params[$i+1];
                        }
                    }
                }
            }

            //提取query参数
            if ( isset($urlInfo['query']) ) {
                $params = explode('&', $urlInfo['query']);
                foreach ( $params as $values ) {
                    $__p = explode('=', $values);
                    if ( trim($__p[0]) == '' ) {
                        continue;
                    }
                    $_GET[$__p[0]] = urldecode($__p[1]);
                }
            }

        }

        //如果没有任何参数，则访问默认页面。如http://www.herosphp.my这种格式
        if ( !$this->module ) $this->setModule($defaultUrl['module']);
        if ( !$this->action ) $this->setAction($defaultUrl['action']);
        if ( !$this->method ) $this->setMethod($defaultUrl['method']);

        $this->setParameters($_GET + $_POST);

        //die();
    }

    /**
     * URL短链接的目标链接到源链接之间的转换
     * @param $url
     * @return mixed
     */
    public static function url2source($url) {

        $mappingRules = array();
        foreach ( self::$urlMappingRules as $target => $source ) {
            $mappingRules['/' . $target . '/iU'] = $source;
        }
        return preg_replace(array_keys($mappingRules), $mappingRules, $url);

    }

    /**
     * Get a parameter's value.
     * @param string $name
     * 参数名称
     * @param $func_str
     * 函数名称，参数需要用哪些函数去处理
     * @param boolean $setParam 是否重置参数
     * @return int|string
     */
    public function getParameter( $name, $func_str=null, $setParam=true ) {

		if ( !$func_str ) return urldecode($this->parameters[$name]) ? urldecode($this->parameters[$name]) : $this->parameters[$name];

        $funcs = explode("|", $func_str);
        $args = urldecode($this->parameters[$name]);
        foreach ( $funcs as $func ) {
            $args = call_user_func($func, $args);
        }
        if ( $setParam ) {
            $this->parameters[$name] = $args;
        }
        return $args;

    }

    /**
     * @param string $name 获取整数的参数
     * @return int
     */
    public function getIntParam($name) {
        return intval($this->parameters[$name]);
    }

    /**
     * 获取浮点型的参数
     * @param $name
     * @return float
     */
    public function getFloatParam($name) {
        return floatval($this->parameters[$name]);
    }

    /**
     * 获取字符串参数
     * @param $name
     * @return string
     */
    public function getStrParam($name) {
        return trim($this->parameters[$name]);
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * 添加参数
     * @param $name
     * @param $value
     */
    public function addParameter( $name, $value ) {
        if ( $name && $value )
            $this->parameters[$name] = $value;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $url
     */
    public function setRequestUri($url)
    {
        $this->requestUri = $url;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @param mixed $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * @return mixed
     */
    public function getReferer()
    {
        return $this->referer;
    }

}
