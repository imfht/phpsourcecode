<?php
/** ModPHP 核心函数 */
/** conv_request_vars() 转换表单提交的变量 */
function conv_request_vars(&$input = null, $config = array()){
	if($input === null){
		$config = config2list(config(), '', '_', true);
		conv_request_vars($_GET, $config); //转换 $_GET
		conv_request_vars($_POST, $config); //转换 $_POST
		$reqOd = array('G'=>'_GET', 'P'=>'_POST', 'C'=>'_COOKIE');
		$_REQUEST = array();
		foreach (str_split(ini_get('request_order')) as $v) {
			if(isset($reqOd[$v])) //重新填充 $_REQUEST 变量
				$_REQUEST = array_merge($_REQUEST, $GLOBALS[$reqOd[$v]]);
		}
		return null;
	}
	foreach ($input as $k => $v) {
		if(is_array($v)) conv_request_vars($v, $config); //递归转换
		elseif($v === 'true') $v = true; //将 'true' 转换为 true
		elseif($v === 'false') $v = false; //将 'false' 转换为 false
		elseif($v === 'undefined' || $v === 'null') $v = null; //将 'undefined' 和 'null' 转换为 null
		elseif(is_numeric($v) && (int)$v < 2147483647) $v = (int)$v; //为确保平台兼容性，数字最大值不应超过 2147483646
		//针对配置项，将 _ 转换为 .
		if(strpos($k, '_') && (is_client_call('mod', 'install') || is_client_call('mod', 'config')) && in_array($k, $config)){
			unset($input[$k]);
			$k = str_replace('_', '.', $k);
		}
		$input[$k] = $v;
	}
	ksort($input); //重新排序
}

/**
 * load_config_file() 载入 ModPHP 配置目录中的配置文件
 * @param  string $file 文件名
 * @return array        配置数组
 */
function load_config_file($file){
	$config = @load_config(__ROOT__.'user/config/'.$file) ?: array();
	if($config && $file == 'config.php'){
		$config = array_xmerge(load_config(__CORE__.'config/'.$file) ?: array(), $config);
	}elseif(!$config){
		$config = load_config(__CORE__.'config/'.$file) ?: array();
	}
	return $config;
}

/**
 * hooks() 存储 Api Hook 回调函数
 * @param  string $api    [可选]API 名称，使用点语法，如 user.add
 * @param  array  $value  [可选]API 回调函数集，或者设置为 null/false 来清空 API
 * @return array          如果未设置 $api 参数，返回所有回调函数集
 *                        如果设置了 $api 参数，但未设置 $value 参数，返回 $api 对应的回调函数集, 不存在则返回 false
 *                        如果同时设置 $api 和 $value 参数，则始终返回 $value
 */
function hooks($api = '', $value = ''){
	static $hooks = array();
	if(!$api) return $hooks;
	$_api = "['".str_replace('.', "']['", $api)."']"; //将点语法替换为数组形式
	if($value === ''){
		return eval('return isset($hooks'.$_api.') ? $hooks'.$_api.' : null;'); //eval() 用得好，功能很强大
	}elseif($value === null || $value === false){
		eval('unset($hooks'.$_api.');');
		return null;
	}else{
		eval('$hooks'.$_api.' = null; $_hook = &$hooks'.$_api.';'); //通过引用的方式来修改原变量
		return $_hook = $value; //给 $_hook 赋值就相当于给 $hook 赋值
	}
}

/** 
 * add_hook() 添加 Api Hook 回调函数
 * @param  string|array $api      API 名称, 可使用索引数组同时为多个 API 设置同一个回调函数
 * @param  callable     $func     回调函数
 * @param  boolean      $apiIsSet [可选]API 表示回调函数为集合，默认 true，如果设置为 false, 则 API 表示单个回调函数
 */
function add_hook($api, $func, $apiIsSet = true) {
	$apis = is_array($api) ? $api : array($api);
	foreach ($apis as $api) {
		if($apiIsSet){
			$hooks = hooks($api) ?: array();
			if(!in_array($func, $hooks)) $hooks[] = $func; //添加回调函数
			hooks($api, $hooks);
		}else{
			hooks($api, $func); //将函数绑定到 API
		}
	}
}
function_alias('add_hook', 'add_action');

/**
 * remove_hook() 移除 Api Hook 回调函数
 * @param  string|array $api  API 名称, 可使用索引数组同时为多个 API 移除回调函数
 * @param  callable     $func [可选]要移除的函数名，匿名函数只能通过清空 API 来移除绑定
 */
function remove_hook($api, $func = ''){
	$apis = is_array($api) ? $api : array($api);
	foreach ($apis as $api) {
		$hooks = hooks($api);
		if($hooks){
			if($func){
				$i = array_search($func, $hooks);
				if($i !== false){
					array_splice($hooks, $i, 1); //通过函数名删除指定的回调函数
					hooks($api, $hooks); //更新 API 绑定的内容
				}
			}else{
				hooks($api, null); //清空 API
			}
		}
	}
}
function_alias('remove_hook', 'remove_action');
function_alias('remove_hook', 'delete_action');

/** 
 * do_hooks() 执行 Api Hook 回调函数
 * @param  string  $api    API 名称
 * @param  mixed   &$input [可选]传入参数
 */
function do_hooks($api, &$input = null){
	$hooks = hooks($api);
	if(!error() && $hooks){
		$isSet = is_array($hooks);
		$hooks = $isSet ? $hooks : array($hooks);
		foreach ($hooks as $func) {
			if(is_string($func) && strpos($func, '::')){ //处理类方法
				list($class, $method) = explode('::', $func);
				if(method_exists($class, $method))
					$result = $class::$method($input); //静态调用类方法
				else
					$result = null;
			}elseif(is_callable($func)){ //处理回掉函数，PHP7 中也可以处理匿名类，需要类中定义 __invoke() 方法
				$result = $func($input);
			}elseif(method_exists($func, '__invoke')){ //将类作为函数调用
				$func = new $func;
				$result = $func($input);
			}else{
				$result = null;
			}
			if(error() && $isSet) break; //出现错误则不再执行后面的回调函数
			if($result !== null) $input = $result; //如果回调函数有返回值，则将其填充到传入参数中
		}
	}
}
function_alias('do_hooks', 'do_actions');

