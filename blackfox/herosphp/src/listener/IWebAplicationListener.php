<?php
/**
 * 应用程序生命周期监听器接口
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\listener;

use herosphp\http\HttpRequest;

interface IWebAplicationListener {

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
     * 在控制器的住方法调用之后无论如何也会调用的，比如在控制器调用之后直接die掉，
     * 返回json视图，这样 beforeSendResponse()这个监听器是无法捕获的
     * @param HttpRequest $request
     * @param \herosphp\core\Controller $actionInstance
     * @return mixed
     */
    public function actionInvokeFinally(HttpRequest $request, $actionInstance);

    /**
     * 响应发送之前
     * @param \herosphp\core\Controller $actionInstance
     * @return mixed
     */
    public function beforeSendResponse(HttpRequest $request, $actionInstance);

    /**
     * 响应发送之后
     * @param \herosphp\core\Controller $actionInstance
     * @return mixed
     */
    public function afterSendResponse($actionInstance);

}
