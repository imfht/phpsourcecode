<?php
defined('IN_TEMPLI') or die('非法引用');
/**
 * TempLi 控制器基类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-1-20
 */
abstract class Controller
{
    /** @var View  */
    private $view = null;
    /** @var  Loader */
    protected $load = null;
    /** @var Application */
    protected $app = null;
    function __construct()
    {
        $this->app = Templi::getApp();
        $this->load = $this->app->load;
        $this->view = new View();
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
     * 获取当前模块名
     */
    protected function getModuleName()
    {
        return Templi::getApp()->getModuleName();
    }

    /**
     * 获取当前 操作 方法名
     */
    protected function getActionName()
    {
        return Templi::getApp()->getActionName();
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
        if (is_null($file) || is_null($dir)) {
            //$backtrace = debug_backtrace($provide_object = DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit = 2);
            $backtrace = debug_backtrace($provide_object = DEBUG_BACKTRACE_PROVIDE_OBJECT);
            $controllerPath = $backtrace[0]['file'];
            $dir = str_replace($this->app->getConfig('app_path').'controller', '',dirname($controllerPath));
            if (empty($file)) {
                $controller = substr($backtrace[1]['class'], 0, -10);
                $action = $backtrace[1]['function'];
                $file = $controller.'_'.$action;
            }
        }
        if (empty($dir)) {
            $path = '';
        } else {
            $path = $dir.'/';
        }
        $this->view->display($path.$file);
    }

    /**
     * 获取模板渲染后的内容
     * @param string $file
     * @param null $dir
     * @return string
     */
    protected function render($file, $dir=NULL)
    {
        if (is_null($dir)) {
            $backtrace = debug_backtrace($provide_object = DEBUG_BACKTRACE_PROVIDE_OBJECT);
            //$backtrace = debug_backtrace($provide_object = DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit = 2);
            $controllerPath = $backtrace[0]['file'];
            $dir = str_replace($this->app->getConfig('app_path').'controller', '',dirname($controllerPath));
        }
        $path = '';
        if (!empty($dir)) {
            $path = $dir.'/';
        }
        return $this->view->render($path.$file);
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
     * 魔术方法 有不存在的操作的时候执行
     * @param string $action
     * @param mixed $param
     * @throws Abnormal
     */
    function __call($action, $param)
    {
        if(method_exists($this,'_empty')){
            $this->_empty($action, $param);
        }else{
	        if(APP_DEBUG){
                throw new Abnormal($action.' 方法不存在', 500);
            } else {
                show_404();
            }
        }
    }
}