/**
 * config() 读取和设置配置
 *          ModPHP 拥有三层配置模式，即默认配置、用户配置、运行时配置，优先级从右到左
 *          默认配置文件: mod/config/config.php
 *          用户配置文件：user/config/config.php
 * @param  string $key   [可选]配置名
 * @param  string $value [可选]配置值
 * @return string        如果未设置 $key 参数，则返回所有配置组成的关联数组
 *                       如果仅设置 $key 参数，如果存在该配置，则返回配置值，否则返回 null
 *                       如果设置了 $value 参数，则始终返回 $value
 *                       如果配置文件中不存在 $key 配置而为 $key 配置设置值，则将 $key 配置加载到内存中
 */
function config($key = '', $value = null){
	static $config = array();
	if(!$config) $config = load_config_file('config.php');
	if(!$key) return $config;
	if($value === null && is_string($key)){
		$key = "['".str_replace('.', "']['", $key)."']";
		return eval('return isset($config'.$key.') ? $config'.$key.' : null;');
	}else{
		if(is_string($key)) $key = array($key => $value);
		foreach ($key as $k => $v) { //同时设置多个配置
			$k = "['".str_replace('.', "']['", $k)."']";
			eval('$config'.$k.' = null; $_config = &$config'.$k.';'); //为配置项创建引用
			$_config = $v; //通过引用赋值
		}
		return $value !== null ? $value : true;
	}
}

/**
 * database() 返回配置的数据库结构数组
 * @param  string  $key      [可选]数组的一维键名
 * @param  boolean $withAttr [可选]当设置 $key 参数时返回包含属性的关联数组，默认 false, 只返回包含字段名的索引数组
 * @return array             如果设置了 $key，则返回 $database 的二维数组键名组成的数组，否则返回 $database
 */
function database($key = '', $withAttr = false){
	static $db = array();
	if(!$db) $db = load_config_file('database.php');
	if(!$key) return $db;
	return isset($db[$key]) ? ($withAttr ? $db[$key] : array_keys($db[$key])) : null;
}

/**
 * staticuri() 设置或获取指定模板文件的伪静态 URI 格式
 * @param  string|array $file   [可选]模板文件名
 * @param  string       $format [可选]伪静态 URI 格式
 * @return mixed                如果未提供参数，则返回所有伪静态地址格式
 *                              如果仅提供 $file 参数，则返回对应的伪静态地址
 *                              如果同时提供两个参数，则始终返回 $format
 */
function staticuri($file = '', $format = ''){
	static $uri = array();
	if(!$uri) $uri = load_config_file('static-uri.php');
	if(!$file) return $uri;
	if(is_string($file) && path_starts_with($file, __ROOT__)) //替换为相对于 __ROOT__ 的路径
		$file = substr($file, strlen(__ROOT__));
	if(is_assoc($file)){ //同时设置多个伪静态规则
		foreach ($file as $k => $v) {
			if(path_starts_with($k, __ROOT__)) $k = substr($k, strlen(__ROOT__));
			$uri[$k] = $v;
		}
		return true;
	}elseif($format){
		return $uri[$file] = $format;
	}
	return isset($uri[$file]) ? $uri[$file] : null;
}
function_alias('staticuri', 'staticurl');

/**
 * lang() 设置和获取语言提示
 * @param  string|array $key [可选]指定语言提示或设置为关联数组以设置语言提示
 *                           该函数还支持其他参数，当提供时，他们将用来替换语言中的标记
 *                           例如 lang('mod.noData') 的返回值是“无{module}数据。”，
 *                           则 lang('mod.noData', '用户') 的返回值则为“无用户数据。”
 * @return string|array      指定语言提示或所有语言提示(如果未设置 $key )
 */
function lang($key = ''){
	static $lang = array();
	$args = array_slice(func_get_args(), 1);
	if(!$lang){
		$file = strtolower(config('mod.language')).'.php'; //对应示例： zh-CN => zh-cn.php
		$lang = load_config(__ROOT__.'user/lang/'.$file) ?: load_config(__CORE__.'lang/'.$file) ?: load_config(__CORE__.'lang/en-us.php') ?: array(); //载入语言包
	}
	if(!$key) return $lang;
	if(is_assoc($key)){ //设置语言提示
		foreach ($key as $k => $v) {
			$k = "['".str_replace('.', "']['", $k)."']";
			eval('$lang'.$k.' = null; $_lang = &$lang'.$k.';'); //通过引用进行设置
			$_lang = $v;
		}
		return true;
	}
	$_key = "['".str_replace('.', "']['", $key)."']";
	eval('$msg =  isset($lang'.$_key.') ? $lang'.$_key.' : "'.$key.'";');
	if(!is_string($msg)) return $msg;
	if(preg_match_all('/{(.+)}/U', $msg, $matches)){
		return str_replace($matches[0], $args, $msg); //将其他参数作为语言中的标记
	}
	return $msg;
}

/**
 * success() 返回成功的操作，用在类方法或 Api Hook 回调函数中
 * @param  string|array  $data  操作成功的提示或数据
 * @param  array         $extra [可选]额外的信息
 * @return array                操作结果
 */
function success($data, array $extra = array(), $state = true){
	$arr = array('success'=>$state, 'data'=>$data);
	$arr = array_merge($arr, $extra);
	return $arr;
}

/**
 * error() 返回失败的操作，用在类方法或 Api Hook 回调函数中
 * @param  string|array  $data  [可选]操作失败的提示或数据
 * @param  array         $extra [可选]额外的信息
 * @return array                操作结果
 */
function error($data = '', array $extra = array()){
	static $error = null; //错误信息保存在内存中
	if($data === null || $data === false){
		return $error = null;
	}elseif($data !== ''){
		return $error = success($data, $extra, false);
	}else{
		return $error;
	}
}

/**
 * is_display() 判断当前展示的页面是否为指定页面
 * @param  string  $file 页面文件名
 * @return boolean
 */
