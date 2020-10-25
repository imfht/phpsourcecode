<?php
namespace Core;
/**
 * @author shooke
 * 路由类，接收处理和输出处理url
 * 在common函数类库生成url和App初始文件的url解析中都用到
 */
class Route {
	/**
	 * 分组
	 *
	 * @var string
	 */
	public static $group = null;
	/**
	 * 模块
	 *
	 * @var string
	 */
	public static $module = null;
	/**
	 * 操作方法
	 *
	 * @var string
	 */
	public static $action = null;
	/**
	 * 参数状态，获取参数时使用,初始0
	 *
	 * @var string
	 */
	public static $status = array('groupIsDefault'=>false,'moduleIsDefault'=>false,'actionIsDefault'=>false);
	
// +----------------------------------------------------------------------
// | 下面开始是接收处理
// +----------------------------------------------------------------------
	public static function restful(){
	    $config = Config::get('URL_ROUTE_DIY');
	    if (empty($config)) reutrn ;//如果为空终止返回
	    
	}
	
	/**
	 * 原生url参数获取
	 * 0 index.php?g=group&m=module&a=action
	 * 1 ?g=group&m=module&a=action
	 */
	public static function gmaUrl(){
		//取得所有分组
		$group_dir = self::_getGroupDir();
		//设置默认值
		$group = $module = $action = '';
		//$g = $m = $a = '';
		list($g,$m,$a) = explode(',',Config::get('URL_GMA_VAR'));
		//获取原生地址变量名
		if(Config::get('GROUP_DEFAULT')){
			//获取群组
			if (Config::get('GROUP_DOMAIN')) {	//域名绑定处理
				foreach (Config::get('GROUP_DOMAIN') as $key => $domain){
					if ($domain == $_SERVER['HTTP_HOST']){
						if(in_array($key,$group_dir)){//分组目录中有对应分组
							$group = $key;//正式获得分组
						}
						break;
					}
				}
			}else{//url中有分组参数
			    //分组参数默认值标记
			    !isset($_GET[$g]) && self::$status['groupIsDefault'] = true;
			    //默认值处理
				$group = isset($_GET[$g]) ? $_GET[$g] : Config::get('GROUP_DEFAULT');				
			}			
		}
		//参数默认值标记
		!empty($_GET[$m]) && self::$status['moduleIsDefault'] = true;		
		!empty($_GET[$a]) && self::$status['actionIsDefault'] = true;
		//默认值处理
		$module = empty($_GET[$m]) ? Config::get('MODULE_DEFAULT') : $_GET[$m];
		$action = empty($_GET[$a]) ? Config::get('ACTION_DEFAULT') : $_GET[$a];
		
		//将结果传递给静态变量
		self::$group = $group;
		self::$module = $module;
		self::$action = $action;		
	}
	//网址解析
	public static function parseUrl(){
		//获取当前文件的路径/demo/index.php
		$script_name = HttpRequest::getScriptUrl();
		//获取完整的路径，包含"?"之后的字符串/demo/index.php/group/module...
		$url = HttpRequest::getRequestUri();
		/**
		 * 当url模式是 2 3时
		 * 2 index.php/group/module/action
		 * 3 /group/module/action 
		 */
		if (Config::get('URL_REWRITE_ON')<4) {				
			//去除url包含的当前文件的路径信息,只截取文件后的参数
			if ( $url && @strpos($url,$script_name,0) !== false ){
				$url = substr($url, strlen($script_name));
			} else {
				$script_name = str_replace(basename($script_name), '', $script_name);
				if ( $url && @strpos($url, $script_name, 0) !== false ){
					$url = substr($url, strlen($script_name));
				}
			}

		}else { 
			/**
			 * 当url模式是4 5时
			 * 4 index.php?r=/group/module/action
			 * 5 ?r=/group/module/action
			 */
			$url = isset($_GET[Config::get('URL_R_VAR')]) ? $_GET[Config::get('URL_R_VAR')] : '/';
		}

		//第一个字符是'/'，则去掉
		if ($url[0] == '/') {
			$url = substr($url, 1);
		}

		//去除问号后面的查询字符串
		if ( $url && false !== ($pos = @strrpos($url, '?')) ) {
			$url = substr($url,0,$pos);
		}

		//去除后缀
		if ($url&&($pos = strrpos($url,Config::get('URL_HTML_SUFFIX'))) > 0) {
			$url = substr($url,0,$pos);
		}

		$url = self::_getGroup($url);//获取分组名称
		$url = self::_getModule($url);//获取模块名称
		$url = self::_getAction($url);//获取操作方法名称

		//解析参数
		if($url) {
			$param = explode(Config::get('URL_PARAM_DEPR'), $url);
			$param_count = count($param);
			for($i=0; $i<$param_count; $i=$i+2) {
				$_GET[$i] = $param[$i];
				if(isset($param[$i+1])) {
					if( !is_numeric($param[$i]) ){
						$_GET[$param[$i]] = $param[$i+1];
					}
					$_GET[$i+1] = $param[$i+1];
				}
			}
		}
	}
	
