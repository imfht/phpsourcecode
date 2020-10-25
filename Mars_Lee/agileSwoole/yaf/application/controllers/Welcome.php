<?php
/**
 * Created by weibo.com
 * User: wenlong11
 * Date: 2018/9/20
 * Time: 上午11:39
 */

class WelcomeController extends Yaf_Controller_Abstract
{
    public $_req;
    public function indexAction()
    {
        $this->view(['title'=>'hello world!']);
    }


    /**
     * 通用初始化方法
     *
     * {@inheritDoc}
     * @see Yaf_Controller_Abstract::init()
     */
    public function init() {
        $this->_req = $this->getRequest()->getParam('request');
        $this->_response = $this->getRequest()->getParam('response');
    }

    /**
     * 展示模板
     *
     * @param array  $tpl_vars
     * @param string $tpl_path
     *
     * @return void
     */
    public function view($tpl_vars = array(), $tpl_path = null) {
        $view = new \Comm\View($this->_response, $this->_req);
        if(!$tpl_path) {
            $tpl_name = $this->getRequest()->getControllerName();
            $tpl_path = strtolower(str_replace('_', '/', $tpl_name));
        }
        $tpl_path .= '.phtml';
        $view->display($tpl_path, $tpl_vars);
    }

    /**
     * 获取展示对象
     *
     * @return \Comm\View
     */
    protected function _getViewObject() {
        return new \Comm\View($this->_response, $this->_req);
    }

    /**
     * GET获取一条数据
     *
     * @param string $offset  KEY
     * @param mixed  $default 默认值
     *
     * @return mixed
     */
    protected function _get($offset, $default = null) {
        $get = isset($this->_req->get) ? $this->_req->get : array();
        return isset($get[$offset]) ? $get[$offset] : $default;
    }

    /**
     * POST获取一条数据
     *
     * @param string $offset  KEY
     * @param mixed  $default 默认值
     *
     * @return mixed
     */
    protected function _post($offset, $default = null) {
        $get = isset($this->_req->post) ? $this->_req->post : array();
        return isset($get[$offset]) ? $get[$offset] : $default;
    }

    /**
     * Location重定向一个地址
     *
     * @param string $url  要跳转的URL
     * @param number $code HTTP状态码
     *
     * @return void
     */
    protected function _location($url, $code = 302) {
        \Kernel\Core\Mime\Response::location($this->_response, $url, $code);
    }

}