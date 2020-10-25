<?php

/**
 * Controller.php
 * @author: liyongsheng
 * @email： liyongsheng@huimai365.com
 * @date: 2015/6/10
*/

namespace framework\web;
use framework\core\Controller as BaseController;
use framework\core\View,
    framework\core\Abnormal,
    framework\core\Common;

/**
 * Class Controller
 * @package framework\web
 */
class Controller extends BaseController
{
    /** @var View  */
    private $view = null;
    public function __construct()
    {
        parent::__construct();
        $this->view = new View();
    }
    /**
     * 给模板文件分配变量
     * @param string $name 变量名称
     * @param mixed $value 变量值
     */
    protected function assign($name, $value='')
    {
        $this->view->assign($name, $value);
    }
    /**
     * 给模板文件 批量分配变量
     * @param array $data 变量名称
     */
    protected function setOutput($data)
    {
        $this->view->setOutput($data);
    }

    /**
     * 显示 视图
     * @param string $file 模板文件名称
     * @param null $dir
     */
    protected function display($file=NULL, $dir=NULL)
    {
        $this->view->display($this->_createFileName($file, $dir));
    }
    /**
     * 获取模板渲染后的内容
     * @param string $file
     * @param null $dir
     * @return string
     */
    protected function render($file, $dir=NULL)
    {
        return $this->view->render($this->_createFileName($file, $dir));
    }
    /**
     * 消息提示页
     * @param string $msg 消息内容
     * @param null $url_forward
     * @param int $ms 等待时间
     * @param string $module
     */
    protected function showMessage($msg, $url_forward=null, $ms=null, $module='index')
    {
        $data['url_forward'] = $url_forward?APP_URL.$url_forward:'goback';
        $data['ms'] = $ms?$ms:1250;
        $data['msg'] =$msg;
        $this->setOutput($data);
        $this->view->display($module.'/showmessage');
        die;
    }
    /**
     * 生成文件名
     * @param string $file
     * @param string $dir
     * @return array
     */
    private function _createFileName($file=NULL, $dir=NULL)
    {
        if (is_null($file) || is_null($dir)) {
            $backtrace = debug_backtrace($provide_object = DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit = 3);
            $controllerPath = pathinfo($backtrace[1]['file']);
            if(empty($dir)){
                $dir = str_replace(\Templi::getApp()->appPath.'controllers', '', $controllerPath['dirname']);
            }
            $controller = $controllerPath['filename'];
            $action = lcfirst(substr($backtrace[2]['function'], 6));
            $file = $controller.'_'.$action;
        }
        $dir = rtrim($dir, '/').'/';
        return $dir.$file;
    }
    /**
     * 魔术方法 有不存在的操作的时候执行
     * @param string $action
     * @param mixed $param
     * @throws Abnormal
     */
    public function __call($action, $param)
    {
        if(method_exists($this,'_empty')){
            $this->_empty($action, $param);
        }else{
            if(APP_DEBUG){
                throw new Abnormal($action.' 方法不存在', 500);
            } else {
                Common::show404();
            }
        }
    }
}