function is_display($file){
	if(!display_file()) return false;
	if($file[strlen($file)-1] == '/')
		return path_starts_with(display_file(), $file); //判断目录
	return $file == display_file(); //判断文件
}

/**
 * is_template() 判断当前展示的页面是否为模板页面或在模板目录中
 * @param  string  $file [可选]模板文件名
 * @return boolean
 */
function is_template($file = ''){
	return is_display(template_path($file, false));
}

/** is_home() 判断是否为首页 */
function is_home(){
	return is_template(config('site.home.template'));
}

/**
 * is_client_call() 判断当前是否为通过 URL 请求操作
 * @param  string  $obj [可选]请求对象
 * @param  string  $act [可选]请求方法
 * @return boolean 
 */
function is_client_call($obj = '', $act = ''){
	if((__SCRIPT__ != 'mod.php' && !is_socket()))
		return false;
	elseif($obj && $act) //同时比较 obj 和 act
		return isset($_GET['obj'], $_GET['act']) && !strcasecmp($obj, $_GET['obj']) && !strcasecmp($act, $_GET['act']);
	elseif($obj) //比较 obj
		return isset($_GET['obj']) && !strcasecmp($obj, $_GET['obj']);
	elseif($act) //比较 act
		return isset($_GET['act']) && !strcasecmp($act, $_GET['act']);
	else
		return !empty($_GET['obj']) || !empty($_GET['act']);
}

/**
 * is_socket() 判断是否运行于 Socket 模式
 * @return boolean
 */
function is_socket(){
	return isset($_SERVER['SOCKET_SERVER']) && $_SERVER['SOCKET_SERVER'] == 'on';
}
function_alias('is_socket', 'is_websocket');

/** detect_site_url() 检测网站根目录地址 */
function detect_site_url($header = '', $host = ''){
	static $siteUrl = ''; //将网站地址保存到内存中
	$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
	if($siteUrl && !$header) return $siteUrl;
	if($header){ //非客户端请求，如 Socket 服务器中使用
		/** @var closure 获取 Document Root */
		$getDocRoot = function($path) use (&$getDocRoot){
			if(!$path || $path == '/') return __ROOT__;
			$i = strrpos(__ROOT__, $path);
			if($i !== false && $i == (strlen(__ROOT__) - strlen($path))){
				return substr(__ROOT__, 0, $i);
			}else{
				$path = rtrim($path, '/');
				$i = strrpos($path, '/');
				$path = substr($path, 0, $i ? $i+1 : 0);
				return $getDocRoot($path); //递归获取
			}
		};
		$header = explode(' ', $header); //$header 即 HTTP 请求头中的第一行
		$path = strstr($header[1], '?', true) ?: $header[1];
		$path = substr($path, 1, strrpos($path, '/')+1);
		$docRoot = $getDocRoot($path);
		$scheme = is_ssl() ? 'https' : 'http'; //协议类型
	}else{ //客户端请求
		$docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])); //获取 Document Root
		if($docRoot) $docRoot = $docRoot.'/';
		if(!path_starts_with(__ROOT__, $docRoot)){ //ModPHP 运行在符号链接目录中
			$scriptFile = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME']));
			$i = strrpos($scriptFile, $script);
			$docRoot = substr($scriptFile, 0, $i+1);
		}
		extract(parse_url(url()));
	}
	if(path_starts_with(__ROOT__, $docRoot)){
		$sitePath = substr(__ROOT__, strlen($docRoot)); //网站目录
	}else{
		$sitePath = substr($script, 1, strrpos($script, '/')+1);
	}
	return isset($scheme) ? $siteUrl = $scheme.'://'.$host.(!empty($port) ? ':'.$port : '').'/'.$sitePath : '';
}

/**
 * site_url() 获取网站根目录地址
 * @param  string $file [可选]目录下的文件
 * @return string       网站根目录 URL 地址，如果设置 $file, 则将返回包含 $file 的地址
 */
function site_url($file = ''){
	if(config('site.URL')) return config('site.URL').$file;
	return detect_site_url().$file;
}

/** 
 * template_url() 获取模板目录的完整 URL 地址
 * @param  string $file [可选]目录下的文件
 * @return string       模板目录 URL 地址，如果设置 $file, 则将返回包含 $file 的地址
 */
function template_url($file = ''){
	return site_url().template_path($file, false);
}

/**
 * create_url() 自动生成 URL 链接，第一个参数为伪静态 URL 格式, 其他参数用于替换 {} 标注的关键字
 * @param  string $format 伪静态 URL 格式，使用 / 作为分隔符，如 page/{page}.html
 * @param  array  $args   用以替换关键字的参数列表，为一个关联数组
 *                        也可以为函数传入多个参数，每一个参数对应一个要替换的关键字
 * @return                生成的链接，创建失败则返回 false
 */
function create_url($format, $args){
	$args = is_array($args) ? $args : array_slice(func_get_args(), 1); //参数值列表
	$index = config('mod.pathinfoMode') ? 'index.php/' : '';
	if(is_assoc($args)){
		$keys = array_keys($args);
		$values = array_values($args); //替换值
		foreach ($keys as &$key) {
			$key = '{'.$key.'}';
		}
		return site_url($index).@str_replace($keys, $values, $format);
	}elseif(preg_match_all('/{(.+)}/U', $format, $matches)){
		return site_url($index).str_replace($matches[0], $args, $format); //将除了第一个参数外的其他参数作为替换值
	}
	return false;
}

/**
 * analyze_url() 解析伪静态 URL 地址
 * @param  string  $format  伪静态 URL 格式，如: '{categoryName}/{post_id}.html';
 * @param  string  $url     [可选]待解析的 URL 地址，如果不设置，则默认为当前访问路径
 * @return array            URL 中包含的参数，匹配结果为空则返回 false
 */
