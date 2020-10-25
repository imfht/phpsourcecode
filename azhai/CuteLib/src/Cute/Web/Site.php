<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Web;

use \Cute\Application;
use \Cute\Web\Router;
use \Cute\Web\SessionHandler;

/**
 * 网站
 */
class Site extends Application
{

    const HOSTS_SECTION = 'hosts';

    /**
     * 发送Header
     */
    public static function header($name, $value, $replace = true)
    {
        if (!headers_sent()) {
            $content = empty($name) ? '' : strval($name) . ': ';
            $content .= is_array($value) ? implode(' ', $value) : strval($value);
            @header($content, $replace);
        }
    }

    /**
     * 初始化环境
     */
    public function initiate()
    {
        parent::initiate();
        $root = Router::getCurrent();
        $this->installRef($root, ['dispatch', 'abort', 'redirect']);
        $this->installRef(Router::$current, ['route', 'expose']);
        $this->install('\\Cute\\Web\\Input', [
            'getClientIP', 'input' => 'getInstance',
        ]);
        $memory = $this->load('redis');
        $sess_handler = new SessionHandler($memory);
        if ($sess_handler->prepare()) {
            $this->installRef($sess_handler, ['setExpire', 'share', 'update']);
        }
        return $this;
    }

    /**
     * 获取当前网址对应handlers，并从后向前运行第一个拦截成功的
     */
    public function run()
    {
        $route_key = $this->getConfig('route_key', 'r');
        $path = $this->input('GET')->pop($route_key, '/');
        $route = $this->dispatch($path);
        foreach ($route['handlers'] as $handler) {
            if (empty($handler)) {
                continue;
            }
            if (is_string($handler) && class_exists($handler, true)) {
                $handler = new $handler($this, $route['method']);
            }
            if (is_callable($handler)) {
                try {
                    $output = exec_function_array($handler, $route['args']);
                } catch (\Exception $error) {
                    $route['method'] = 'except';
                    throw $error;
                }
            }
        }
        return die(strval($output));
    }

    /**
     * 使用预置的IP地址
     * @return string 域名对应备用IP地址
     */
    public function getStandbyHost($domain)
    {
        $section = $this->storage->getSectionOnce(self::HOSTS_SECTION);
        return $section->getItemInsensitive($domain, '');
    }

}
