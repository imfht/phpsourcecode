 <?php 
 /** ***********************
 * 作者：卢逸 www.61php.com
 * 日期：2015/5/21
 * 作用：合法验证静态类
 ** ***********************/
class verify{
	static function check(&$get){
		global $path_module;
		//默认的方法
		if (!$get['m']) $get['m']="home";
		if (!$get['o']) $get['o']="core";
		if (!$get['t']) $get['t']="index";
		//设定模块目录
		$path_module = _SITE_APPLICATION_PATH . "program".DS.$get['m']. DS."com_" .$get['o'].DS;

		//检查模块控制部分是否存在
		$path_controller=$path_module."controller.php";
		
		if (!file_exists($path_controller)) {
			die(_lang_no_controller_file);
		}
		//检查view部分是否存在
		$path_view=$path_module."view.php";
		if (!file_exists($path_controller)) {
			die(_lang_no_view_file);
		}

		//引用控制与视图部分
		require_once $path_controller;
		require_once $path_view;
		//实例化控制类
		$className="{$get['o']}Controller".ucfirst($get['m']);
		
		if (!class_exists($className)){
			die(_lang_no_controller);
		}
		$controller=new $className();
		
		$view=$controller->executeView();
		//view执行
		$do=$get['t'];
		
		if (!method_exists($view,$do)){
			die(_lang_no_view_function);
		}

		$view->$do();
	}
}
 ?>