	/**
	 * 根据配置文件判断包含分组并返回数组格式
	 * @return multitype:mixed 
	 */
	private static function _getGroupDir(){
		$return = array();
		$group_dir = glob(CP_CONFIG_PATH.'*.php');//取得分组目录
		foreach ($group_dir as $group){
			basename($group) <> 'config.php' && $return[] = str_replace('.php','',basename($group));//排除config.php 配置文件名作为分组名称列表
		}
		return $return;
	}
	//获取群组
	/**
	 * 获取group参数，分组
	 * @param unknown $url
	 * @return string
	 */
	private static function _getGroup($url){
		
		//设置默认分组时获取分组
		if(Config::get('GROUP_DEFAULT')){			
			$group_dir = self::_getGroupDir();//取得分组目录
			//域名绑定处理
			if (Config::get('GROUP_DOMAIN')) {
				foreach (Config::get('GROUP_DOMAIN') as $key => $domain){
					if ($domain == $_SERVER['HTTP_HOST']){
						if(in_array($key,$group_dir)){//分组目录中有对应分组
							self::$group = $key;//正式获得分组
						}
						break;
					}
				}
			}
			
			//通过域名找不到分组，则通过url寻找
			if(!self::$group){				
				//未进行域名绑定
				if ( $url && ($pos = @strpos($url, Config::get('URL_GROUP_DEPR'), 1) )>0 ) {					
					$group = substr($url,0,$pos);//分组
					if(in_array($group,$group_dir)){//分组目录中有对应分组
						self::$group = $group;//正式获得分组
						$url = substr($url,$pos+1);//除去分组名称，剩下的url字符串
					}					
				}else {//url找不到分组时，直接用url作为分组					
					if(in_array($url,$group_dir)){//分组目录中有对应分组
						self::$group = $url;//正式获得分组
						$url='';//清除剩余url
					}					
				}				
			}
			
			//如果进过url解析还是未获得分组，则采用默认值
			if(empty(self::$group)){
			    self::$status['groupIsDefault'] = true;//分组参数默认值标记
			    self::$group = Config::get('GROUP_DEFAULT');//设置默认值
			}
			
		}
		//返回剩余url
		return $url;
	}
	/**
	 * 获取module参数，模块类
	 * @param unknown $url
	 * @return string
	 */
	private static function _getModule($url){
		if ( $url && ($pos = @strpos($url, Config::get('URL_MODULE_DEPR'), 1) )>0 ) {
			self::$module = substr($url,0,$pos);//模块
			$url = substr($url,$pos+1);//除去模块名称，剩下的url字符串
		}else {		    
			self::$module = $url;//找不到，把剩余url作为参数
			$url='';//清除剩余url
		}
		
		//如果进过url解析还是未获得模块，则采用默认值
		if(empty(self::$module)){
		    self::$status['moduleIsDefault'] = true;//模块参数默认值标记
		    self::$module = Config::get('MODULE_DEFAULT');//设置默认值
		}
		
		//返回剩余url
		return $url;
	}
	/**
	 * 获取action参数，操作方法
	 * @param unknown $url
	 * @return string
	 */
	private static function _getAction($url){
		if($url&&($pos=@strpos($url,Config::get('URL_ACTION_DEPR'),1))>0) {
			self::$action = substr($url, 0, $pos);//模块
			$url = substr($url, $pos+1);			
		} else {		    
			self::$action=$url;	//找不到，把剩余url作为参数
			$url='';//清除剩余url
		}
		
	   //如果进过url解析还是未获得方法，则采用默认值
		if(empty(self::$action)){
		    self::$status['actionIsDefault'] = true;//方法参数默认值标记
		    self::$action = Config::get('ACTION_DEFAULT');//设置默认值
		}		

		//返回剩余url
		return $url;
	}

// +----------------------------------------------------------------------
// | 下面开始是输出处理
// +----------------------------------------------------------------------

/**
 * 生成url
 * @access public
 * @param array $url 地址参数url( '[模块/操作]','额外参数1=值1&额外参数2=值2...')
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
	public static function url($module,$request='',$author=''){
		/*
		 * 0 index.php?g=group&m=module&a=action
		 * 1 ?g=group&m=module&a=action
		 * 2 index.php/group/module/action
		 * 3 /group/module/action	
		 * 4 index.php?r=/group/module/action
		 * 5 ?r=/group/module/action
		 */
		$url_model = Config::get('URL_REWRITE_ON');
		switch ($url_model){			
			case 0:
			case 1:
				return self::gma_url($module,$request,$author);
			case 2:
			case 3:
				return self::path_info($module,$request,$author);
			case 4:
			case 5:
				return self::rewrite_url($module,$request,$author);
		}

	}
	/**
	 * 2 index.php/group/module/action
	 * 3 /group/module/action	
	 * @param unknown $module 'group/module/action'
	 * @param string $request 字符串或数组
	 * @param string $author 锚点连接
	 * @return string
	 */
	private static function path_info($module,$request='',$author=''){
		$group_default = Config::get('GROUP_DEFAULT');
		$group_depr = Config::get('URL_GROUP_DEPR');
		$module_depr = Config::get('URL_MODULE_DEPR');
		$action_depr = Config::get('URL_ACTION_DEPR');
		$param_depr = Config::get('URL_PARAM_DEPR');
		$domain_list = Config::get('GROUP_DOMAIN');//分组域名列表
		$domain = $domain_list[CP_GROUP];//取得当前分组对应域名
		//分解分组 模块 操作方法
		$marray = explode('/',$module);

		//如果只有模块和操作方法
		if (count($marray)==2) {
			$module = $marray[0].$module_depr.$marray[1].$action_depr;//生成module/action/格式
			if ($group_default) {//分组开启状态
				if (CP_GROUP != $group_default && !$domain) {//当前分组不是默认分组 并且没有设置域名
					$module = CP_GROUP.$group_depr.$module;//添加当前分组到url中 生成group/module/action/
				}
			}
		}
		//有分组 模块 操作方法
		if (count($marray)==3) {
			$module = $marray[1].$module_depr.$marray[2].$action_depr;//module/action/格式 无需加入分组名，最后生成url时将域名加入
			if ($marray[0] != $group_default && !$domain) {//指定分组不是默认分组 并且没有设置域名
				$module = $marray[0].$group_depr.$module;
			}
		}
		if(!empty($request)){
			if (is_array($request)) {
			    $request = http_build_query($request);
// 				$param = '';
// 				foreach ($request as $key=>$val){
// 					$param .= $param_depr.$key.$param_depr.$val;
// 				}
// 				$request = substr($param,1);//第一个字符是分隔符，去除出第一个字符
// 				$module = $module.$request;//组合模块和参数
			}
			$request = str_replace(array('=','&'), array($param_depr,$param_depr), $request);
			$module = $module.$request;			

		}else {//没有reque参数
			$module = substr($module,0,-1);//去除最后的action结尾分隔，符生成module/action格式
		}//end if request
		if ($domain) {//有对应域名时生成地址加入域名
			$return = Config::get('URL_SERVER_PROTOCOL').$domain.__APP__.'/'.$module.Config::get('URL_HTML_SUFFIX');
		}else {
			//如果设置了域名则添加协议如http://domain.com,如果域名为空则返回''
			$http = Config::get('URL_HTTP_HOST') ? Config::get('URL_SERVER_PROTOCOL').Config::get('URL_HTTP_HOST') : '';
			$return = $http.__APP__.'/'.$module.Config::get('URL_HTML_SUFFIX');
		}
		return $author ? $return.'#'.$author : $return;
	}
	/**
	 * 0 index.php?g=group&m=module&a=action
	 * 1 ?g=group&m=module&a=action
	 * @param unknown $module 'group/module/action'
	 * @param string $request 字符串或数组
	 * @param string $author 锚点连接
	 * @return string
	 */
	private static function gma_url($module,$request='',$author=''){
		$group_default = Config::get('GROUP_DEFAULT');
		$domain_list = Config::get('GROUP_DOMAIN');//分组域名列表
		$domain = $domain_list[CP_GROUP];//取得当前分组对应域名
		//获取原生地址变量名
		list($g,$m,$a) = explode(',',Config::get('URL_GMA_VAR'));

		//分解分组 模块 操作方法
		$marray = explode('/',$module);

		//如果只有模块和操作方法
		if (count($marray)==2) {
			$module = $m.'='.$marray[0].'&'.$a.'='.$marray[1];//生成m=module&a=action格式
			if ($group_default) {//分组开启状态
				if (CP_GROUP != $group_default && !$domain) {//当前分组不是默认分组 并且没有设置域名
					$module = $g.'='.CP_GROUP.'&'.$module;//添加当前分组到url中 生成g=group&m=module&a=action
				}
			}
		}
		//有分组 模块 操作方法
		if (count($marray)==3) {
			$module = $m.'='.$marray[1] .'&'. $a.'='.$marray[2];//m=module&a=action格式 无需加入分组名，最后生成url时将域名加入
			if ($marray[0] != $group_default && !$domain) {//指定分组不是默认分组 并且没有设置域名
				$module = $g.'='.$marray[0].'&'.$module;
			}
		}
		if(!empty($request)){
			if (is_array($request)) {
			    $request = http_build_query($request);
// 				$param = '';
// 				foreach ($request as $key=>$val){
// 					$param .= '&'.$key.'='.$val;
// 				}				
// 				$module = $module.$param;//组合模块和参数
			}
			$module = $module.'&'.$request;
			
		}//end if request
		$app = Config::get('URL_REWRITE_ON')==1 ? __APP__.'/' : __APP__;
		if ($domain) {//有对应域名时生成地址加入域名
			$return = Config::get('URL_SERVER_PROTOCOL').$domain.$app.'?'.$module;
		}else {
			//如果设置了域名则添加协议如http://domain.com,如果域名为空则返回''
			$http = Config::get('URL_HTTP_HOST') ? Config::get('URL_SERVER_PROTOCOL').Config::get('URL_HTTP_HOST') : '';
			$return = $http.$app.'?'.$module;
		}
		return $author ? $return.'#'.$author : $return;
	}
	/** 
	 * 4 index.php?r=/group/module/action
	 * 5 ?r=/group/module/action
	 * @param unknown $module 'group/module/action'
	 * @param string $request 字符串或数组
	 * @param string $author 锚点连接
	 * @return string
	 */
	private static function rewrite_url($module,$request='',$author=''){
		$group_default = Config::get('GROUP_DEFAULT');
		$group_depr = Config::get('URL_GROUP_DEPR');
		$module_depr = Config::get('URL_MODULE_DEPR');
		$action_depr = Config::get('URL_ACTION_DEPR');
		$param_depr = Config::get('URL_PARAM_DEPR');
		$domain_list = Config::get('GROUP_DOMAIN');//分组域名列表
		$domain = $domain_list[CP_GROUP];//取得当前分组对应域名
		//分解分组 模块 操作方法
		$marray = explode('/',$module);

		//如果只有模块和操作方法
		if (count($marray)==2) {
			$module = $marray[0].$module_depr.$marray[1].$action_depr;//生成module/action/格式
			if ($group_default) {//分组开启状态
				if (CP_GROUP != $group_default && !$domain) {//当前分组不是默认分组 并且没有设置域名
					$module = CP_GROUP.$group_depr.$module;//添加当前分组到url中 生成group/module/action/
				}
			}
		}
		//有分组 模块 操作方法
		if (count($marray)==3) {
			$module = $marray[1].$module_depr.$marray[2].$action_depr;//module/action/格式 无需加入分组名，最后生成url时将域名加入
			if ($marray[0] != $group_default && !$domain) {//指定分组不是默认分组 并且没有设置域名
				$module = $marray[0].$group_depr.$module;
			}
		}
		if(!empty($request)){
			if (is_array($request)) {
			    $request = http_build_query($request);
// 				$param = '';
// 				foreach ($request as $key=>$val){
// 					$param .= $param_depr.$key.$param_depr.$val;
// 				}
// 				$param = substr($param,1);//第一个字符是分隔符，去除出第一个字符
// 				$module = $module.$param;//组合模块和参数
			}
			$request = str_replace(array('=','&'), array($param_depr,$param_depr), $request);
			$module = $module.$request;			

		}else {//没有reque参数
			$module = substr($module,0,-1);//去除最后的action结尾分隔，符生成module/action格式
		}//end if request
		$app = Config::get('URL_REWRITE_ON')==5 ? __APP__.'/' : __APP__;


		if ($domain) {//有对应域名时生成地址加入域名
			$return = Config::get('URL_SERVER_PROTOCOL').$domain.$app."?".Config::get('URL_R_VAR')."=".$module.Config::get('URL_HTML_SUFFIX');
		}else {
			//如果设置了域名则添加协议如http://domain.com,如果域名为空则返回''
			$http = Config::get('URL_HTTP_HOST') ? Config::get('URL_SERVER_PROTOCOL').Config::get('URL_HTTP_HOST') : '';
			$return = $http.$app."?".Config::get('URL_R_VAR')."=".$module.Config::get('URL_HTML_SUFFIX');
		}
		return $author ? $return.'#'.$author : $return;
	}
}