<?php
/**
 * Windows兼容插件
 *
 * @author chengxuan <i@chengxuan.li>
 */

class IisPlugin extends Yaf_Plugin_Abstract {

	/**
	 * 路由分发开始
	 *
	 * @see Yaf_Plugin_Abstract::routerShutdown()
	 */
	public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		//IIS不支持默认路由，采用URL Query String方式路由
		$router = Yaf_Dispatcher::getInstance()->getRouter();
		$route = new Yaf_Route_Simple('m', 'c', 'a');
		$router->addRoute('Windows-IIS-Supervar', $route);
	}
	
	/**
	 * 路由分发结束
	 * {@inheritDoc}
	 * @see Yaf_Plugin_Abstract::routerShutdown()
	 */
	public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		$request->controller = str_replace('/', '_', $request->controller);
	}
}
