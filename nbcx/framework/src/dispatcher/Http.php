<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\dispatcher;

use nb\Config;
use nb\Request;
use nb\Response;
use nb\Router;
use nb\Pool;

/**
 * Swoole
 *
 * @package nb\dispatcher
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Http extends Php {

    protected $config = [
        'path'=>__APP__,
        'allow'=>'ico|css|js|jpg|png',
        'expire'=>1800,
        'enable'=>true
    ];

    public function __construct($config = []) {
        $this->config = array_merge($this->config,$config);
    }

    public function run() {
        $this->doStatic() || $this->doWith();
    }

    /**
     * 处理业务请求
     * @return mixed
     * @throws \ReflectionException
     */
    private function doWith() {
        //判断是否为模块绑定
        $module = Config::$o->module_bind;
        $router = Router::driver();
        if($module && isset($module[$host = Request::driver()->host])) {
            $this->module($module[$host]);
            $router = $router;
            $router->module = $module[$host];
            $router->mustAnalyse();
        }
        else {
            //判断是否为绑定模块
            $router->mustAnalyse();
            //如果访问的模块，加载模块配置
            if($router->module) {
                $this->module($router->module);
            }
        }

        //如果请求的Action为Debug，则打开debug页面
        switch ($router->controller) {
            case 'debug':
                $this->debug($router);
                break;
            default :
                //如果加载不成功，作为404处理
                //过滤掉禁止访问的方法
                $class = $router->class;//$this->load($router);
                if(!$class || in_array($router->function,Config::$o->notFunc)) {
                    return Pool::object('nb\\event\\Framework')->notfound();
                }
                //过滤掉禁止访问的方法
                //if (in_array($router->function,Config::$o->notFunc)) {
                //    return Pool::object('nb\\event\\Framework')->notfound();
                //}
                $this->go($class,$router->function);
                break;
        }
    }

    /**
     * 处理资源请求
     *
     * @return bool|void
     */
    private function doStatic() {
        $conf = $this->config;

        if(!$conf['enable']) {
            return false;
        }

        $ext = Request::driver()->ext;
        if(!$ext || false === strpos($conf['allow'], $ext)) {
            return false;
        }

        $request  = Request::driver();
        $response = Response::driver();

        $path = $conf['path'] . $request->pathinfo;
        if (!is_file($path)) {
            $response->status(404);
            return true;
        }
        $expire = $conf['expire'];
        if ($expire) {
            $fstat = stat($path);
            //过期控制信息
            if (isset($request->header['if-modified-since'])) {
                $lastModifiedSince = strtotime($request->header['if-modified-since']);
                if ($lastModifiedSince and $fstat['mtime'] <= $lastModifiedSince) {
                    //不需要读文件了
                    $response->status(304);
                    return true;
                }
            }
        }
        $response->header('Cache-Control',"max-age={$expire}");
        $response->header('Pragma',"max-age={$expire}");
        $response->header('Last-Modified',date('D, d-M-Y H:i:s T', $fstat['mtime']));
        $response->header('Expires',"max-age={$expire}");
        $response->header('Content-Type',Response::$mimes[$ext]);
        echo file_get_contents($path);
        return true;

    }


    protected function module($module) {
        $conf = Config::$o;
        $conf_file = __APP__.$conf->folder_module.DS.$module.DS.'config.inc.php';

        if(is_file($conf_file)) {
            $config = include $conf_file;
            if(isset($config['path_autoinclude'])) {
                $path_autoext = isset($config['path_autoext'])?$config['path_autoext']:$conf->path_autoext;
                $conf->import(
                    $config['path_autoinclude'],
                    $path_autoext
                );
                unset($config['path_autoinclude']);
            }
            if(isset($config['view'])) {
                Config::setx_merge('view',$config['view']);
                unset($config['view']);
            }

            foreach ($config as $k=>$v) {
                $conf->$k = $v;
            }
        }
        else {
            $conf->import(
                [__APP__.$conf->folder_module.DS.$module.DS.'include'.DS],
                $conf->path_autoext
            );
        }
        return true;
    }
}