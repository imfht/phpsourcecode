<?php
/**
 * TempLi 控制器基类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-1-20
 */

namespace framework\core;

abstract class Controller
{
    /** @var Application */
    protected $app = null;
    public function __construct()
    {
        $this->app = \Templi::getApp();
        if(method_exists($this,'init'))
            $this->init();
    }

    /**
     * 获取当前 控制器名
     * @return string
     */
    protected function getControllerName()
    {
        return get_class($this);
    }

    /**
     * ajax 返回
     * @param array $data
     */
    protected function ajaxRespond($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($data);
    }
    /**
     * 魔术方法 有不存在的操作的时候执行
     * @param string $action
     * @param mixed $param
     * @throws \framework\core\Abnormal
     */
    public function __call($action, $param)
    {
        if(method_exists($this,'_empty')){
            $this->_empty($action, $param);
        }else{
            throw new Abnormal($action.' 方法不存在', 500);
        }
    }

}