function analyze_url($format, $url = ''){
	$url = $url ?: url(); //如果未提供 URL 地址，则使用当前访问的地址
	$uri = strstr($url, '?', true) ?: $url;
	$uri = urldecode($uri); //对 URI 地址进行转义解码
	if(path_starts_with($uri, site_url('index.php'))) //URL 以网站地址 + index.php 开始
		$uri = substr($uri, strlen(site_url())+10);
	elseif(path_starts_with($uri, site_url())) //URL 以网站地址开始
		$uri = substr($uri, strlen(site_url()));
	$format = explode('/', trim($format, '/')); //使用 / 作为分隔符
	$uri = explode('/', trim($uri, '/'));
	$end = count($format)-1;
	$args = array();
	$count = count($format);
	for ($i=0; $i < $count; $i++) {
		if(!isset($uri[$i])) continue;
		if($i == $end){
			$ext1 = strrchr($format[$i], '.'); //伪静态后缀
			$ext2 = strrchr($uri[$i], '.'); // URI 后缀
			if($ext1 != $ext2) return false; //判断后缀名是否相同(如果有)
			elseif($len = strlen($ext1)){
				$format[$i] = substr($format[$i], 0, -$len);
				$uri[$i] = substr($uri[$i], 0, -$len);
			}
		}
		if($format[$i][0] == '{' && $format[$i][strlen($format[$i])-1] == '}'){
			$args[trim($format[$i], '{}')] = $uri[$i]; //替换关键字
		}elseif($format[$i] != $uri[$i]){
			return false;
		}
	}
	return $args ?: false;
}

/**
 * current_file() 获取当前文件名，与常量 __FILE__ 不同，__FILE__ 的值始终是它所在文件的名称，
 *                而 current_file() 则返回调用该函数的文件的名称。
 * @return string 当前文件名
 */
function current_file(){
	$debug = debug_backtrace(); //使用回溯跟踪
	$count = count($debug);
	if($count > 1){
		for ($i=0; $i < $count; $i++) { 
			if(isset($debug[$i]['file'], $debug[$i+1]['args'][0]) && $debug[$i+1]['args'][0] == $debug[$i]['file']){
				break;
			}
		}
		if(!isset($debug[$i])) $i -= 1;
	}else $i = 0;
	return str_replace('\\', '/', $debug[$i]['file']);
}

/**
 * current_dir() 获取当前目录
 * @param  string $file [可选]目录下的文件
 * @return string       当前目录地址, 如果设置 $file, 则将返回包含 $file 的地址
 */
function current_dir($file = ''){
	return substr(current_file(), 0, strrpos(current_file(), '/')+1).$file;
}

/**
 * template_path() 获取模板目录的路径
 * @param  string $file [可选]目录下的文件
 * @param  bool   $abs  [可选]返回绝对路径，默认 true
 * @return string       模板目录的地址，如果设置 $file, 则将返回包含 $file 的地址
 */
function template_path($file = '', $abs = true){
	return ($abs ? __ROOT__ : '').config('mod.template.appPath').config('mod.template.savePath').$file;
}
function_alias('template_path', 'template_dir');

/** 
 * template_file() 获取显示的模板文件名
 * @return string  文件名
 */
function template_file(){
	return path_starts_with(display_file(), template_path('', false)) ? substr(display_file(), strlen(template_path('', false))) : '';
}

/**
 * current_dir_url() 获取当前目录的完整 URL 地址
 * @param  string $file [可选]目录下的文件
 * @return string       当前目录的 URL 地址, 如果设置 $file, 则将返回包含 $file 的地址
 */
function current_dir_url($file = ''){
	return detect_site_url().substr(current_dir(), strlen(__ROOT__)).$file;
}

/**
 * import() 在页面中载入 js、css 等文件，也可载入程序文件或其他文件(和 include 相同)
 * @param  string $file 文件名
 * @param  string $tag  [可选]HTML 标签
 * @param  string $attr [可选]标签属性
 * @return null|mixed   如果载入的是 php 文件或未知文件，则返回其内容
 */
function import($file, $tag = '', $attr = ''){
	if(path_starts_with($file, __ROOT__)){ //$file 为绝对服务器路径
		$url = site_url(substr($file, strlen(__ROOT__)));
	}elseif(strpos($file, '://')){ //$file 为 URL 地址
		$url = $file;
	}elseif($file[1] != ':' && $file[0] != '/'){ //$file 为相对路径
		$url =  current_dir_url($file); //获取绝对 URL 地址
		$file = current_dir($file); //获取绝对服务器路径
		if(template::$saveDir && path_starts_with($file, template::$saveDir)){ //该文件在模板编译目录下
			$path = substr($file, strlen(template::$saveDir));
			$file = template::$rootDir.$path; //获取原始目录路径
			$url = template::$rootDirURL.$path;
		}
	}else{
		$url = '';
	}
	$ext = extname($file);
	$tag = strtolower(trim($tag, '<>'));
	$attr = $attr ? " $attr" : '';
	if(!$url && $tag) return null;
	if($ext == 'js'){ //js 文件
		echo '<script type="text/javascript" src="'.$url.'"'.$attr."></script>\n";
	}elseif($ext == 'css'){ //css 文件
		echo '<link type="text/css" rel="stylesheet" href="'.$url.'"'.$attr." />\n";
	}elseif(is_img($file)){ //图片文件
		echo '<img src="'.$url.'"'.$attr.' />';
	}elseif($tag){ //其他通过 html 标签引入的文件，如 iframe 引入 html
		echo '<'.$tag.' src="'.$url.'"'.$attr.($tag == 'img' || $tag == 'embed' ? ' />' : '></'.$tag.'>');
	}else{ //其他直接引入到程序中的文件，如 php
		${'FILE'.INIT_TIME} = $file;
		unset($file, $tag, $attr, $url, $ext, $path);
		extract($GLOBALS); //将全局变量暴露给引用的文件
		return include ${'FILE'.INIT_TIME};
	}
}

/**
 * get_template_file() 获取 URL 请求显示的模板文件
 * @param  string  $url     [可选]URL 地址
 * @param  string  $tpldir  [可选]模板目录
 * @param  string  $rootURL [可选]根目录 URL 地址
 * @param  string  $uri     [可选]URI 地址
 * @return string|false     模板文件名
 */
