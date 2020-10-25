<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: View.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  视图
 * =============================================================================                   
 */

namespace core;

require ROOT . DS . 'smarty' . DS . 'Smarty.class.php';

class View extends \Smarty
{

    function __construct()
    {
        parent::__construct();

        $template_dir = [
        ];
        //分组目录

        $module = Config::get('modules');
        $has_module = empty($module[App::getRouter()->getModule()]) ? null : $module[App::getRouter()->getModule()];
        //别名处理
        if (is_array($has_module) && isset($has_module['module'])) {
            //子模块模板目录（一般用于后台）
            if (App::getRouter()->getModuleChild()) {
                $template_dir[] = ROOT . DS . 'modules' . DS . $has_module['module'] . DS . MODULE_CHILD . DS . 'views' . DS;
            }
            $template_dir[] = ROOT . DS . 'modules' . DS . $has_module['module'] . DS . 'views' . DS;
        } else {
            $template_dir[] = ROOT . DS . 'modules' . DS . App::getRouter()->getModule() . DS . 'views' . DS;
        }
        $template_dir[] = ROOT . DS . 'views' . DS;
        $this->setTemplateDir($template_dir);
        $this->setCompileDir(ROOT . DS . 'runtime' . DS . 'template_c' . DS);
        $this->setCacheDir(ROOT . DS . 'runtime' . DS . 'cache' . DS);
        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';
        $this->caching = \Smarty::CACHING_OFF;
        $this->registerClass("Router", "\\core\\Router");
        $this->registerClass("Tool", "\\core\\Tool");
        $this->assign('Config', Config::get());
    }

}
