<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 路由类
*/

defined('INPOP') or exit('Access Denied');

class Router{

	public function __construct(){}

	//获取HTTP路由
	public static function get(){
		if(isset($_SERVER['REQUEST_URI'])){
			$query_string = $_SERVER['REQUEST_URI'];
		}else{
			if(isset($_SERVER['argv'])){
				$query_string = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
			}else{
				$query_string = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
			}
		}
		//无重写情况下过滤入口
		$query_string = str_replace($_SERVER['SCRIPT_NAME'], '',$query_string);
		$temp = explode(ROUTER_TYPE, $query_string);
		//删除第一个元素，即根目录对应的路由
		array_shift($temp);
        $default_control = DEFAULT_END."_".DEFAULT_CONTROL;
		if(empty($temp[0])) return array('control' => $default_control,'action' => DEFAULT_ACTION);
		if(empty($temp[1])) $temp[] = 'index';
		//去除空项
		foreach($temp as $val){
			if($val) $url[] = $val;
		}
		list($control, $action) = $url;
		$control_array = explode("_", $control);
		if(!$control_array[1]) $control = DEFAULT_END."_".$control;
		//有参数的情况
		$params = array();
		if(count($url)>2){
			array_shift($url);
			array_shift($url);
			$params = $url;
		}
		return array("control" => $control, "action" => $action, "params" => $params,);
	}
	
	//解析内部路由
	public static function analysis($name){
		if(!$name) return false;
		//名字格式是控制器名+ROUTER_TYPE+动作名
		$name_array = explode(ROUTER_TYPE, $name);
		$control_name = $name_array[0];
		$action_name = $name_array[1] ? $name_array[1] : 'index';
		$return['control'] = $control_name;
		$return['action'] = $action_name;
		return $return;	
	}

}
?>