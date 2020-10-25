<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\request;

use nb\Obj;
use nb\Pool;

/**
 * Swoole
 *
 * @package nb\request
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Http extends Php {

    /**
     * @var \swoole\http\Request
     */
    protected $req;

    public function __construct() {
        $this->req = Pool::get('\swoole\http\Request');//\nb\driver\Swoole::$o->request;
        if(!$this->req) {
            $this->req = new \StdClass();
        }
    }

    public function _input() {
        return $this->input;
    }

    public function _server() {
        $header = $this->req->header?:[];
        $server = $this->req->server?:[];
        return array_merge($header,$server);
    }

    public function _files() {
        if(isset($this->req->files)) {
            return $this->req->files;
        }
    }

    public function _get(){
        if(isset($this->req->get)) {
            return $this->req->get;
        }
        return [];
    }

    public function _post(){
        if(isset($this->req->post)) {
            return $this->req->post;
        }
        return [];
    }

    public function _request(){
        return array_merge(
            $this->get,
            $this->post
        );
    }

    public function _cookie() {
        if(isset($this->req->cookie)) {
            return $this->req->cookie;
        }
        return [];
    }

    protected function _method() {
        return $this->server['request_method'];
    }

    /**
     * 根据parse_url的结果重新组合url
     *
     * @access public
     * @param array $params 解析后的参数
     * @return string
     */
    public function _buildUrl($params='') {
        return 'buildUrl';
    }

    protected function _ip() {
        $server = $this->server;
        if($server['x-real-ip']) {
            return $server['x-real-ip'];
        }
        return $server['remote_addr'];
    }

    public function _host() {
        list($host,$port) = explode(':',$this->server['host']);
        $this->port = $port;
        return $host;
    }

    protected function _port() {
        list($host,$port) = explode(':',$this->server['host']);
        $this->port = $host;
        return $port;
    }

    public function _agent() {
        return $this->server['user-agent'];
    }

    public function _scheme() {
        return 'http';
        return $this->isSsl() ? 'https' : 'http';
    }

    public function _pathinfo() {
        return $this->req->server['path_info'];
    }

    public function _uri() {
        return $this->req->server['request_uri'];
    }

    public function _url() {
        return $this->url = $this->domain . $this->uri;
    }

    public function _referer() {

    }

    public function _requestTime() {
        return $this->req->server['request_time'];
    }

    public function _requestTimeFloat() {
        return $this->req->server['request_time_float'];
    }

    /**
     * 设置或获取当前包含协议的域名
     * @access public
     * @param string $domain 域名
     * @return string
     */
    public function _domain() {
        return $this->scheme . '://' . $this->host;
    }

    public function _isPost() {
        $method = $this->method;
        if($method === 'POST') {
            return true;
        }
        return false;
    }

    public function _isGet(){
        $method = $this->method;
        if($method === 'Get') {
            return true;
        }
        return false;
    }

    public function _isAjax(){

    }

    public function __call($name, $arguments) {
        if(method_exists($this->res,$name)) {
            return call_user_func_array([$this->res,$name],$arguments);
        }
        // TODO: Implement __call() method.
        return null;
    }

}