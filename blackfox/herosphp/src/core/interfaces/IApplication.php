<?php
/**
 * 应用程序接口类，定义应用程序的生命周期
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\core\interfaces;

interface IApplication {

    /**
     * 请求初始化
     * @return void
     */
    public function requestInit();

    /**
     * action当前访问的操作方法调用
     * @return void
     */
    public function actionInvoke();

    /**
     * 发送响应
     * @return void
     */
    public function sendResponse();
} 