function get_template_file($url = '', $tpldir = '', $rootURL = '', $uri = ''){
	if(!$uri){
		$url = $url ?: url(); //如果不提供 URL 则使用当前访问的地址
		$rootURL = $rootURL ?: current_dir_url();
		if($rootURL && path_starts_with($url, $rootURL))
			$uri = substr($url, strlen($rootURL)); //获取相对路径
		$query = strstr($uri, '?'); //查询字符串
		$uri = $_uri = strstr($uri, '?', true) ?: $uri;
		if(path_starts_with($uri, 'index.php/'))
			$uri = substr($uri, 10); //以 index.php/ 开头则将其去掉
		$uri = rtrim($tpldir.$uri, '/');
	}
	$exts = template::$extensions; //模板引擎所使用的后缀名
	if(file_exists($uri)){
		if(!is_dir($uri)){
			return $uri;
		}else{
			if(!empty($_uri) && $_uri[strlen($_uri)-1] != '/'){ //如果访问的是一个目录而 URL 不以 / 结尾，
				redirect($rootURL.$_uri.'/'.$query, 301); //自动更正并跳转 URL 地址
			}
			foreach($exts as $ext){
				if(file_exists($uri.'/index.'.$ext)) //如果存在索引文件
					return $uri.'/index.'.$ext;      //则使用索引文件
			}
			return false;
		}
	}
	foreach($exts as $ext){
		if(file_exists($uri.'.'.$ext))
			return $uri.'.'.$ext; //判断是否使用了不带后缀的链接
	}
	if($len = strrpos($uri, '/')){
		$uri = substr($uri, 0, $len);
		return get_template_file('', '', '', $uri); //当前目录没有匹配的文件，则继续向父目录查找
	}
	return false;
}

/**
 * display_file() 设置或者获取显示页面文件名
 * @param  string  $url [可选]URL 地址，不设置则为当前地址
 * @param  boolean $set [可选]将 $url 设置为一个文件名，然后设置 $set 为 true，以此设置显示页面
 * @return string
 */
function display_file($url = '', $set = false){
	static $file = '';
	static $_file = '';
	static $sid = null; //在 Socket 中区别不同的客户端
	if($file !== '' && !$url) return $file;
	if($set){
		if(path_starts_with($url, __ROOT__))
			$url = substr($url, strlen(__ROOT__)); //获取文件相对路径
		return $file = $url;
	}elseif(!$url && (__SCRIPT__ == 'mod.php' || is_socket())){
		if(!empty($_SERVER['HTTP_REFERER']))
			$url = $_SERVER['HTTP_REFERER']; //使用来路页面作为需要解析的地址
		else
			return $file = __SCRIPT__;
	}
	$url = $url ?: url();
	if(!$url) return false;
	$uri = strstr($url, '?', true) ?: $url;
	if(path_starts_with($uri, site_url('index.php'))){
		$index = 'index.php/';
		$uri = site_url().substr($uri, strlen(site_url())+10); //10 表示截掉 index.php? 或 index.php/
	}else{
		$index = '';
	}
	$tplPath = template_path('', false);
	$appPath = config('mod.template.appPath');
	$home = config('site.home.template');
	if($uri == site_url() || $uri == site_url($home)){ //首页
		return $file = $tplPath.$home;
	}elseif(path_starts_with($uri, site_url())){
		$uri = substr($uri, strlen(site_url())); //获取相对路径
	}elseif(strpos($uri, '://')){ //URL 地址不是本站地址
		return $file = __SCRIPT__;
	}
	if(file_exists(__ROOT__.$uri)){ // URL 是一个实际的文件地址
		return $file = $uri;
	}
	$isIndex = __SCRIPT__ == 'index.php'; //是否运行索引（模板入口）文件
	$uri = rtrim($uri, '/');
	$tpl = '';
	if(!$tpl && $appPath)
		$tpl = get_template_file($url, $appPath, site_url()); //尝试从 app 目录获取模板文件
	if(!$tpl && $tplPath)
		$tpl = get_template_file($url, $tplPath, site_url()); //尝试从模板目录获取模板文件
	if(!$tpl)
		return $file = $tplPath.config('site.errorPage.404'); //无模板则报告 404 错误
	if($tpl != $tplPath.$home){ //URL 地址对应一个真实的文件
		$ext = '.'.extname($tpl);
        if($ext != '.'){
            $types = load_config_file('mime.ini'); //加载 Mime 类型配置
            $mime = isset($types[$ext]) ? $types[$ext] : 'text/plain';
        }
		if($isIndex) set_content_type($mime); //设置响应头中的 Mime 类型
		if(staticuri($tpl) && $args = analyze_url(staticuri($tpl), $uri)){ //尝试解析 URL
			if($isIndex) $_GET = array_merge($_GET, $args);
		}
		return $file = $tpl;
	}else{ //URL 地址是一个伪静态地址
		$URI = config('site.home.staticURI') ?: config('site.home.staticURL');
		if($URI && ($args = analyze_url($URI, $uri)) !== false){ //判断 URL 是否为首页
			if($isIndex) $_GET = array_merge($_GET, $args);
			return $file = $tpl;
		}
		if(config('mod.installed')){
			$config = config();
			//尝试获取模块记录
			foreach(database() as $key => $value){
				if(!empty($config[$key]['staticURI']) && !empty($config[$key]['template'])){
					$URI = $config[$key]['staticURI'];
					$get = 'get_'.$key;
					if($args = analyze_url($URI, $uri)){ //解析 URL 地址
						foreach($args as $k => $v){
							if(in_array($k, database($key)) && strpos($k, $key) === 0){
								$where[$k] = $v; //组合 where 条件
							}
						}
						if(isset($where) && ($result = database::open(0)->select($key, "{$key}_id", $where)) && $result->fetchObject()){ //检查记录是否存在
							if($isIndex) $_GET = array_merge($_GET, $args);
							$file = $tplPath.$config[$key]['template'];
							if($_file !== $file || $sid !== session_id()){
								$_file = $file;
								$sid = session_id(); //更新会话
								$get($_GET); //获取记录
							}
							return $file;
						}
					}
				}
			}
			//尝试根据自定义永久链接获取记录
			foreach(database() as $key => $value){
				if(isset($value[$key.'_link']) && !empty($config[$key]['template'])){
					if($link = substr($url, strlen(site_url($index)))){
						$get = 'get_'.$key;
						$result = database::open(0)->select($key, "{$key}_id", "`{$key}_link` = '{$link}'"); //检查记录是否存在
						if($result && $result->fetchObject()){
							$file = $tplPath.$config[$key]['template'];
							if($_file !== $file || $sid !== session_id()){
								$_file = $file;
								$sid = session_id();
								$get(array($key.'_link'=>$link)); //获取记录
							}
							return $file;
						}
					}
				}
			}
		}
		return $file = $tplPath.config('site.errorPage.404'); //如果没有匹配的模板，则报告 404 错误
	}
}

