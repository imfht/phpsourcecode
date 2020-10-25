<?php
class cpHtmlCache {
	static private $module;//当前模块
	static private $action;//当前操作方法
	static private $cacheAble = false;//用来标志是否可以正常读写缓存
	static private $cacheFile;//缓存文件名

		//读取静态缓存文件
	static public function read($module='', $action=''){	
		self::$module = $module;
		self::$action = $action;
		//如果不符合静态规则，则直接返回false
		if ( false == self::_checkRule() ) {
			return false;
		}
		self::$cacheAble = true;//标志静态缓存可正常使用
		
		if( isset($_SERVER['PATH_INFO']) ) {
			$url = $_SERVER['PATH_INFO'];
		} else {
			$script_name = $_SERVER["SCRIPT_NAME"];//获取当前文件的路径
			$url = $_SERVER["REQUEST_URI"];//获取完整的路径，包含"?"之后的字符串
			
			//去除url包含的当前文件的路径信息
			if ( $url && @strpos($url,$script_name,0) !== false ){
				$url = substr($url, strlen($script_name));
			} else {
				$script_name = str_replace(basename($_SERVER["SCRIPT_NAME"]), '', $_SERVER["SCRIPT_NAME"]);
				if ( $url && @strpos($url, $script_name, 0) !== false ){
					$url = substr($url, strlen($script_name));
				}
			}
		}
		//第一个字符是'/'，则去掉
		if ($url[0] == '/') {
			$url = substr($url, 1);
		}	

		//设定缓存文件名
		if( empty($_SERVER['QUERY_STRING']) && preg_match("#^[a-z0-9_\-\/]+\.html$#i",$url) ){
			self::$cacheFile =$url;
		} else {
			self::$cacheFile = self::$module . '/' . self::$action . '/' . md5($_SERVER['REQUEST_URI']) . cpConfig::$config['APP']['HTML_CACHE_SUFFIX'];		
		}
		self::$cacheFile = cpConfig::$config['APP']['HTML_CACHE_PATH'] . self::$cacheFile;
		$dir = dirname( self::$cacheFile );
		if ( !is_dir($dir) ) {
			@mkdir($dir,0777,true);
		}
		
		if( isset(cpConfig::$config['APP']['HTML_CACHE_RULE'][self::$module][self::$action]) ) {
			$expires = cpConfig::$config['APP']['HTML_CACHE_RULE'][self::$module][self::$action];
		} else {
			$expires = cpConfig::$config['APP']['HTML_CACHE_RULE'][self::$module]['*'];
		}
			
		//静态缓存文件存在，且没有过期，则直接读取
		if ( file_exists(self::$cacheFile) && (time() < ( filemtime( self::$cacheFile ) + $expires ) ) ) {
			readfile(self::$cacheFile);
			return true;
		}
		ob_start();//开启内容输出控制
		return false;
	}
	
	//写入静态缓存文件
	static  public function write() {	
		if(self::$cacheAble) {
			$contents=ob_get_contents();
			if ( strlen($contents)>0 && file_put_contents(self::$cacheFile,$contents) ) { 
			   ob_end_clean();
			 //为了可以用回调函数，修改生成的静态页面，生成静态页面之后再读取
			   self::read(self::$module,self::$action);
			} else {  //没有输出内容，直接输出，不生成空的静态页面
			 ob_end_flush();
			}
		}
	}

	//检查规则，看是否满足生成静态页面的条件 
	static private function _checkRule() {
		//缓存没有开启或缓存规则为空，则直接返回false
		if ( (cpConfig::$config['APP']['HTML_CACHE_ON'] == false) || empty(cpConfig::$config['APP']['HTML_CACHE_RULE']) ) {
			return false;
		}
		return isset(cpConfig::$config['APP']['HTML_CACHE_RULE'][self::$module][self::$action]) || isset(cpConfig::$config['APP']['HTML_CACHE_RULE'][self::$module]['*']);
	}
}