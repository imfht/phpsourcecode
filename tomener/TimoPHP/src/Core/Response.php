<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;

class Response
{
    /**
     * @var string 响应类型
     */
    protected static $type;

    /**
     * @var array 响应数据
     */
    protected static $data;

    /**
     * 发送响应数据
     *
     * @param string $data
     * @param string $type
     * @param bool $return
     * @param bool $isExit
     * @return array|null|string
     */
    public static function send($data = '', $type = '', $return = false, $isExit = true)
    {
        static::$type = static::type();
        $type = strtolower($type ? : static::$type);

        $headers = [
            'json'   => 'application/json',
            'xml'    => 'text/xml',
            'html'   => 'text/html',
            'jsonp'  => 'application/javascript',
            'script' => 'application/javascript',
            'text'   => 'text/plain',
        ];

        if (!headers_sent() && isset($headers[$type])) {
            header('Content-Type:' . $headers[$type] . '; charset=utf-8');
        }

        $data = $data ?: static::$data;

        switch ($type) {
            case 'json':
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                break;
            case 'jsonp':
                $handler = !empty($_GET[Config::runtime('var_jsonp_handler')]) ? $_GET[Config::runtime('var_jsonp_handler')] : Config::runtime('default_jsonp_handler');
                $data    = $handler . '(' . json_encode($data, JSON_UNESCAPED_UNICODE) . ');';
                break;
            case '':
            case 'html':
            case 'text':
                // 不做处理
                break;
        }

        if ($return) {
            return $data;
        }

        echo $data;
        if ($isExit) {
            exit;
        }
        return null;
    }

    /**
     * 重定向
     *
     * @param string $url
     * @param int $code
     */
    public static function redirect($url, $code = 302)
    {
        static::sendResponseCode($code);
        static::header('Location', $url);
        exit;
    }

    /**
     * 输出类型获取、设置
     *
     * @param null $type 输出内容的格式类型
     * @return bool
     */
    public static function type($type = null)
    {
        if (is_null($type)) {
            return static::$type ? : Config::runtime('default_return_type');
        }
        static::$type = $type;
        return true;
    }

    /**
     * 发送HTTP状态码
     *
     * @param int $code
     */
    public static function sendResponseCode($code = 200)
    {
        http_response_code($code);
    }

    /**
     * 设置响应头
     * @access public
     * @param string $name 参数名
     * @param string $value 参数值
     * @return void
     */
    public static function header($name, $value)
    {
        header($name . ':' . $value);
    }
}
