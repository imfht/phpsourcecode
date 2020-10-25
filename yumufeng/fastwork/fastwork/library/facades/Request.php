<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 15:49
 */

namespace fastwork\facades;


use fastwork\Facade;

/**
 * @see \fastwork\Request
 * @mixin \fastwork\Request
 * @method mixed getHttpRequest() static
 * @method mixed setHttpRequest($httpRequest) static 获取当前包含协议、端口的域名
 * @method mixed domain(bool $port = false) static 获取当前包含协议、端口的域名
 * @method mixed systems() static 获取当客户端类型
 * @method float time(bool $float = false) static 获取当前请求的时间
 * @method string method(bool $method = false) static 当前的请求类型
 * @method bool isGet() static 是否为GET请求
 * @method bool isPost() static 是否为POST请求
 * @method bool isPut() static 是否为PUT请求
 * @method bool isDelete() static 是否为DELTE请求
 * @method bool isHead() static 是否为HEAD请求
 * @method bool isPatch() static 是否为PATCH请求
 * @method bool isOptions() static 是否为OPTIONS请求
 * @method bool isCli() static 是否为cli
 * @method bool isCgi() static 是否为cgi
 * @method mixed param(string $name = '', mixed $default = null, mixed $filter = '') static 获取当前请求的参数
 * @method mixed get(string $name = '', mixed $default = null, mixed $filter = '') static 设置获取GET参数
 * @method mixed post(string $name = '', mixed $default = null, mixed $filter = '') static 设置获取POST参数
 * @method mixed file(string $name = '') static 获取上传的文件信息
 * @method mixed header(string $name = '', mixed $default = null) static 设置或者获取当前的Header
 * @method mixed input(array $data,mixed $name = '', mixed $default = null, mixed $filter = '') static 获取变量 支持过滤和默认值
 * @method mixed filter(mixed $filter = null) static 设置或获取当前的过滤规则
 * @method bool isSsl() static 当前是否ssl
 * @method bool isAjax(bool $ajax = false) static 当前是否Ajax请求
 * @method mixed ip(int $type = 0, bool $adv = true) static 获取客户端IP地址
 * @method bool isMobile() static 检测是否使用手机访问
 * @method string scheme() static 当前URL地址中的scheme参数
 * @method string query() static 当前请求URL地址中的query参数
 * @method string host(bool $stric = false) static 当前请求的host
 * @method string port() static 当前请求URL地址中的port参数
 * @method string contentType() static 当前请求 HTTP_CONTENT_TYPE
 * @method string module() static 获取当前的模块名
 * @method string controller(bool $convert = false) static 获取当前的控制器名
 * @method string action(bool $convert = false) static 获取当前的操作名
 */

class Request extends Facade
{
    protected static function getFacadeClass(): string
    {
        return 'request';
    }
}