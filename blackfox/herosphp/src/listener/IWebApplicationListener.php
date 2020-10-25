<?php
/**
 * 应用程序生命周期监听器接口
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\listener;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;

interface IWebApplicationListener {

    /**
     * 请求初始化之前
     * @return mixed
     */
    public function beforeRequestInit();

    /**
     * action 方法调用之前
     * @return mixed
     */
    public function beforeActionInvoke(HttpRequest $request);

    /**
     * 响应发送之前
     * @param \herosphp\core\Controller $actionInstance
     * @return mixed
     */
    public function beforeSendResponse(HttpRequest $request, Controller $actionInstance);

    /**
     * 响应发送之后
     * @param \herosphp\core\Controller $actionInstance
     * @return mixed
     */
    public function afterSendResponse(Controller $actionInstance);

}
