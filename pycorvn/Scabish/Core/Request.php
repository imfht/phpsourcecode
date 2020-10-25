<?php
namespace Scabish\Core;

use SCS;
use Exception;

/**
 * Scabish\Core\Request
 * URL请求解析类
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2016-12-7
 */
class Request {
    
    public $control; // 当前请求的控制器名
    public $action; // 当前请求的方法名
    public $route; // 当前路由
    public $baseUri; // 当前请求基本url
    public $base; // 项目asset基本目录(可能会和baseUri不同)
    public $requestUri; // 当前请求的url
    
    private $_get = [];
    private $_post = [];
    private $_param = [];
    private $_segment = [];
    
    private static $_instance;
    
    private function __construct() {
        if(SCS::Instance()->mode == 'web') {
            $this->ParseGet();
            $this->ParsePost();
            $this->ParseParam();
            $this->ParseSegment();
        } elseif(SCS::Instance()->mode == 'cmd') {
            $this->requestUri = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
            $this->ParseAndSplit($this->requestUri);
        } else {
            throw new Exception('Item "mode" of Configure is required');
        }
    }
    
    public function __set($name, $value) {
        throw new Exception('access deny: '.$name.' = '.$value);
    }
    
    public function __clone() {}
    
    public static function Instance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 
     * 获取/设置当前GET请求中指定参数值
     */
    public function Get() {
        $argNum = func_num_args();
        if(0 == $argNum) {
            return $this->_get;
        }
        if(1 == $argNum) {
            if(empty($this->_get)) {
                return false;
            }
            foreach($this->_get as $k=>$v) {
                if(0 == strcasecmp($k, func_get_arg(0))) {
                    return $this->_get[$k];
                }
            }
            return false;
        }
        
        if(2 == $argNum) {
            $this->_get[func_get_arg(0)] = func_get_arg(1);
        }
    }
    
    /**
     * 获取/设置当前POST请求中指定参数值
     * @example
     *  SCS::Request()->Post('a'); // 获取参数a的值
     *  SCS::Request()->Post('a', 1); // 设置参数a的值为1
     */
    public function Post() {
        $args = func_get_args();
        if(empty($args)) return $this->_post;
        if(count($args) == 1) { // 取值
            if(isset($this->_post[$args[0]])) {
                return is_array($this->_post[$args[0]]) ? $this->_post[$args[0]] : trim($this->_post[$args[0]]);
            } else {
                return false;
            }
        } elseif(count($args) == 2) { // 设置值
            $this->_post[$args[0]] = $args[1];
        }
    }
    
    /**
     * 获取/设置当前REQUEST请求中指定参数值
     * @example
     *  SCS::Request()->Param('a'); // 获取参数a的值
     *  SCS::Request()->Param('a', 1); // 设置参数a的值为1
     */
    public function Param() {
        $args = func_get_args();
        if(empty($args)) return $this->_param;
        if(count($args) == 1) { // 取值
            if(!isset($this->_param[$args[0]])) return false;
            return is_array($this->_param[$args[0]]) ? $this->_param[$args[0]] : trim($this->_param[$args[0]]);
        } elseif(count($args) == 2) { // 设置值
            $this->_param[$args[0]] = $args[1];
        }
    }
    
    /**
     * 获取/设置当前REQUEST请求中指定段
     * @param integer $index url请求分段索引，0为控制器名；1为动作名
     * @example
     *  对于url： http://domain.com/?blog/view/1，1表示blog的id，获取id的方式：
     *  SCS::Request()->Segment(2);
     */
    public function Segment($index) {
        return isset($this->_segment[$index]) ? trim($this->_segment[$index]) : false;
    }
    
    /**
     * 路由重定向
     * @param string $route 路由地址
     * @param array $params 参数
     * @param boolean $exit 是否立即终止程序执行
     * @example
     * SCS::Request()->Route('index/test', array('id' => 1, 'name' => 'keluo'));
     */
    public function Route($route = '', $params = array(), $exit = true) {
        $this->Redirect(SCS::Url()->Create($route, $params), $exit);
    }
    
    /**
     * url重定向
     * @param string $url 完整url地址
     * @param boolean $exit 是否立即终止程序执行
     */
    public function Redirect($url, $exit = true) {
        header('Location: '.$url);
        $exit && exit;
    }
    
    /**
     * 检查当前请求是否为post方式
     */
    public function IsPost() {
        return 0 == strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }
    
    /**
     * 验证当前请求是否合法
     * @todo 对请求来源，数据安全进行检查
     */
    public function IsValid() {
        // @todo 在请求数据中构造签名参数_sign和时间戳_time,如有必要，对数据本身进行签名，以确保数据是安全未经篡改的
        // SCS::Url()->CreateSign('contact/post')
    }
    
    private function ParseRequestUri() {
        $base = substr(SCS::Instance()->app, strlen($_SERVER['DOCUMENT_ROOT']));
        // 阿里云主机奇葩环境，$_SERVER[DOCUMENT_ROOT]和__FILE__路径不一致，被逼无奈单独处理，正常情况请使用上一行
        #$base = substr(SCS::Instance()->app, strlen('/data'.substr($_SERVER['DOCUMENT_ROOT'], 4)));
        $base = trim(str_replace('\\', '/', $base), '/');
        $this->base = $base ? '/'.$base : '';
        $this->baseUri = trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
        $this->baseUri = $this->baseUri ? '/'.$this->baseUri : '';
        $requestUri = preg_replace(SCS::Instance()->rewrite ? '/\?.*/' : '/^\/+\?/', '', $_SERVER['REQUEST_URI']);
        $requestUri = preg_replace('/'.str_replace('/', '\/', $this->baseUri).'/i', '', $requestUri, 1);
        $requestUri = str_replace('index.php', '', $requestUri);
        $this->requestUri = ltrim(trim($requestUri, '/'), '?');
    }
    
    /**
     * 
     * 解析GET请求
     */
    private function ParseGet() {
        $this->ParseRequestUri();
        $this->ParseAndSplit($this->requestUri);
    }
    
    private function ParseAndSplit($params) {
        if(strlen($params)) {
            $splits = explode('/', $params);
            $this->control = $splits[0];
            if(!isset($splits[1])) {
                $this->action = 'index';
            } else {
                $this->action = $splits[1];
                array_shift($splits);
                array_shift($splits);
                if(!empty($splits)) {
                    if(count($splits) % 2 != 0) array_pop($splits);
                    $keys = $values = array();
                    for($idx = 0; $idx < count($splits); $idx++) {
                        if($idx % 2 == 0) {
                            $keys[] = $splits[$idx];
                        } else {
                            $values[] = trim(urldecode($splits[$idx]));
                        }
                    }
                    if(!empty($keys) && !empty($values)) {
                        $this->_get = array_combine($keys, $values);
                    }
                }
            }
        } else {
            $this->control = 'index';
            $this->action = 'index';
        }
        $this->route = $this->control.'/'.$this->action;
    }
    
    /**
     * 
     * 解析POST请求
     */
    private function ParsePost() {
        if(!$this->IsPost()) return;
        foreach($_POST as $k=>$v) {
            $this->_post[$k] = is_array($v) ? $v : trim($v);
        }
    }
    
    /**
     *
     * 解析REQUEST请求
     */
    private function ParseParam() {
        $this->_param = array_merge($this->_get, $this->_post);
    }
    
    /**
     * 解析url请求段
     * @return array
     */
    private function ParseSegment() {
        $segments = explode('/', $this->requestUri);
        foreach($segments as $k=>$segment) {
            if(strlen(trim($segment, '/'))) {
                $this->_segment[] = $segment;
            }
        }
    }
}