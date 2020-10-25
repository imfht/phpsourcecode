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
use nb\Pool;
use nb\Validate;

/**
 * Driver
 *
 * @package nb\dispatcher
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
abstract class Driver {

    /**
     * 当前请求的参数源
     * @var array
     */
    public $input = [];

    /**
     * 当前控制器需要的参数
     * @var array
     */
    public $params = [];

     /**
     * 启动调度器
     * @return mixed
     */
    abstract public function run();

    /**
     * 本次调度控制器的数据来源
     * @param \ReflectionClass $controller
     * @param $app
     * @return mixed
     */
    abstract protected function input(\ReflectionClass $controller, $app);

    /**
     * 模块调度
     *
     * @param $module
     * @return bool
     */
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
                //Config::setx($k,$v);
            }
        }
        else {
            //$conf->view['view_path'] = __APP__.$conf->module.DS.$module.DS.'view'.DS;
            $conf->import(
                [__APP__.$conf->folder_module.DS.$module.DS.'include'.DS],
                $conf->path_autoext
            );
        }
        return true;
    }

    /**
     * 根据控制器名字和方法名来执行控制器
     *
     * @param $controller
     * @param $function
     * @throws Exception
     * @throws \ReflectionException
     */
    public function go($controller, $function) {
        $controller = new \ReflectionClass($controller);

        //创建当前控制器对象，并放入池子
        $app = Pool::value('controller',$controller->newInstance());

        //获取此次请求的参数
        $this->input = $this->input($controller,$app);

        $pubparams = [];
        $scene = [];
        $_before_argsn = [];
        $_function_argsn = [];

        if ($_hasbefore = $controller->hasMethod('__before')) {
            $_before = new \ReflectionMethod($app, '__before');
            $_before_argsn = $_before->getNumberOfParameters();
            if($_before_argsn>0) {
                $args =  $_before->getParameters();
                $this->verification($args,$pubparams,$scene, $controller, $app);
            }
        }

        if ($_hasfunction = $controller->hasMethod($function)) {
            $_function = new \ReflectionMethod($app, $function);
            $_function_argsn = $_function->getNumberOfParameters();
            if($_function_argsn>0) {
                $args =  $_function->getParameters();
                $this->verification($args,$this->params,$scene, $controller, $app);
            }
        }

        $scene = array_unique($scene);
        $param = array_unique(array_merge($pubparams,$this->params));

        $validate = $this->validate($controller,$app);

        //将控制器专属的验证器放入对象池
        Pool::set(Validate::class,$validate);

        if($validate && ($_before_argsn || $_function_argsn) ) {
            $result = $validate->scene($function, $scene)->check($param);
            if(!$result) {
                if ($controller->hasMethod('__error')) {
                    return $app->__error($validate->error,$validate->field);
                }
                return Pool::object('nb\event\Framework')->validate(
                    $validate->error,
                    $validate->field
                );
            }
        }

        //判断用户是否构建了__before方法,如果构建，则只有__before为true，才进行处理
        $_hasbefore = $_hasbefore?call_user_func_array([$app,'__before'], $pubparams):true;
        $return = null;
        if ($_hasbefore) {
            if($_hasfunction) {
                if (!$_function->isPublic() || $_function->isStatic()) {
                    Pool::object('nb\event\Framework')->notfound();
                    return;
                }
                $params = $this->params?:[];
                $return = call_user_func_array([$app,$function],$params);

            }
            else {
                return Pool::object('nb\event\Framework')->notfound();
            }
        }

        //判断用户是否构建了__after方法,如果构建，则执行
        if ($controller->hasMethod('__after')) {
            $app->__after($return);
        }
    }


    /**
     * 创建验证器对象
     * @return Validate
     */
    private function validate($controller,$app) {
        $rule = [];
        if($controller->hasProperty('_rule')) {
            $rule = $app->_rule;
        }

        $validate = null;
        if($controller->hasProperty('_validate')) {
            $validate = $app->_validate;
        }
        else {
            $rule and $validate = Validate::class;
        }

        if($validate) {
            $message = $controller->hasProperty('_message')?$app->_message:[];
            $validate = new $validate($rule,$message);
        }

        return $validate;
    }

    /**
     * 包装需要验证的参数
     * @param $args
     * @param $param
     * @param $scene
     * @throws Exception
     */
    private function verification($args,&$param,&$scene, $r, $app){
        foreach ($args as $v) {
            $scene[] = $v->name;

            if(isset($this->input[$v->name])){
                if(is_array($this->input[$v->name])) {
                    $param[$v->name] = $this->input[$v->name];
                }
                else if(strlen($this->input[$v->name])>0) {
                    $param[$v->name] = $this->input[$v->name];
                }
                else if($v->isDefaultValueAvailable()) {
                    $param[$v->name] = $v->getDefaultValue();
                }
                else {
                    if ($r->hasMethod('__error')) {
                        $app->__error("{$v->name}参数为必须参数!",$v->name);
                    }
                    else {
                        Pool::object('nb\event\Framework')->validate(
                            "{$v->name}参数为必须参数!",
                            $v->name
                        );
                    }
                    quit();
                }
            }
            else if($v->isDefaultValueAvailable()) {
                $param[$v->name]=$v->getDefaultValue();
            }
            else {
                if ($r->hasMethod('__error')) {
                    $app->__error("{$v->name}参数为必须参数!",$v->name);
                }
                else {
                    Pool::object('nb\event\Framework')->validate(
                        "{$v->name}参数为必须参数!",
                        $v->name
                    );
                }
                quit();
            }
        }
    }

}