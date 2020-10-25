<?php

/**
 * 请求响应基类
 * @package Madphp\Http
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Http;

class Response
{
    /**
     * 响应实例
     * @var object
     */
    public static $responseInstance = NULL;
    
    /**
     * 状态码
     * @var int
     */
    protected $status;

    /**
     * 文档类型
     * @var string
     */
    protected $contentType;

    /**
     * 头信息
     * @var array
     */
    protected $headers = array(
        'H-Powered-By' => 'Madphp/1.0',
    );

    /**
     * 内容体文本
     * @var string
     */
    protected $body;

    /**
     * 是否已经发送
     * @var bool
     */
    protected $isSendStatus = FALSE;

    /**
     * 状态码描述
     * @var array
     */
    protected static $statusTexts = array(
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '200' => 'OK ^-^',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => '(Unused)',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
    );

    /**
     * 构造方法
     */
    private function __construct()
    {
        $this->status = 200;
        $this->contentType = 'text/html; charset=UTF-8';
    }

    public static function init()
    {
        if (null === self::$responseInstance) {
            self::$responseInstance = new self();
        }
    }
}