<?php

/**
 * API抽象类
 *
 * @package Api
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Api;
abstract class Abs {

    /**
     * API基础URL，继承后重写，用于拼拼完整URL
     *
     * @var string
     */
    protected static $_url_basic = '';
    
    /**
     * 用户浏览器代理
     *
     * @var string
     */
    protected static $_user_agent = 'Chengxuan-Hiblog-App';
    
    /**
     * 是否是批量请求
     * 
     * @var boolean
     */
    protected $_multi_request = false;
    
    /**
     * 超时时间
     * 
     * @var int
     */
    protected $_time_out = 10;
    
    
    /**
     * 构造方法
     * 
     * @param boolean $multi_request 是否采用批量请求
     */
    public function __construct($multi_request = false) {
        $this->_multi_request = $multi_request;
    }
    
    /**
     * GET请求数据
     * 
     * @param string $path    URL路径
     * @param array  $param   参数
     * @param string $timeout 超时时间
     * 
     * @return \Comm\Request\Single|\mixed
     */
    protected function _get($path, array $param = null, $timeout = null) {
        if($param) {
            $query_string = http_build_query($param);
            $path .= (strpos($path, '?') ? '&' : '?') . $query_string;
        }
        
        $request = $this->_fetchRequestSingle($path, $timeout);
        return $this->_returnRequest($request);
    }
    

    /**
     * POST提交数据
     * 
     * @param string $path           URL路径
     * @param array  $param          参数
     * @param string $custom_request 自定义请求方式
     * @param string $timeout        超时时间
     * 
     * @return \Comm\Request\Single
     */
    protected function _post($path, $param = null, $custom_request = null, $timeout = null) {
        $post_param = is_array($param) ? http_build_query($param) : $param;
        $request = $this->_fetchRequestSingle($path, $timeout);
        $request->setPostData($post_param);
        $custom_request && $request->setOption(CURLOPT_CUSTOMREQUEST, $custom_request);
        return $this->_returnRequest($request);
    }
    
    /**
     * 返回数据
     * 
     * @param \Comm\Request\Single $request
     * 
     * @return \Comm\Request\Single|mixed
     */
    protected function _returnRequest(\Comm\Request\Single $request) {
        if($this->_multi_request) {
            return $request;
        } else {
            $result = $request->exec();
            return $this->_process($result, $request);
        }
    }
    
    /**
     * @todo 未来支持批量
     * @throws \Exception\Program
     */
    public function mAdd() {
        if(!$this->_multi_request) {
            throw new \Exception\Program('Api object is not multi mode.');
        }
    }
    
    
    /**
     * 批量执行
     * 
     * @todo 未来支持
     */
    public function mExecute() {
        if(!$this->_multi_request) {
            throw new \Exception\Program('Api object is not multi mode.');
        }
    }
    
    /**
     * 获取Request请求对象
     * 
     * @param string $path    请求路径
     * @param string $timeout 超时时间
     * 
     * @return \Comm\Request\Single
     */
    protected function _fetchRequestSingle($path, $timeout = null) {
        $timeout || $timeout = $this->_time_out;
        $request = new \Comm\Request\Single($this->_url($path));
        $request->setUserAgent(static::$_user_agent)->setTimeout($timeout);
        $this->_prepareRequest($request);
        return $request;
    }
    
    /**
     * 准备Request请求（主要用于继承后重写编写插件）
     * 
     * @param \Comm\Request\Single $request
     * 
     * @return void
     */
    protected function _prepareRequest(\Comm\Request\Single $request) {
    }

    
    /**
     * 根据相对路径获取URL
     * 
     * @param string $path
     * 
     * @return string
     */
    protected function _url($path) {
        return static::$_url_basic . $path;
    }
    
    /**
     * 处理请求返回值
     * 
     * @param string               $result
     * @param \Comm\Request\Single $request
     * 
     * @return mixed
     */
    protected function _process($result, \Comm\Request\Single $request) {
        $result = json_decode($result);
        return $result;
    }
    
    /**
     * 初始化对象
     * 
     * @return \Api\Abs
     */
    static public function init() {
        return new static();
    }
    
    
    
} 