/**
 * get_table_by_primkey() 通过主键获取表名
 * @param  string $primkey 主键
 * @return string          表名(模块名)
 */
function get_table_by_primkey($primkey){
	foreach (database() as $key => $value) {
		foreach ($value as $k => $v) {
			if($k == $primkey && stripos($v, 'PRIMARY KEY') !== false) return $key;
		}
	}
	return false;
}

/**
 * get_primkey_by_table() 通过表名获取主键
 * @param  string $table 表名(模块名)
 * @return string        主键
 */
function get_primkey_by_table($table){
	if(is_array(database($table, true))) {
		foreach (database($table, true) as $k => $v) {
			if(stripos($v, 'PRIMARY KEY') !== false) return $k;
		}
	}
	return false;
}

/**
 * register_module_functions() 自动注册模块函数, 该函数将自动注册下面这些函数:
 * _{module}():           包含实例化的对象、当前分页、总页数等记录元信息的函数
 * get_{module}():        获取单条记录的函数
 * get_multi_{module}():  获取多条记录的函数
 * get_search_{module}(): 搜索(模糊查询)多条记录的函数
 * the_{module}():        存储当前记录信息的函数
 * {module}_*():          与数据表字段名对应的函数
 * prev_{module}():       获取上一条记录的函数
 * next_{module}():       获取下一条记录的函数
 * {module}_parent():     获取父记录的函数
 * {module}_{ex-table}(): 获取从表记录的函数
 */
function register_module_functions($module = ''){
	if(!$module){
		foreach(array_keys(database()) as $module){
			register_module_functions($module); //自动注册函数
		}
		return null;
	}
	$keys = database($module);
	$primkey = get_primkey_by_table($module);
	$parent = in_array($module.'_parent', $keys);
	$code = '
	if(!function_exists("_'.$module.'")){
		function _'.$module.'($key = "", $value = null){
			static $module = array();
			if(!$key) return $module ?: null;
			if($value === null){
				return isset($module[$key]) ? $module[$key] : null;
			}else{
				return $module[$key] = $value;
			}
		}
	}
	if(!function_exists("get_multi_'.$module.'")){
		function get_multi_'.$module.'($arg = array(), $act = "getMulti"){
			static $result = array();
			static $_arg = array();
			static $_act = "getMulti";
			static $i = 0;
			static $sid = "";
			if(is_numeric($arg)){
				if(isset($result["data"][$arg])){
					$i = $arg;
					return the_'.$module.'($result["data"][$i]);
				}else return null;
			}
			if(!$result || (is_assoc($arg) && $_arg != $arg) || $_act != $act || $sid != session_id()) {
				$i = 0;
				the_'.$module.'(null);
				$_arg = $arg;
				$_act = $act;
				$sid = session_id();
				$result = '.$module.'::$act($_arg);
				error(null);
			}
			if(!$result || !$result["success"]) {
				if(_'.$module.'("pages")) _'.$module.'("pages", 0);
				if(_'.$module.'("total")) _'.$module.'("total", 0);
				return null;
			}else if(isset($result["data"][$i])){
				if($i == 0){
					_'.$module.'("limit", $result["limit"]);
					_'.$module.'("total", $result["total"]);
					_'.$module.'("page", $result["page"]);
					_'.$module.'("pages", $result["pages"]);
					_'.$module.'("orderby", $result["orderby"]);
					_'.$module.'("sequence", $result["sequence"]);
					if($act == "search") _'.$module.'("keyword", $result["keyword"]);
				}
				$data = $result["data"][$i];
				$i++;
				if(!$data) return get_multi_'.$module.'();
				return the_'.$module.'($data);
			}else{
				$i = 0;
				return null;
			}
		}
	}
	if(!function_exists("get_search_'.$module.'")){
		function get_search_'.$module.'($arg = array()){
			return get_multi_'.$module.'($arg, "search");
		}
	}
	if(!function_exists("get_'.$module.'")){
		function get_'.$module.'($arg = array()){
			static $result = array();
			static $_arg = array();
			static $sid = "";
			if(is_numeric($arg)) $arg = array("'.$primkey.'"=>$arg);
			if(!$result || (is_assoc($arg) && $_arg != $arg) || $sid != session_id()){
				$_arg = $arg;
				the_'.$module.'(null);
				$result = array();
				$sid = session_id();
				$_result = '.$module.'::get($_arg);
				error(null);
				if(!$_result["success"]) return null;
				else $result = $_result["data"];
			}
			return the_'.$module.'($result);
		}
	}
	if(!function_exists("the_'.$module.'")){
		function the_'.$module.'($key = "", $value = null){
			static $result = array();
			if(is_assoc($key)){
				return ($result = array_merge($result, $key)) ?: null;
			}else if($key && $value !== null){
				return $result[$key] = $value;
			}else if($key === null){
				return ($result = array()) ?: null;
			}
			if(!$key) return $result ?: null;
			else if(isset($result[$key])) return $result[$key];
			else if(strpos($key, "'.$module.'_") !== 0){
				$key = "'.$module.'_".$key;
				return isset($result[$key]) ? $result[$key] : null;
			}else return null;
		}
	}
	if(!function_exists("prev_'.$module.'")){
		function prev_'.$module.'($key = "", $act = "getPrev"){
			static $result = array();
			static $primkey = 0;
			static $_act = "getPrev";
			static $sid = "";
			if(!$result || $_act != $act || $primkey != the_'.$module.'("'.$primkey.'") || $sid != session_id()){
				$result = array();
				$_act = $act;
				$sid = session_id();
				$primkey = the_'.$module.'("'.$primkey.'");
				$arg = array("'.$primkey.'"=>$primkey);
				if(is_array($key)) $arg = array_merge($arg, $key);
				$_result = '.$module.'::$act($arg);
				error(null);
				if(!$_result["success"]) return null;
				else $result = $_result["data"];
			}
			if(!$key || is_array($key)) return $result ?: null;
			else if(isset($result[$key])) return $result[$key];
			else if(strpos($key, "'.$module.'_") !== 0){
				$key = "'.$module.'_".$key;
				return isset($result[$key]) ? $result[$key] : null;
			}else return null;
		}
	}
	if(!function_exists("next_'.$module.'")){
		function next_'.$module.'($key = ""){
			return prev_'.$module.'($key, "getNext");
		}
	}';
	eval($code); //运行代码
	foreach ($keys as $k) {
		if(strpos($k, $module) === 0){
			$func = $k;
			if(stripos($k, '_parent') === false){
				$code = '
				if(!function_exists("'.$func.'")){
					function '.$func.'($key = ""){
						$result = the_'.$module.'("'.$k.'");
						if(!$key) return $result ?: null;
						else if(isset($result[$key])) return $result[$key];
						else if(strpos($key, "'.$module.'_") !== 0){
							$key = "'.$module.'_".$key;
							return isset($result[$key]) ? $result[$key] : null;
						}else return null;
					}
				}';
			}else{
				$code = '
				if(!function_exists("'.$module.'_parent")){
					function '.$module.'_parent($key = ""){
						static $result = array();
						static $primkey = 0;
						static $sid = "";
						if(!$result || $primkey != the_'.$module.'("'.$primkey.'") || $sid != session_id()){
							$result = array();
							$sid = session_id();
							$primkey = the_'.$module.'("'.$primkey.'");
							$parent = the_'.$module.'("'.$module.'_parent");
							if(!$parent) return null;
							$_result = '.$module.'::get(array("'.$primkey.'"=>$parent));
							error(null);
							if(!$_result["success"]) return null;
							else $result = $_result["data"];
						}
						if(!$key) return $result ?: null;
						else if(isset($result[$key])) return $result[$key];
						else if(strpos($key, "'.$module.'_") !== 0){
							$key = "'.$module.'_".$key;
							return isset($result[$key]) ? $result[$key] : null;
						}else return null;
					}
				}';
			}
		}else{
			if($_table = get_table_by_primkey($k)){
				$code = '
				if(!function_exists("'.$module.'_'.$_table.'")){
					function '.$module.'_'.$_table.'($key = ""){
						static $result = array();
						static $primkey = 0;
						static $sid = "";
						if(!$result || $primkey != the_'.$module.'("'.$primkey.'") || $sid != session_id()){
							$result = array();
							$sid = session_id();
							$primkey = the_'.$module.'("'.$primkey.'");
							$_result = the_'.$module.'();
							foreach (database("'.$_table.'") as $k) {
								if(isset($_result[$k])) $result[$k] = $_result[$k];
							}
						}
						if(!$key) return $result ?: null;
						else if(isset($result[$key])) return $result[$key];
						else if(strpos($key, "'.$_table.'_") !== 0){
							$key = "'.$_table.'_".$key;
							return isset($result[$key]) ? $result[$key] : null;
						}else return null;
					}
				}';
			}
		}
		eval($code);
	}
}

