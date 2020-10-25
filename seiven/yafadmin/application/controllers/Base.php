<?php
/**
 * 基础Controller
 * @author user
 *
 */
class BaseController extends Yaf_Controller_Abstract {
    public $request;
    public $response;
    public $view;
    public $session;
    public $config;
    // 自动执行
    function init(){
        $this->view = $this->getView();
        // 设置Controller的模板位置为模块目录下的views文件夹
        $this->setViewpath(APPLICATION_PATH . '/application/modules/' . $this->getModuleName() . '/views');
        $this->initView();
        // 各模块静态文件位置
        $this->view->assign('static', '/static/' . strtolower($this->getModuleName()));
        $this->request = $this->getRequest();
        $this->session = Yaf_Session::getInstance();
        $this->response = $this->getResponse();
        //
        $this->view->assign('controller', $this->request->getControllerName());
        $this->view->assign('action', $this->request->getActionName());
        $this->config = Yaf_Registry::get('config');
    }
}