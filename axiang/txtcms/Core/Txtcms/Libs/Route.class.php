<?php
/**
 * TXTCMS 路由映射
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-28
 */
class Route{
	protected $options   =  array(
        'URL_ROUTER_ON'         => false,   // 是否开启URL路由
        'URL_ROUTE_RULES'       => array(), // 默认路由规则
        );
	//路由检测
	public function check(){
		$regx = trim($_SERVER['QUERY_STRING'],'/');
		if(empty($regx)) return true;
		if(!config('URL_ROUTER_ON')) return false;
		$routes=config('URL_ROUTE_RULES');
		if(!empty($routes)) {
			$depr=config('URL_PATH_DEPR');
			// 分隔符替换,统一分隔符
			//$regx = str_replace($depr,'/',$regx);
			$regx = rtrim($regx,'.'.config('URL_PATH_SUFFIX'));
			foreach ($routes as $rule=>$route){
				if(0===strpos($rule,'/') && preg_match($rule,$regx,$matches)) { // 正则路由
					//解析正则
                    return $this->parseRegex($matches,$route,$regx);
                }
			}
		}
		return false;
	}
	// 解析正则路由
	private function parseRegex($matches,$route,$regx) {
		// 获取路由地址规则
        $url   =  is_array($route)?$route[0]:$route;
        $url   =  preg_replace('/:(\d+)/e','$matches[\\1]',$url);
        if(0=== strpos($url,'/') || 0===strpos($url,'http')) { // 路由重定向跳转
            header("Location: $url", true,(is_array($route) && isset($route[1]))?$route[1]:301);
            exit;
        }else{
            // 解析路由地址
            $var  =  $this->parseUrl($url);
            // 解析剩余的URL参数
            $regx =  substr_replace($regx,'',0,strlen($matches[0]));
            if($regx) {
                preg_replace('@(\w+)\/([^,\/]+)@e', '$var[strtolower(\'\\1\')]=strip_tags(\'\\2\');', $regx);
            }
            // 解析路由自动传入参数
            if(is_array($route) && isset($route[1])) {
                parse_str($route[1],$params);
                $var   =   array_merge($var,$params);
            }
            $_GET   =  array_merge($var,$_GET);
        }
        return true;
	}
	// 解析规范的路由地址
    // 地址格式 [分组/模块/操作?]参数1=值1&参数2=值2...
    private function parseUrl($url) {
        $var  =  array();
        if(false !== strpos($url,'?')) { // [分组/模块/操作?]参数1=值1&参数2=值2...
            $info   =  parse_url($url);
            $path   = explode('/',$info['path']);
            parse_str($info['query'],$var);
        }elseif(strpos($url,'/')){ // [分组/模块/操作]
            $path = explode('/',$url);
        }else{ // 参数1=值1&参数2=值2...
            parse_str($url,$var);
        }
        if(isset($path)) {
            $var[config('ACTION_VAR')] = array_pop($path);
            if(!empty($path)) {
                $var[config('MODULE_VAR')] = array_pop($path);
            }
            if(!empty($path)) {
                $var[config('GROUP_VAR')]  = array_pop($path);
            }
        }
        return $var;
    }
}