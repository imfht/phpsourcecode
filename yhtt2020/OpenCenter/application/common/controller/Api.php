<?php
/**
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: sun(slf02@ourstu.com)
 * Date: 2018/9/25
 * Time: 14:02
 */

namespace app\common\controller;


use think\Controller;
use think\Request;

/**
 * Class Api基类
 * @package app\common\controller
 */
class Api extends  Controller
{
    protected $appKey;
    protected $appVersion;
    protected $openId;
    protected $accessToken;

    // 当前请求类型
    protected $_method = '';
    // 当前请求的资源类型
    protected $_type = '';
    // REST允许的请求类型列表
    protected $allowMethod = array('get', 'post', 'put', 'delete');
    // REST默认请求类型
    protected $defaultMethod = 'get';
    // REST允许请求的资源类型列表
    protected $allowType = array('html', 'xml', 'json', 'rss');
    // 默认的资源类型
    protected $defaultType = 'html';
    // REST允许输出的资源类型列表
    protected $allowOutputType = array(
        'xml' => 'application/xml',
        'json' => 'application/json',
        'html' => 'text/html',
    );

    public function __construct()
    {
       // parent::__construct($request, $app);
        header('Access-Control-Allow-Origin:*');

        $aToken = input('access_token');
        //todo open_id和版本号验证
        if ($aToken != config('app.access_token')) {
            $this->apiReturn(400, '无效的access_token');
        }

    }

    /**
     * @param $result
     * @author sun slf02@ourstu.com
     * @date 2018/9/25 11:02
     * 返回数据
     */
    public function ajaxSuccess($result)
    {
        $this->apiSuccess('返回成功', $result);
    }

    /**必要参数的验证提示
     * @param array $args 待验证参数对象组
     * @author sun slf02@ourstu.com
     */
    public function ajaxError($args)
    {
        foreach ($args as $key => $value) {
            if (empty($value))
                $this->apiError($key . '参数错误');
        }
    }

    /**
     * @param $code
     * @author sun slf02@ourstu.com
     * @date 2018/9/25 11:02
     * 发送Http状态信息
     */
    protected function sendHttpStatus($code)
    {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if (isset($_status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:' . $code . ' ' . $_status[$code]);
        } else {
            // 找不到code码时为400
            $code = 400;
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:' . $code . ' ' . $_status[$code]);
        }
    }

    /**
     * 编码数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @return void
     */
    protected function encodeData($data, $type = '')
    {
        if (empty($data)) return '';
        if ('json' == $type) {
            // 返回JSON数据格式到客户端 包含状态信息
            $data = json_encode($data);
        } elseif ('xml' == $type) {
            // 返回xml格式数据
            $data = xml_encode($data);
        } elseif ('php' == $type) {
            $data = serialize($data);
        }
        //TODO 过滤
        $callback = input('callback', '');
        if ($callback) {
            $data = $callback . '(' . $data . ')';
        }
        // 默认直接输出
        $this->setContentType($type);
        header('Content-Length: ' . strlen($data));
        return $data;
    }

    /**
     * 设置页面输出的CONTENT_TYPE和编码
     * @access public
     * @param string $type content_type 类型对应的扩展名
     * @param string $charset 页面输出编码
     * @return void
     */
    public function setContentType($type, $charset = '')
    {
        if (headers_sent()) return;
        if (empty($charset)) $charset = config('DEFAULT_CHARSET');
        $type = strtolower($type);
        if (isset($this->allowOutputType[$type])) //过滤content_type
            header('Content-Type: ' . $this->allowOutputType[$type] . '; charset=' . $charset);
    }

    /**
     * 输出返回数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @param integer $code HTTP状态
     * @return void
     */
    protected function response($data, $type = '', $code = 200)
    {
        $this->sendHttpStatus($code);
        exit($this->encodeData($data, strtolower($type)));
    }

    /**
     * apiReturn  api返回方法，约定 。int 是 code。string 是 Info，array 是 data
     * 其中array型可以传多个，第一个返回参数名为data，其余的按照data_ + 调用时的位置 命名
     * @author sun slf02@ourstu.com
     */
    protected function apiReturn()
    {
        $code = 200;
        $args = func_get_args();
        $this->apiResponse($args, $code);
    }

    protected function apiSuccess()
    {
        $args = func_get_args();
        $code = 200;
        $this->apiResponse($args, $code);
    }

    protected function apiError()
    {
        $args = func_get_args();
        $code = 400;
        $this->apiResponse($args, $code);
    }

    protected function apiResponse($args, $code = 200)
    {
        $rs = array();
        foreach ($args as $key => $v) {
            if (is_array($v)) {
                if (isset($rs['data'])) {
                    $rs['data_' . $key] = $v;
                } else {
                    $rs['data'] = $v;
                }
            } elseif (is_int($v)) {
                $code = $v;
            } else {
                $rs['info'] = $v;
            }
        }
        $rs['code'] = $code;
        $this->response($rs, 'json', $code);
    }


}
