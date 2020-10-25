<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 下午4:46.
 */

namespace MiotApi\Api;

class ErrorCode
{
    /**
     * HTTP 标准状态码
     *
     * @var array
     */
    public static $httpCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // WebDAV; RFC 2518
        200 => 'OK 成功，操作完成',
        201 => 'Created',
        202 => 'Accepted 已经接受此次请求，但操作未完成（完成了会有事件通知）',
        203 => 'Non-Authoritative Information', // since HTTP/1.1
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status 成功，但具有多个状态值 (对多个属性的读写)', // WebDAV; RFC 4918
        208 => 'Already Reported', // WebDAV; RFC 5842
        226 => 'IM Used', // RFC 3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other', // since HTTP/1.1
        304 => 'Not Modified',
        305 => 'Use Proxy', // since HTTP/1.1
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect', // since HTTP/1.1
        308 => 'Permanent Redirect', // approved as experimental RFC
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
        418 => 'I\'m a teapot', // RFC 2324
        419 => 'Authentication Timeout', // not in RFC 2616
        420 => 'Method Failure', // Spring Framework
        422 => 'Unprocessable Entity', // WebDAV; RFC 4918
        423 => 'Locked', // WebDAV; RFC 4918
        424 => 'Failed Dependency', // WebDAV; RFC 4918
        425 => 'Unordered Collection', // Internet draft
        426 => 'Upgrade Required', // RFC 2817
        428 => 'Precondition Required', // RFC 6585
        429 => 'Too Many Requests', // RFC 6585
        431 => 'Request Header Fields Too Large', // RFC 6585
        444 => 'No Response', // Nginx
        449 => 'Retry With', // Microsoft
        450 => 'Blocked by Windows Parental Controls', // Microsoft
        451 => 'Unavailable For Legal Reasons', // Internet draft
        494 => 'Request Header Too Large', // Nginx
        495 => 'Cert Error', // Nginx
        496 => 'No Cert', // Nginx
        497 => 'HTTP to HTTPS', // Nginx
        499 => 'Client Closed Request', // Nginx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // RFC 2295
        507 => 'Insufficient Storage', // WebDAV; RFC 4918
        508 => 'Loop Detected', // WebDAV; RFC 5842
        509 => 'Bandwidth Limit Exceeded', // Apache bw/limited extension
        510 => 'Not Extended', // RFC 2774
        511 => 'Network Authentication Required', // RFC 6585
        598 => 'Network read timeout error', // Unknown
        599 => 'Network connect timeout error', // Unknown
    ];

    /**
     * 出现错误的位置.
     *
     * @var array
     */
    public static $positions = [
        0 => '未知位置',
        1 => '开放平台',
        2 => '设备云',
        3 => '设备',
        4 => 'MIOT-SPEC',
    ];

    public static $errorCodes = [
        '001' => 'Device不存在',
        '002' => 'Service不存在',
        '003' => 'Property不存在',
        '004' => 'Event不存在',
        '005' => 'Action不存在',
        '006' => '没找到设备描述',
        '007' => '没找到设备云',
        '008' => '无效的ID (无效的PID、SID、AID、EID等)',
        '009' => 'Scene不存在',
        '013' => 'Property不可读',
        '023' => 'Property不可写',
        '033' => 'Property不可订阅',
        '043' => 'Property值错误',
        '034' => 'Action返回值错误',
        '015' => 'Action执行错误',
        '025' => 'Action参数个数不匹配',
        '035' => 'Action参数错误',
        '036' => '设备操作超时',
        '100' => '设备在当前状态下无法执行此操作',
        '901' => 'TOKEN不存在或过期',
        '902' => 'TOKEN非法',
        '903' => '授权过期',
        '904' => '语音设备未授权',
        '905' => '设备未绑定',
    ];

    public static function getPosition($positionCode)
    {
        return ' Position : '.
            (isset(self::$positions[$positionCode]) ? self::$positions[$positionCode] : 'Unknow Position');
    }

    /**
     * @param array $miotErrorCode
     *
     * @return string
     */
    public static function getMiotErrorMessage($miotErrorCode)
    {
        if (preg_match('/\-70([0-9]{3})([0-9])([0-9]{3})/', $miotErrorCode, $matches)) {
            return self::getHttpMessage($matches[1]).
                self::getErrorMessage($matches[2]).
                self::getErrorMessage($matches[3]);
        }

        return 'Unknow Error';
    }

    public static function getHttpMessage($httpCode)
    {
        return ' HttpCodeMessage ('.$httpCode.'): '.
            (isset(self::$httpCodes[$httpCode]) ? self::$httpCodes[$httpCode] : 'Unknow Http Code');
    }

    public static function getErrorMessage($errorCode)
    {
        return ' ErrorMessage : '.
            (isset(self::$errorCodes[$errorCode]) ? self::$errorCodes[$errorCode] : 'Unknow Error');
    }
}
