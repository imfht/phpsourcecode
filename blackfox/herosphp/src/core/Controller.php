<?php
/**
 * 控制器抽象基类, 所有的控制器类都必须继承此类。
 * 每个操作对应一个方法。
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\core;

abstract class Controller extends Template {

    /**
     * 视图模板名称
     * @var string
     */
    private $view = null;

	/**
     * 控制器初始化方法，每次请求必须先调用的方法，action子类可以重写这个方法进行页面的初始化
	 */
	public function C_start() {}

    /**
     * 设置视图模板
     * @param       string      $view      模板名称
     */
    public function setView( $view ) {
        $this->view = $view;
    }

    /**
     * 获取视图
     * @return string
     */
    public function getView() {
        return $this->view;
    }

    //析够函数
    public function __destruct()
    {
        $liseners = WebApplication::getInstance()->getListeners();
        //调用响应发送后生命周期监听器
        if ( !empty($liseners) ) {
            foreach ( $liseners as $listener ) {
                $listener->actionInvokeFinally(WebApplication::getInstance()->getHttpRequest(), $this);
            }
        }
    }

}