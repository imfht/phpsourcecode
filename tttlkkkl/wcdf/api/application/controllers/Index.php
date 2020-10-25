<?php

/**
 * 默认模块，封装一些系统级别的接口
 */
class IndexController extends system\controllers\Web
{
    public function init()
    {
        parent::init();
    }

    /**
     * @return bool
     */
    public function indexAction()
    {
        $apiList = [
            ['部门:', '/member/api/department/'],
            ['部门层级列表:', '/member/api/department_level/'],
        ];
        $this->getView()->assign('apiList', $apiList);
        $this->getView()->assign('domain', Yaf\Registry::get('config')->domain->root);
    }
}
