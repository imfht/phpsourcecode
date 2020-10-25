<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/3
 * Time: 13:58
 */

namespace fastwork\facades;


use fastwork\Facade;
/**
 * @see \fastwork\Response
 * @mixin \fastwork\Response
 * @method void setHttpResponse(\swoole_http_response $response) static
 * @method mixed json($data, $callback = null) static
 * @method void header($key, $val) static
 * @method void cookie(...$args) static
 * @method void code(int $code) static
 * @method void redirect(string $url) static
 * @method void tpl(array $data = [], $file) static
 * @method void clear() static
 */
class Response extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'response';
    }
}