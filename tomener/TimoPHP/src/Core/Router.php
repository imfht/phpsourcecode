<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;
use Timo\Exception\CoreException;

class Router
{
    /**
     * 控制器名称
     *
     * @var string
     */
    private $controller;

    /**
     * 操作名称
     *
     * @var string
     */
    private $action;

    /**
     * @var array
     */
    private $param = [];

    /**
     * 初始化router
     *
     * Router constructor.
     */
    function __construct()
    {
        if (!IS_CLI) {
            $this->parse();
        } else {
            $this->parseCli();
        }
    }

    /**
     * 解析路由
     *
     * @return bool
     * @throws CoreException
     */
    private function parse()
    {
        $param = [];

        if (isset($_GET['r'])) {
            $_SERVER['PATH_INFO'] = $_GET['r'];
        }
        self::parseModule();
        self::loadAppConfig();

        $url = Config::runtime('url');
        $controller = $url['c'];
        $action = $url['a'];

        $path = !isset($_SERVER['PATH_INFO']) ? '' : trim(trim($_SERVER['PATH_INFO'], $url['ext']), '/');
        if (!empty($path)) {
            $router_config = Config::runtime('router.rules');
            if (is_array($router_config)) {
                foreach ($router_config as $key => $value) {
                    if (!strpos($path, '/')) {
                        continue;
                    }
                    $path = str_replace($key, $value, $path);
                }
            }
            $param = explode('/', $path);

            $router_mode = Config::runtime('router.mode');
            if ($router_mode == 1) {
                $len = count($param);
                if ($len > 1) {
                    $controller_arr = array_slice($param, 0, $len - 1);
                    $controller_arr[$len - 2] = ucfirst($controller_arr[$len - 2]);
                    $controller = implode('\\', $controller_arr);
                    $action = $param[$len - 1];
                } else {
                    $controller = ucfirst($param[0]);
                }
                $param = [];
            } else {
                !empty($param[0]) && $controller = $param[0];
                isset($param[1]) && $action = $param[1];
                $controller = ucfirst($controller);
                $param = array_slice($param, 2);
            }
        }

        $this->controller = $controller;
        $this->action = $action;
        $this->param = $param;
        return true;
    }

    /**
     * CLI模式解析路由
     *
     * @return bool
     * @throws CoreException
     */
    private function parseCli()
    {
        self::loadAppConfig();

        $param = [];
        $url = Config::runtime('url');
        $controller = $url['c'];
        $action = $url['a'];

        $argv = $_SERVER['argv'];
        if (isset($argv[1])) {
            $router = explode('/', $argv[1]);
            $controller = isset($router[0]) ? $router[0] : $controller;
            $action = isset($router[1]) ? $router[1] : $action;
        }
        array_shift($argv);
        array_shift($argv);

        if (count($argv) > 0) {
            foreach ($argv as $value) {
                $temp = explode('=', $value);
                $param[$temp[0]] = $temp[1];
            }
        }

        $this->controller = ucfirst($controller);
        $this->action = $action;
        $this->param = $param;
        return true;
    }

    /**
     * 返回控制器名称
     *
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * 返回action名称
     *
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * 模块解析
     *
     * @return bool
     * @throws CoreException
     */
    private static function parseModule()
    {
        $project_mode = Config::runtime('project.mode');
        $app = Config::runtime('default_app');
        switch ($project_mode) {
            case 0:
                break;
            case 1:
                $modules = Config::runtime('apps');
                if ($modules && isset($_SERVER['PATH_INFO'])) {
                    $path = explode('/', $_SERVER['PATH_INFO']);
                    if (isset($path[1]) && isset($modules[$path[1]])) {
                        $app = $modules[$path[1]];
                        $_SERVER['PATH_INFO'] = str_replace('/' . $path[1], '', $_SERVER['PATH_INFO']);
                    }
                }
                break;
            case 2:
                $version = Request::getHeaders('Version');
                if (!$version) {
                    throw new CoreException("need version Header");
                }
                $temp = explode('.', $version);
                if (count($temp) > 1) {
                    $app = $temp[0] . '\\' . implode('_', $temp);
                } else {
                    $app = $temp[0];
                }
                break;
        }
        defined('APP_NAME') || define('APP_NAME', $app);
        return true;
    }

    /*
     * 加载应用配置
     */
    private static function loadAppConfig()
    {
        $app_config = APP_DIR_PATH . APP_NAME . DS . 'config.php';
        if (file_exists($app_config)) {
            Config::load($app_config, 'runtime');
        }
    }
}
