<?php

/**
 * 控制器抽象类
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
abstract class AbsController extends Yaf_Controller_Abstract {
    
    /**
     * 是否需要登录（默认需要）
     * 
     * @var Boolean
     */
    protected $_need_login = true;

    /**
     * (non-PHPdoc)
     * @see Yaf_Controller_Abstract::init()
     */
    public function init() {
        //判断用户是否登录
        if($this->_need_login && !Yaf_Registry::get('current_uid')) {
            throw new \Exception\Nologin('no login');
        }
    }
    
    /**
     * 渲染模板
     * 
     * @param array  $assign   模板变量
     * @param string $tpl_path 模板路径（不填则是当前Controller）
     */
    public function viewDisplay(array $assign = array(), $tpl_path = null) {
        $view = new \Comm\View();
        $tpl_name = $this->getRequest()->getControllerName();
        if($tpl_path) {
            $tpl_path .= '.phtml';
        } else {
            $tpl_path = strtolower(str_replace('_', '/', $tpl_name)) . '.phtml';
        }
        $view->display($tpl_path, $assign);
    }
    
} 