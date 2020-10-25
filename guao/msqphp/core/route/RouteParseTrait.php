<?php declare (strict_types = 1);
namespace msqphp\core\route;

use msqphp\base\ip\Ip;

trait RouteParseTrait
{
    // 解析信息数组
    // url格式 protocol ://  hostname[:port] / path / [;parameters][?query]#fragment
    // 因#fragment无法获取,所以忽略,[;parameters]这个不常用,忽略
    private static $parse_info = [
        // 'protocol' =>'',
        // 'domain'   =>'',
        // 'method'   =>'',
        // 'path'     =>'',
        // 'query'    =>'',
        // 'get'      =>[],
        // 'extension' => '',
    ];

    // 解析路径和查询参数
    private static function parsePathQueryExtension(): void
    {
        // 获取路径和get参数
        $path_and_query = urldecode(ltrim($_SERVER['REQUEST_URI'], '/'));

        static::$parse_info['get'] = [];

        // 若果不存在get参数,例:www.example.com/nihao/20
        if (false === $pos = strpos($path_and_query, '?')) {
            // 直接赋值
            static::$parse_info['path'] = static::parseExtension($path_and_query);
        } else {
            // 分割path和query
            static::$parse_info['path']  = static::parseExtension(substr($path_and_query, 0, $pos));
            static::$parse_info['query'] = $query = substr($path_and_query, $pos + 1);
            // query语句解析
            !empty($query) && array_map(function (string $param) {
                // 包括等于号,避免不完全的param参数
                if (false !== $pos = strpos($param, '=')) {
                    // 添加到数组中
                    static::$parse_info['get'][substr($param, 0, $pos)] = substr($param, $pos + 1);
                }
            }, explode('&', $query));
        }
        $_GET                 = static::$parse_info['get'];
        static::$pending_path = explode('/', static::$parse_info['path']);
    }

    // 移除'index.php','.php'等后缀
    private static function parseExtension(string $path): string
    {
        $pos_a = strrpos($path, '.');
        $pos_b = strrpos($path, '/');
        // .在/后 例:.com/index.php,此时获取后缀名
        if ($pos_a && $pos_b && $pos_a > $pos_b) {
            $extension = substr($path, $pos_a + 1);
            $path      = substr($path, 0, $pos_a);
            // 忽略/index,避免一些问题(可有可无)
            isset($path[5]) && substr($path, -6) === '/index' && $path = substr($path, 0, strlen($path) - 10);
        }
        static::$parse_info['extension'] = $extension ?? 'php';
        return trim($path, '/');
    }

    // 获得路径
    private static function getPath(): string
    {
        return static::$parse_info['path'];
    }
    // 获得查询语句(get参数)
    private static function getQuery(): string
    {
        return static::$parse_info['query'];
    }
    // 获得扩展名
    private static function getExtension(): string
    {
        return static::$parse_info['extension'];
    }
    // 获得访问方法
    private static function getMethod(): string
    {
        return static::$parse_info['method'] = static::$parse_info['method'] ?? strtolower(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? 'ajax' : $_SERVER['REQUEST_METHOD']);
    }
    // 获得协议
    private static function getProtocol(): string
    {
        if (!isset(static::$parse_info['protocol'])) {
            static::$parse_info['protocol'] =
            (isset($_SERVER['HTTPS']) && ('1' === $_SERVER['HTTPS'] || 'on' === strtolower($_SERVER['HTTPS'])))
            ||
            (isset($_SERVER['SERVER_PORT']) && '443' === $_SERVER['SERVER_PORT'])
            ? 'https'
            : 'http';
        }
        return static::$parse_info['protocol'];
    }
    // 获得域名
    private static function getDomain(): string
    {
        return static::$parse_info['domain'] = static::$parse_info['domain'] ?? $_SERVER['$_SERVER_NAME'] ?? $_SERVER['HTTP_HOST'];
    }
    // 获得端口
    private static function getPort(): int
    {
        return static::$parse_info['port'] = (int) static::$parse_info['port'] ?? $_SERVER['SERVER_PORT'];
    }
    // 获得ip
    private static function getIp(): string
    {
        return static::$parse_info['ip'] = static::$parse_info['ip'] ?? Ip::get();
    }
    // 获得referer
    private static function getReferer(): string
    {
        return static::$parse_info['referer'] = static::$parse_info['referer'] ?? $_SERVER['HTTP_REFERER'];
    }
}
