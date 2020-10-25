<?php
namespace app\member\controller;

use app\common\controller\IndexBase;

use app\common\model\Plugin as PluginModel;

class Plugin extends IndexBase
{
	protected $model;
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new PluginModel();
	}

	/**
	 * 执行插件内部方法
	 * @return mixed
	 */
	public function execute()
	{
	    $plugin     = input('param.plugin_name');
	    $controller = input('param.plugin_controller');
	    $action     = input('param.plugin_action');
	    define('IN_PLUGIN', $plugin .'#' . $controller .'#' . $action);

	    //参数优先级为 POST>GET>ROUTE
	    $get_params = input('get.');
	    $post_params = input('post.');
	    $params     = $this->request->except(['plugin_name', 'plugin_controller', 'plugin_action'], 'route');
	    if($get_params){
	        $params = $params?array_merge($params,$get_params):$get_params;
	    }
	    if($post_params){
	        $params = $params?array_merge($params,$post_params):$post_params;
	    }
	    
	    if (empty($plugin) || empty($controller) || empty($action)) {
	        $this->error('没有指定插件名称、控制器名称或操作名称');
	    }
	    
	    if (!plugin_action_exists($plugin, $controller, $action)) {
	            $this->error("找不到类及方法：plugins\\{$plugin}\\member\\".format_class_name($controller));
	    }
	    return plugin_action($plugin, $controller, $action, $params);
	}

}