/**
 * report_http_error() 报告 HTTP 错误
 * @param string $code 状态码，401，403，404 或 500
 * @param string $msg  [可选]错误提示
 */
function report_http_error($code, $msg = ''){
	$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : (display_file() ?: __SCRIPT__);
	$status = array(
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		500 => 'Internal Server Error',
		);
	$html = array(
		401 => "<p>This server could not verify that you are authorized to access the document requested.</p><p>Either you supplied the wrong credentials (e.g., bad password), or your browser doesn't understand how to supply the credentials required.</p>",
		403 => "<p>You don't have permission to access ".$uri." on this server.</p>",
		404 => "<p>The requested URL ".$uri." was not found on this server.</p>",
		500 => "<p>The server encountered an internal error or misconfiguration and was unable to complete your request.</p><p>Please contact the server administrator".(isset($_SERVER['SERVER_ADMIN']) ? ", {$_SERVER['SERVER_ADMIN']}" : '')." and inform them of the time the error occurred, and anything you might have done that may have caused the error.</p>\n\t<p>More information about this error may be available in the server error log.</p>",
		);
	if(!is_agent() && !$msg) $msg = "$code {$status[$code]}";
	if(is_socket()){
		SocketServer::send(json_encode(error($msg, array(
			'status'=>$code, //状态码
			'statusText'=>$status[$code], //错误信息
			'obj'=>$_GET['obj'],
			'act'=>$_GET['act']
			))));
		return;
	}
	$file = config('site.errorPage.'.$code);
	$file = $file ? template_path($file) : false;
	Header('HTTP/1.1 '.$code.' '.$status[$code]); //添加头部信息
	if($file && file_exists($file) && !$msg){
		display_file($file, true);
	}else{
		echo $msg ?: "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n<html>\n<head>\n\t<title>{$code} {$status[$code]}</title>\n</head>\n<body>\n\t<h1>{$code} {$status[$code]}</h1>\n\t{$html[$code]}\n</body>\n</html>";
		if(is_agent()){
			do_hooks('mod.template.load.complete');
			exit();
		}
	}
}

/**
 * report_401/403/404/500() 报告 401/403/404/500 错误
 * is_401/403/404/500() 判断是否为错误页面
 * @param  string $msg 错误提示
 */
