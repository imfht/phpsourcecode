<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


class Application
{
    /**
     * @var string 控制器名
     */
    protected static $controller;

    /**
     * @var string 动作名称
     */
    protected static $action;

    /**
     * @var string 参数
     */
    protected static $params;

    /**
     * @var Container 依赖注入容器
     */
    protected static $container;

    /**
     * 设置控制器、动作、参数、服务容器
     *
     * @param string $controller
     * @param string $action
     * @param array $params
     */
    public static function iniSet($controller, $action, $params)
    {
        static::$controller = $controller;
        static::$action = $action;
        static::$params = $params;
    }

    /**
     * 获取控制器名
     *
     * @return string
     */
    public static function controller()
    {
        return static::$controller;
    }

    /**
     * 获取动作名
     *
     * @return string
     */
    public static function action()
    {
        return static::$action;
    }

    /**
     * 获取IOC/DI容器
     *
     * @return Container
     */
    public static function di()
    {
        if (is_null(static::$container)) {
            static::$container = new Container();
            static::$container->instance(['Timo\Core\Container' => 'di'], static::$container);
        }
        return static::$container;
    }

    /**
     * 返回一个数组或JSON字符串
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @param bool $json_encode
     * @return array|string
     */
    public static function result($code = 1, $msg = '', $data = null, $json_encode = false)
    {
        $result = ['code' => $code, 'msg' => $msg];
        if (!is_null($data)) {
            $result['data'] = $data;
        }

        if ($json_encode) {
            $result = json_encode($result);
        }

        return $result;
    }
}