foreach (array(401, 403, 404, 500) as $code) {
	eval('
	function report_'.$code.'($msg = ""){
		report_http_error('.$code.', $msg);
	}
	function is_'.$code.'(){
		return is_template(config("site.errorPage.'.$code.'"));
	}');
}
unset($code);

if(extension_loaded('sockets')):
/**
 * socket_retrive_session() socket 模式下重现会话
 * @param  string $sid   会话 ID
 * @param  array  $event 事件
 * @return bool
 */
function socket_retrive_session($sid, $event){
	global $SOCKET_SESS, $SOCKET_USER;
	if(session_retrieve($sid)){  //重现会话
		$SOCKET_SESS[session_id()] = $event['client']; //将会话 ID 和 socket 客户端绑定
		$uid = me_id();
		if(!isset($SOCKET_USER[$uid])) $SOCKET_USER[$uid] = array();
		if(!in_array($event['client'], $SOCKET_USER[$uid])){
			$SOCKET_USER[$uid][] = $event['client']; //将用户 ID 和 socket 客户端绑定
		}
		return true;
	}
	return false;
}
endif;

/**
 * strapos() 查找字符串中第一次出现的位置，根据操作系统自动决定是否使用大小写敏感
 * @param  string  $str   规定要搜索的字符串
 * @param  string  $find  规定要查找的字符串
 * @param  integer $start [可选]规定在何处开始搜索
 * @return mixed          返回字符串在另一字符串中第一次出现的位置，如果没有找到字符串则返回 FALSE。
 */
function strapos($str, $find, $start = 0){
	return PHP_OS == 'WINNT' ? stripos($str, $find, $start) : strpos($str, $find, $start);
}

/** is_console() 判断是否运行在交互式控制台中 */
function is_console(){
	$files = get_included_files();
	return PHP_SAPI == 'cli' && ((__SCRIPT__ == 'mod.php' && !isset($_SERVER['argv'][1])) || (is_socket() && in_array(realpath(__ROOT__.'mod.php'), $files)));
}

/**
 * config2list() 获取配置的点语法列表，也可以用来获取语言、Api Hook 等等。
 * @param  array  $config     配置数组
 * @param  string $prefix     [可选]前缀
 * @param  string $delimiter  [可选]分隔符，默认 .
 * @param  bool   $bottomOnly [可选]只获取最底层配置，默认 false
 * @return array              一个包含所有传入配置的点语法列表的索引数组
 */
function config2list(array $config, $prefix = '', $delimiter = '.', $bottomOnly = false){
	$paths = array();
	if($prefix && $prefix[strlen($prefix)-1] != $delimiter) $prefix .= $delimiter;
	foreach ($config as $k => $v){
		if(is_array($v)){
			if(!$bottomOnly)
				$paths[] = $prefix.$k;
			$paths = array_merge($paths, config2list($v, $prefix.$k, $delimiter, $bottomOnly)); //递归获取
		}else{
			$paths[] = $prefix.$k;
		}
	}
	return $paths;
}

/**
 * get_module_funcs() 获取自动创建的模块函数
 * @param  string $module [可选]模块名称
 * @return array          由所有模块函数组成的数组
 */
function get_module_funcs($module = ''){
	if(!$module){
		$modules = database();
		foreach ($modules as $key => &$value) {
			$value = get_module_funcs($key);
		}
		return $modules;
	}
	$funcs = get_defined_functions();
	$funcs = $funcs['user'];
	$_funcs = array(
		'_'.$module,
		'get_'.$module,
		'get_multi_'.$module,
		'get_search_'.$module,
		'the_'.$module,
		'prev_'.$module,
		'next_'.$module,
		);
	$keys = database($module);
	foreach ($keys as $key) {
		if(strpos($key, $module.'_') === 0){
			$_funcs[] = $key;
		}elseif($extable = get_table_by_primkey($key)){ //判断是否存在外表
			$_funcs[] = $module.'_'.$extable;
		}
	}
	foreach ($_funcs as $i => $func) {
		if(!in_array($func, $funcs)) unset($_funcs[$i]); //去除不存在的函数名
	}
	return $_funcs;
}

if(!function_exists('mime_content_type')):
/**
 * mime_content_type() 获取一个文件的 MIME 类型
 * @param  string $filename 文件名
 * @return string           文件的 MIME 类型，如果没有匹配，则返回空字符串
 */
function mime_content_type($filename){
	static $mime = array();
	if(!$mime) $mime = load_config_file('mime.ini'); //加载 mime 类型扩展
	$ext = '.'.extname($filename);
	return isset($mime[$ext]) ? $mime[$ext] : "";
}
endif;

/**
 * http_auth_login() 使用 HTTP 访问认证登录账户
 * @param  string $realm [可选]设置域信息
 * @param  string $type  [可选]认证方式，1：基本认证(默认)，2：摘要认证(仅系统未安装时有效)
 *                       如果使用摘要认证登录，那么程序会自动生成一个全局变量 $digest
 *                       来保存解析后的认证信息。
 * @return array         操作结果
 */
function http_auth_login($realm = "HTTP Authentication", $type = 1){
	if(!config('mod.installed') && ($type === 2 || !empty($_SERVER['PHP_AUTH_DIGEST']))){
		$key = config('user.password.encryptKey'); //密码解密密钥
		$users = array();
		$userMeta = array();
		foreach(load_config_file('users.php') as $i => $user){ //遍历用户
			$user = explode(':', $user);
			if(count($user) == 3){ //合法的用户描述符
				$users[$user[0]] = decrypt($user[1], $key);
				$userMeta[$user[0]] = array(
					'user_id' => $i+1,
					'user_level' => (int)$user[2]
					);
			}
		}
		$username = http_digest_auth($users, 'report_401', $realm, $GLOBALS['digest']);
		_user('me_id', $userMeta[$username]['user_id']); //设置登录信息
		_user('me_level', $userMeta[$username]['user_level']);
		return user::getMe();
	}else{
		if(empty($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
			header('WWW-Authenticate: Basic realm="'.$realm.'"'); //发送要求验证头部
			report_401(); //报告 401 并阻止输出
		}else{
			$loginKey = config('user.keys.login');
			$loginKey = strstr($loginKey, '|', true) ?: $loginKey;
			$arg = array( //登陆参数
				$loginKey => $_SERVER['PHP_AUTH_USER'], //用户登录字段
				'user_password' => $_SERVER['PHP_AUTH_PW'] //用户密码
				);
			$result = user::login($arg); //登录
			if(!$result['success']){
				unset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
				http_auth_login($realm);
			}
			return $result;
		}
	}
}