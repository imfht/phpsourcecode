<?php
/**
 * 系统核心函数库
 * @author  lajox <lajox@19www.com>
 */
/**
 * 加载核心模型
 * @param string $name Model名称
 * @param string $prefix 表前缀
 * @return Took\Model 返回模型对象
 */
function M($name = null, $prefix = '', $param = array(), $driver = null)
{
    return new Took\Model($name, $prefix, $param, $driver);
}

/**
 * 获得扩展模型
 * @param string $name Model名称
 * @param string $prefix 表前缀
 * @param array $param 参数
 * @return object
 */
function K($name, $prefix = '', $param = array())
{
    if(empty($name)) return new Took\Model;
    $layer = C('MODEL_FIX');
    $name = str_replace('\\', '/', $name);
    $class = parse_res_name($name, $layer, true);
    require_array(array(APP_MODULE_PATH . $class, APP_PATH . $class));
    if(class_exists($class)) {
        $model = new $class(basename($name));
    }elseif(false === strpos($name,'/')){
        // 自动加载公共模块下面的模型
        import('Common/'.$layer.'/'.$class);
        $class = '\\Common\\'.$layer.'\\'.$name.$layer;
        $model = class_exists($class)? new $class($name, $prefix, $param) : new Took\Model($name, $prefix, $param);
    }else {
        Took\Log::record('K方法实例化没找到模型类'.$class, Took\Log::NOTICE);
        $model = new Took\Model(basename($name), $prefix, $param);
    }
    return $model;
}

/**
 * @param string $name Model名称
 * @param string $prefix 表前缀
 * @return Took\Model\RelationModel
 */
function R($name = null, $prefix = '')
{
    return new Took\Model\RelationModel($name, $prefix);
}

/**
 * 获得视图模型
 * @param null $table 表名
 * @param null $full 带前缀
 * @return Took\Model\ViewModel
 */
function V($table = null, $full = null)
{
    return new Took\Model\ViewModel($table, $full);
}

/**
 * 记录和统计时间（微秒）和内存使用情况
 * 使用方法:
 * <code>
 * G('begin'); // 记录开始标记位
 * // ... 区间运行代码
 * G('end'); // 记录结束标签位
 * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
 * echo G('begin','end','m'); // 统计区间内存使用情况
 * 如果end标记位没有定义，则会自动以当前作为标记位
 * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
 * </code>
 * @param string $start 开始标签
 * @param string $end 结束标签
 * @param integer|string $dec 小数位或者m
 * @return mixed
 */
function G($start,$end='',$dec=4) {
    static $_info       =   array();
    static $_mem        =   array();
    if(is_float($end)) { // 记录时间
        $_info[$start]  =   $end;
    }elseif(!empty($end)){ // 统计时间和内存使用
        if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
        if($dec=='m'){
            if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
            return number_format(($_mem[$end]-$_mem[$start])/1024);
        }else{
            return number_format(($_info[$end]-$_info[$start]),$dec);
        }

    }else{ // 记录时间和内存使用
        $_info[$start]  =  microtime(TRUE);
        $_mem[$start]   =  memory_get_usage();
    }
    return null;
}

/**
 * 快速缓存 以文件形式缓存
 * @param String $name 缓存KEY
 * @param bool $value 删除缓存
 * @param string $path 缓存目录
 * @return bool
 */
function F($name, $value = false, $path = TEMP_CACHE_PATH)
{
    static $_cache = array();
    $cacheFile = rtrim($path, '/') . '/' . $name . '.php';
    if (is_null($value)) {
        if (is_file($cacheFile)) {
            unlink($cacheFile);
            unset($_cache[$name]);
        }
        return true;
    }
    if (1 == func_num_args()) {
        if (isset($_cache[$name]))
            return $_cache[$name];
        return is_file($cacheFile) ?
            include $cacheFile : null;
    }
    $data = "<?php return " . compress(var_export($value, true)) . ";\n?>";
    is_dir($path) || dir_create($path);
    if (!file_put_contents($cacheFile, $data)) {
        return false;
    }
    $_cache[$name] = $value;
    return true;
}

/**
 * 缓存处理
 * @param string $name 缓存名称
 * @param bool $value 缓存内容
 * @param null $expire 缓存时间 (秒)
 * @param array $options 选项:
 *          e.g. $options = array("driver"=>"file","dir"=>"Cache")
 *          支持的driver: file, memcache, redis
 * e.g
 *  S('key1','value1', 5, array('driver'=>'file','dir'=>'Cache'));
 *  S('key2','value2', 5, array('driver'=>'file','dir'=>TEMP_CACHE_PATH));
 *  S('key3','value3', 5, array('driver'=>'memcache'));
 *  S('key4','value4', 5, array('driver'=>'redis'));
 *
 * @return bool
 */
function S($name, $value = false, $expire = null, $options = array())
{
    //缓存数据
    static $_data = array();
    //实例缓存对象
    $cacheObj = Took\Cache::init($options);
    if (is_null($value)) {
        return $cacheObj->del($name);
    }
    $driver = isset($options['driver']) ? $options['driver'] : '';
    $key = $name . $driver;
    if ($value === false) {
        if (isset($_data[$key])) {
            Took\Debug::$cache['read_s']++;
            return $_data[$key];
        } else {
            return $cacheObj->get($name, $expire);
        }
    }
    $cacheObj->set($name, $value, $expire);
    $_data[$key] = $value;
    return true;
}

/**
 * 执行控制器中的方法
 * @param string $arg  模块/控制器/方法
 * @param array $args 参数
 * @return mixed
 */
function A($arg, $args = array())
{
    $arg = str_replace('.', '/', $arg);
    $pathArr = explode('/', $arg);
    list($module, $controller, $action) = array(MODULE, CONTROLLER, ACTION);
    switch (count($pathArr)) {
        case 1 :
            //当前控制器
            list($action) = $pathArr;
            $file = COMMON_CONTROLLER_PATH . CONTROLLER . C('CONTROLLER_FIX') . EXT;
            break;
        case 2 :
            //当前应用其他控制器
            list($controller, $action) = $pathArr;
            $file = COMMON_CONTROLLER_PATH . $controller . C('CONTROLLER_FIX') . EXT;
            break;
        case 3 :
            //其它模块控制器与方法
            list($module, $controller, $action) = $pathArr;
            $file = APP_MODULE_PATH . '../' . $module . '/'.C('CONTROLLER_FIX').'/' . $controller . C('CONTROLLER_FIX') . EXT;
            break;
    }
    //控制器名
    $class = $module.'\\'.C('CONTROLLER_FIX').'\\'. $controller . C('CONTROLLER_FIX');
    if (require_cache($file)) {
        if (class_exists($class)) {
            $obj = new $class();
            if (method_exists($class, $action)) {
                if (empty($args)) {
                    return $obj->$action();
                } else {
                    return call_user_func_array(array(&$obj, $action), $args);
                }
            }
        }
    }
}

/**
 * 根据配置文件的URL参数重新生成URL地址
 * @param String $path 访问url
 * @param array $args GET参数
 *                     <code>
 *                     $args = "nid=2&cid=1"
 *                     $args=array("nid"=>2,"cid"=>1)
 *                     </code>
 * @param int $type 指定URL类型 1:PATHINFO模式 2:普通模式 3:兼容模式
 * @return string
 */
function U($path, $args = array(), $type = 0)
{
    return Took\Route::getUrl($path, $args, $type);
}

/**
 * 记录缓存读写与数据库操作次数
 * @param string $name 缓存的KEY
 * @param int $num 缓存次数
 * @return void
 */
function N($name, $num = NULL)
{
    //记数静态变量
    static $data = array();
    if (!isset($data[$name])) {
        $data[$name] = 0;
    }
    if (is_null($num)) { //获得计数
        return $data[$name];
    } else { //更改缓存记数
        $data[$name] += (int)$num;
    }
}

/**
 * 载入或设置配置顶
 * @param string $name 配置名
 * @param string $value 配置值
 * @return mixed
 */
function C($name = null, $value = null)
{
    static $config = array();
    if (is_null($name)) {
        return $config;
    } else if (is_string($name)) {
        $name = strtoupper($name);
        $data = array_change_key_case($config, CASE_UPPER);
        if (!strstr($name, '.')) {
            //获得配置
            if (is_null($value)) {
                return isset($data[$name]) ? $data[$name] : null;
            } else {
                return $config[$name] = isset($data[$name]) && is_array($data[$name]) && is_array($value) ? array_merge($config[$name], $value) : $value;
            }
        } else {
            //二维数组
            $name = array_change_key_case(explode(".", $name));
            if (is_null($value)) {
                return isset($data[$name[0]][$name[1]]) ? $data[$name[0]][$name[1]] : null;
            } else {
                return $config[$name[0]][$name[1]] = $value;
            }
        }
    } else if (is_array($name)) {
        return $config = array_merge($config, array_change_key_case($name, CASE_UPPER));
    }
}

//加载语言处理
function L($name = null, $value = null)
{
    static $languge = array();
    if (is_null($name)) {
        return $languge;
    }
    if (is_string($name)) {
        $name = strtolower($name);
        if (!strstr($name, '.')) {
            if (is_null($value))
                return isset($languge[$name]) ? $languge[$name] : null;
            $languge[$name] = $value;
            return $languge[$name];
        }
        //二维数组
        $name = array_change_key_case_d(explode(".", $name), 0);
        if (is_null($value)) {
            return isset($languge[$name[0]][$name[1]]) ? $languge[$name[0]][$name[1]] : null;
        }
        $languge[$name[0]][$name[1]] = $value;
    }
    if (is_array($name)) {
        $languge = array_merge($languge, array_change_key_case_d($name));
        return true;
    }
}

/**
 * 获取与设置请求参数
 * @param string $var   参数如 Q("cid) Q("get.cid") Q("get.")
 * @param null $default 默认值 当变量不存在时的值
 * @param null $filter  过滤函数
 * @return array|null
 */
function Q($var, $default = null, $filter = null)
{
    //拆分，支持get.id  或 id
    $var = explode(".", $var);
    if (count($var) == 1) {
        array_unshift($var, 'request');
    }
    $var[0] = strtolower($var[0]);
    //获得数据并执行相应的安全处理
    switch (strtolower($var[0])) {
        case 'get' :
            $data = &$_GET;
            break;
        case 'post' :
            $data = &$_POST;
            break;
        case 'request' :
            $data = &$_REQUEST;
            break;
        case 'files' :
            $data = &$_FILES;
            break;
        case 'session' :
            $data = &$_SESSION;
            break;
        case 'cookie' :
            $data = &$_COOKIE;
            break;
        case 'server' :
            $data = &$_SERVER;
            break;
        case 'globals' :
            $data = &$GLOBALS;
            break;
        default :
            throw_exception($var[0] . 'Q方法参数错误');
    }
    //没有执行参数如q("post.")时返回所有数据
    if (empty($var[1])) {
        return $data;
        //如果存在数据如$this->_get("page")，$_GET中存在page数据
    } else if (isset($data[$var[1]])) {
        //要获得参数如$this->_get("page")中的page
        $value = $data[$var[1]];
        //对参数进行过滤的函数
        $funcArr = is_null($filter) ? C("FILTER_FUNCTION") : $filter;
        //参数过滤函数
        if (is_string($funcArr) && !empty($funcArr)) {
            $funcArr = explode(",", $funcArr);
        }
        //是否存在过滤函数
        if (!empty($funcArr) && is_array($funcArr)) {
            //对数据进行过滤处理
            foreach ($funcArr as $func) {
                if (!function_exists($func))
                    continue;
                $value = is_array($value) ? array_map($func, $value) : $func($value);
            }
            $data[$var[1]] = $value;
            return $value;
        }
        return $value;

    } else {
        $data[$var[1]] = $default;
        return $default;
    }
}

/**
 * 生成对象或执行对象方法
 * @param string $class  类名
 * @param string $method 方法
 * @param array $args 参数
 * @return mixed
 */
function O($class, $method = null, $args = array())
{
    $tmp = explode(".", $class);
    $class = array_pop($tmp);
    if (!class_exists($class)) {
        $path = $tmp ? implode('.', $tmp) : null;
        import($class, $path);
    }
    if (class_exists($class, false)) {
        $obj = new $class();
        if (!is_object($obj))
            return false;
        if ($method && method_exists($obj, $method)) {
            if (empty($args)) {
                $args = array();
            }
            return call_user_func_array(array($obj, $method), $args);
        } else {
            return $obj;
        }
    }
}

/**
 * 设置当前页面的布局
 * @param string|false $layout 布局名称 为false的时候表示关闭布局
 * @return void
 */
function layout($layout) {
    if(false !== $layout) {
        // 开启布局
        C('LAYOUT_ON',true);
        if(is_string($layout)) { // 设置新的布局模板
            C('LAYOUT_NAME',$layout);
        }
    }else{// 临时关闭布局
        C('LAYOUT_ON',false);
    }
}

/**
 * 解析资源地址并导入类库文件
 * 例如 module/controller
 * @param string $name 资源地址 格式：[模块/]资源名
 * @param string $layer 分层名称
 * @param bool $ns 是否使用命名空间
 * @return string
 */
function parse_res_name($name, $layer = '', $ns = false) {
    // 指定模块
    if(strpos($name,'/') && substr_count($name, '/')>=1){
        list($module,$name) =  explode('/',$name,2);
    }else{
        $module = defined('MODULE') ? MODULE : '' ;
    }
    $module = $module ? $module : "Common";
    if(!$ns){
        $class = parse_name($name, 1);
        import($module.'/'.($layer ? $layer.'/' : $layer).$class.$layer);
    }else{
        $array = explode('/',$name);
        $class = $module. ($layer ? '\\'.$layer : $layer);
        foreach($array as $name){
            $class .= '\\'.parse_name($name, 1);
        }
    }
    return $class.$layer;
}

/**
 * 字符串命名风格转换
 * type 0 将C风格转换为Java的风格 1 将Java风格转换为C的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 类库导入
 * @param null $class 类名
 * @param null $base 目录
 * @param string $ext 扩展名
 * @return bool
 */
function import($class = null, $base = null, $ext = EXT)
{
    $class = str_replace(array('\\', '.', '#'), array('/', '/', '.'), $class);
    if (empty($base)) {
        $info = explode("/", $class);
        if ('@' == $info[0] || MODULE == $info[0]) {
            // 当前模块下类文件
            $base = APP_MODULE_PATH;
            $class = substr_replace($class, '', 0, strlen($info[0]) + 1);
        } elseif (strtoupper($info[0]) == strtoupper(basename(TOOK_CORE_PATH))) {
            // 框架中的类文件: Took
            $base = dirname(substr_replace($class, TOOK_CORE_PATH, 0, strlen(basename(TOOK_CORE_PATH))+1)) . '/';
            $class = basename($class);
        } elseif (strtoupper($info[0]) == strtoupper(basename(TOOK_TOOL_PATH))) {
            // 工具包: Tool
            $base = TOOK_TOOL_PATH;
            $class = substr($class, strlen(basename(TOOK_TOOL_PATH))+1);
        } elseif (FALSE === strpos($class,'/')) {
            // COMMON模块下Lib目录: Common/Library
            $base = COMMON_LIB_PATH;
        } elseif (count($info)==2) {
            // 公共模块的类库: Common
            $base = APP_COMMON_PATH;
        }elseif (basename(APP_COMMON_PATH) == $info[0]) {
            // 公共模块的类库: Common
            $base = APP_COMMON_PATH;
            $class = substr($class, 7);
        } else {
            $base = APP_PATH. ltrim(dirname($class), '/') . '/';
            $class = basename($class);
        }
    } else {
        $base = str_replace('.', '/', $base);
    }
    // 类文件
    $file = $base . $class . $ext;
    if (!class_exists(str_replace('/','\\',$class), false)) {
        return require_cache($file);
    }
    return true;
}

/**
 * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
 * @param string $class 类库
 * @param string $baseUrl 基础目录
 * @param string $ext 类库后缀
 * @return boolean
 */
function vendor($class, $baseUrl = '', $ext='.php') {
    if (empty($baseUrl))
        $baseUrl = TOOK_VENDOR_PATH;
    return import($class, $baseUrl, $ext);
}

/**
 * 加载文件并缓存
 * @param null $path 导入的文件
 * @return bool
 */
function require_cache($path = null)
{
    // 文件缓存
    static $_files = array();
    // 加载过的文件列表
    if (is_null($path)) {
        return $_files;
    }
    // 已经加载过
    if (isset($_files[$path])) {
        return true;
    }
    // 区分大小写的文件判断
    if (!file_exists_case($path)) {
        return false;
    }
    if(file_exists($path)) {
        $path = str_replace('\\', '/', realpath($path));
    }
    //加载文件并记录缓存
    G('fileLoadStart');
    require_once($path);
    G('fileLoadEnd');
    $_files[$path] = G('fileLoadStart','fileLoadEnd','m');
    return true;
}

/**
 * 导入文件数组
 */
function require_array($fileArr)
{
    foreach ($fileArr as $file) {
        if (is_file($file) && require_cache($file)) return true;
    }
    return false;
}

/**
 * 别名导入
 * @param string | array $name 别名
 * @param string $path 文件路径
 * @return bool
 */
function alias_import($name = null, $path = null)
{
    /**
     * 别名缓存
     */
    static $_alias = array();
    /**
     * 返回别名定义数组
     */
    if (is_null($name)) {
        return $_alias;
    }
    if (is_array($name)) {
        /**
         * 批量导入别名定义
         */
        $_alias = array_merge($_alias, array_change_key_case($name));
        return true;
    } else if (!is_null($path)) {
        /**
         * 定义一条别名规则
         */
        return $_alias[$name] = $path;
    } else if (isset($_alias[strtolower($name)])) {
        /**
         * 加载别名定义文件
         */
        return require_cache($_alias[$name]);
    }
    return false;
}

/**
 * 加载动态扩展文件
 * @var string $path 文件路径
 * @return void
 */
function load_ext_file($path) {
    // 加载自定义外部文件
    if($files = C('LOAD_EXT_FILE')) {
        !is_array($files) AND $files = explode(',',$files);
        foreach ($files as $file){
            $file = $path.'Function/'.$file.'.php';
            if(is_file($file) && require_cache($file)) require_cache($file);
        }
    }
    // 加载自定义的动态配置文件
    if($configs = C('LOAD_EXT_CONFIG')) {
        if(is_string($configs)) $configs =  explode(',',$configs);
        foreach ($configs as $key=>$config){
            $file = is_file($config)? $config : $path.'Config/'.$config.CONF_EXT;
            if(is_file($file)) {
                is_numeric($key)?C(load_config($file)):C($key,load_config($file));
            }
        }
    }
}

/**
 * 加载配置文件 支持格式转换 仅支持一级配置
 * @param string $file 配置文件名
 * @param string $parse 配置解析方法 有些格式需要用户自己解析
 * @return array
 */
function load_config($file,$parse=''){
    $ext  = pathinfo($file, PATHINFO_EXTENSION);
    switch($ext){
        case 'php':
            return include $file;
        case 'ini':
            return parse_ini_file($file);
        case 'yaml':
            return yaml_parse_file($file);
        case 'xml':
            return (array)simplexml_load_file($file);
        case 'json':
            return json_decode(file_get_contents($file), true);
        default:
            if(function_exists($parse)){
                return $parse($file);
            }else{
                halt('系统不支持:'.$ext);
            }
    }
}

/**
 * 获得控制器类名（含命名空间路径）
 */
function getController($name, $path='')
{
    $layer = C('CONTROLLER_FIX');
    $class = ( $path ? basename(APP_ADDON_PATH).'\\'.$path : MODULE ).'\\'.$layer;
    $array = explode('/',$name);
    foreach($array as $name){
        $class .= '\\'.parse_name($name, 1);
    }
    $class .= $layer;
    return $class;
}

/**
 * 实例化控制器并执行方法
 * @param string $class  控制器
 * @param null $method 方法
 * @param array $args 参数
 * @return bool|mixed
 */
function controller($class, $path='', $method = NULl, $args = array())
{
    $classFile = $class . C('CONTROLLER_FIX') . EXT;
    if (require_array(array(MODULE_CONTROLLER_PATH . $classFile))) {
        $class = getController($class, $path);
        if (class_exists($class)) {
            $obj = new $class();
            if ($method && method_exists($obj, $method)) {
                return call_user_func_array(array(&$obj, $method), $args);
            }
            return $obj;
        }
    } else {
        return false;
    }
}

/**
 * session处理
 * @param string|array $name 数组为初始session
 * @param string $value 值
 * @return mixed
 */
function session($name = '', $value = '')
{
    if (is_array($name)) {
        ini_set('session.auto_start', 0);
        if (isset($name['name']))
            session_name($name['name']);
        if (isset($_REQUEST[session_name()]))
            session_id($_REQUEST[session_name()]);
        if (isset($name['path']))
            session_save_path($name['path']);
        if (isset($name['domain']))
            ini_set('session.cookie_domain', $name['domain']);
        if (isset($name['expire'])) {
            ini_set('session.gc_maxlifetime', $name['expire']);
            session_set_cookie_params($name['expire']);
        }
        if (isset($name['use_trans_sid']))
            ini_set('session.use_trans_sid', $name['use_trans_sid'] ? 1 : 0);
        if (isset($name['use_cookies']))
            ini_set('session.use_cookies', $name['use_cookies'] ? 1 : 0);
        if (isset($name['cache_limiter']))
            session_cache_limiter($name['cache_limiter']);
        if (isset($name['cache_expire']))
            session_cache_expire($name['cache_expire']);
        if (isset($name['type'])) {
            $class = '\\Took\\Session\\Driver\\'.ucwords($name['type']);
            $hander = new $class();
            $hander->run();
        }
        //自动开启SESSION
        if (C("SESSION_AUTO_START")) {
            session_start();
        }
    } elseif ($name === '') {
        return $_SESSION;
    } elseif (is_null($name)) {
        $_SESSION = array();
        session_unset();
        session_destroy();
    } elseif ($value === '') {
        if ('[pause]' == $name) { // 暂停
            session_write_close();
        } elseif ('[start]' == $name) { //开启
            session_start();
        } elseif ('[destroy]' == $name) { //销毁
            $_SESSION = array();
            session_unset();
            session_destroy();
        } elseif ('[regenerate]' == $name) { //生成id
            session_regenerate_id();
        } elseif (0 === strpos($name, '?')) { // 检查session
            $name = substr($name, 1);
            return isset($_SESSION[$name]);
        } elseif (is_null($name)) { // 清空session
            $_SESSION = array();
        } else {
            return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
        }
    } elseif (is_null($value)) { // 删除session
        if (isset($_SESSION[$name]))
            unset($_SESSION[$name]);
    } else { //设置session
        $_SESSION[$name] = $value;
    }
}

/**
 * cookie处理
 * @param string $name  名称
 * @param string $value 值
 * @param mixed $option 选项
 * @return mixed
 */
function cookie($name, $value = '', $option = array())
{
    // 默认设置
    $config = array('prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
        'path' => C('COOKIE_PATH'), // cookie 保存路径
        'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE)) return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) { // 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return $_COOKIE;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        // 获取指定Cookie
        return isset($_COOKIE[$name]) ? json_decode(MAGIC_QUOTES_GPC ? stripslashes($_COOKIE[$name]) : $_COOKIE[$name]) : null;
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]);
            // 删除指定cookie
        } else {
            // 设置cookie
            $value = json_encode($value);
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

/**
 * 插件URL
 * @param $path
 * @param $param
 * @return mixed
 */
function addon_url($path, $param = array())
{
    if (!empty($param)) {
        $param = '&' . http_build_query($param);
    } else {
        $param = '';
    }
    $info = explode('/', $path);
    switch (count($info)) {
        case 3:
            $url = __ROOT__ . "/index.php?g=Addon&m={$info[0]}&c={$info[1]}&a={$info[2]}" . $param;
            break;
        case 2:
            $url = __ROOT__ . "/index.php?g=Addon&m=" . MODULE . "&c={$info[0]}&a={$info[1]}" . $param;
            break;
        case 1:
            $url = __ROOT__ . "/index.php?g=Addon&m=" . MODULE . "&c=" . CONTROLLER . "&a={$info[0]}" . $param;
            break;
    }
    return $url;
}

/**
 * 获得浏览器版本
 */
function browser_info()
{
    $agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
    $browser = null;
    if (strstr($agent, 'msie 9.0')) {
        $browser = 'msie9';
    } else if (strstr($agent, 'msie 8.0')) {
        $browser = 'msie8';
    } else if (strstr($agent, 'msie 7.0')) {
        $browser = 'msie7';
    } else if (strstr($agent, 'msie 6.0')) {
        $browser = 'msie6';
    } else if (strstr($agent, 'firefox')) {
        $browser = 'firefox';
    } else if (strstr($agent, 'chrome')) {
        $browser = 'chrome';
    } else if (strstr($agent, 'safari')) {
        $browser = 'safari';
    } else if (strstr($agent, 'opera')) {
        $browser = 'opera';
    }
    return $browser;
}

/**
 * 计算脚本运行时间
 * 传递$end参数时为得到执行时间
 * @param string $start 开始标识
 * @param string $end 结束标识
 * @param int $decimals 小数位
 * @return string
 */
function runtime($start, $end = '', $decimals = 3)
{
    static $runtime = array();
    if ($end != '') {
        $runtime [$end] = microtime();
        return number_format($runtime [$end] - $runtime [$start], $decimals);
    }
    $runtime [$start] = microtime();
}

/**
 * 是否为AJAX提交
 * @return boolean
 */
function ajax_request()
{
    if ( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')]) ) {
        return true;
    }
    return false;
}

/**
 * 对数组或字符串进行转义处理，数据可以是字符串或数组及对象
 * @param void $data
 * @return type
 */
function addslashes_d($data)
{
    if (is_string($data)) {
        return addslashes($data);
    }
    if (is_numeric($data)) {
        return $data;
    }
    if (is_array($data)) {
        $var = array();
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $var[$k] = addslashes_d($v);
                continue;
            } else {
                $var[$k] = addslashes($v);
            }
        }
        return $var;
    }
}

/**
 * 去除转义
 * @param type $data
 * @return type
 */
function stripslashes_d($data)
{
    if (empty($data)) {
        return $data;
    } elseif (is_string($data)) {
        return stripslashes($data);
    } elseif (is_array($data)) {
        $var = array();
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $var[$k] = stripslashes_d($v);
                continue;
            } else {
                $var[$k] = stripslashes($v);
            }
        }
        return $var;
    }
}

/**
 * 将数组转为字符串表示形式
 * @param array $array 数组
 * @param int $level 等级不要传参数
 * @return string
 */
function array_to_string($array, $level = 0)
{
    if (!is_array($array)) {
        return "'" . $array . "'";
    }
    $space = '';
    //空白
    for ($i = 0; $i <= $level; $i++) {
        $space .= "\t";
    }
    $arr = "Array\n$space(\n";
    $c = $space;
    foreach ($array as $k => $v) {
        $k = is_string($k) ? '\'' . addcslashes($k, '\'\\') . '\'' : $k;
        $v = !is_array($v) && (!preg_match("/^\-?[1-9]\d*$/", $v) || strlen($v) > 12) ? '\'' . addcslashes($v, '\'\\') . '\'' : $v;
        if (is_array($v)) {
            $arr .= "$c$k=>" . array_to_string($v, $level + 1);
        } else {
            $arr .= "$c$k=>$v";
        }
        $c = ",\n$space";
    }
    $arr .= "\n$space)";
    return $arr;
}

/**
 * 根据类型获得图像扩展名
 */
if (!function_exists('image_type_to_extension')) {

    function image_type_to_extension($type, $dot = true)
    {
        $e = array(1 => 'gif', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc', 'aiff', 'wbmp', 'xbm');
        $type = (int)$type;
        return ($dot ? '.' : '') . $e[$type];
    }

}

/**
 * 获得随机字符串
 * @param int $len 长度
 * @return string
 */
function rand_str($len = 6)
{
    $data = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $str = '';
    while (strlen($str) < $len)
        $str .= substr($data, mt_rand(0, strlen($data) - 1), 1);
    return $str;
}

/**
 * 加密方法
 * @param      $data 加密字符串
 * @param null $key 密钥
 * @return mixed|string
 */
function encrypt($data, $key = null)
{
    return \Tool\Encry::encrypt($data, $key);
}

/**
 * 解密方法
 * @param string $data 解密字符串
 * @param null $key 密钥
 * @return mixed
 */
function decrypt($data, $key = null)
{
    return \Tool\Encry::decrypt($data, $key);
}

/**
 * 数据安全处理
 * @param      $data 要处理的数据
 * @param null $func 安全的函数
 * @return array|string
 */
function data_format(&$data, $func = null)
{
    $functions = is_null($func) ? C("FILTER_FUNCTION") : $func;
    if (!is_array($functions)) {
        $functions = preg_split("/\s*,\s*/", $functions);
    }
    foreach ($functions as $_func) {
        if (is_string($data)) { //字符串数据
            $data = $_func($data);
        } else if (is_array($data)) { //数组数据
            foreach ($data as $k => $d) {
                $data[$k] = is_array($d) ? data_format($d, $functions) : $_func($d);
            }
        }
    }
    return $data;
}

/**
 * 获得变量值
 * @param string $varName 变量名
 * @param mixed $value 值
 * @return mixed
 */
function _default($varName, $value = "")
{
    return empty($varName) ? $value : $varName;
}

/**
 * 请求方式
 * @param string $method 类型
 * @param string $varName 变量名
 * @param bool $html 实体化
 * @return mixed
 */
function _request($method, $varName = null, $html = true)
{
    $method = strtolower($method);
    switch ($method) {
        case 'ispost' :
        case 'isget' :
        case 'ishead' :
        case 'isdelete' :
        case 'isput' :
            return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method, 2));
        case 'get' :
            $data = &$_GET;
            break;
        case 'post' :
            $data = &$_POST;
            break;
        case 'request' :
            $data = &$_REQUEST;
            break;
        case 'Session' :
            $data = &$_SESSION;
            break;
        case 'cookie' :
            $data = &$_COOKIE;
            break;
        case 'server' :
            $data = &$_SERVER;
            break;
        case 'globals' :
            $data = &$GLOBALS;
            break;
        default :
            throw_exception('abc');
    }
    //获得所有数据
    if (is_null($varName))
        return $data;
    if (isset($data[$varName]) && $html) {
        $data[$varName] = htmlspecialchars($data[$varName]);
    }
    return isset($data[$varName]) ? $data[$varName] : null;
}

/**
 * HTTP状态信息设置
 * @param Number $code 状态码
 */
function set_http_state($code)
{
    $state = array(
        // Success 2xx
        200 => 'OK',
        // Redirection 3xx
        301 => 'Moved Permanently', 302 => 'Moved Temporarily ',
        // Client Error 4xx
        400 => 'Bad Request', 403 => 'Forbidden', 404 => 'Not Found',
        // Server Error 5xx
        500 => 'Internal Server Error', 503 => 'Service Unavailable',
    );
    if (isset($state[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $state[$code]);
        header('Status:' . $code . ' ' . $state[$code]);
        //FastCGI模式
    }
}

/**
 * 是否为SSL协议
 * @return boolean
 */
function is_ssl()
{
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }
    return false;
}

/**
 * 打印常量
 * @return array
 */
function print_const()
{
    $define = get_defined_constants(true);
    $const = array();
    foreach ($define['user'] as $k => $d) {
        $const[$k] = $d;
    }
    show($const);
}

/**
 * 获得几天前，几小时前，几月前
 * @param int $time 时间戳
 * @param array $unit 时间单位
 * @return bool|string
 */
function date_before($time, $unit = null)
{
    $time = intval($time);
    $unit = is_null($unit) ? array("年", "月", "星期", "天", "小时", "分钟", "秒") : $unit;
    switch (true) {
        case $time < (NOW - 31536000) :
            return floor((NOW - $time) / 31536000) . $unit[0] . '前';
        case $time < (NOW - 2592000) :
            return floor((NOW - $time) / 2592000) . $unit[1] . '前';
        case $time < (NOW - 604800) :
            return floor((NOW - $time) / 604800) . $unit[2] . '前';
        case $time < (NOW - 86400) :
            return floor((NOW - $time) / 86400) . $unit[3] . '前';
        case $time < (NOW - 3600) :
            return floor((NOW - $time) / 3600) . $unit[4] . '前';
        case $time < (NOW - 60) :
            return floor((NOW - $time) / 60) . $unit[5] . '前';
        default :
            return floor(NOW - $time) . $unit[6] . '前';
    }
}

/**
 * 获得唯一uuid值
 * @param string $sep 分隔符
 * @return string
 */
function get_uuid($sep = '')
{
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        mt_srand((double)microtime() * 10000);
        //optional for php 4.2.0 and up.
        $id = strtoupper(md5(uniqid(rand(), true)));
        $uuid = substr($id, 0, 8) . $sep . substr($id, 8, 4) . $sep . substr($id, 12, 4) . $sep . substr($id, 16, 4) . $sep . substr($id, 20, 12);
        return $uuid;
    }
}

/**
 * 日期格式化
 * 使用自定义标签时格式化标准ISO日期
 * @param int $timestamp 时间戳
 * @param string $format
 * @return bool|string
 */
function std_date($timestamp = 0, $format = 'Y-m-d')
{
    $timestamp = $timestamp ? $timestamp : time();
    return date($format, $timestamp);
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 截取长度
 * 使用自定义标签时截取字符串
 * @param string $string 字符串
 * @param int $len 长度
 * @param string $end 结尾符
 * @return string
 */
function cutstr($string, $len = 20, $end = '...')
{
    $con = mb_substr($string, 0, $len, 'utf-8');
    if ($con != $string) {
        $con .= $end;
    }
    return $con;
}

/**
 * 生成序列字符串
 * @param $var
 * @return string
 */
function md5_d($var)
{
    return md5(serialize($var));
}

/**
 * Hash函数
 */
function hash_d($data, $len)
{
    $hash = crc32($data) & 0xfffffff;
    return $hash % $len;
}

/**
 * 检测是否空目录
 * @param string $dirName 目录
 * @return bool
 */
function is_empty_dir($dirName)
{
	if(is_dir($dirName)){
		if(count(scandir($dirName))==2){
			return true;
		} 
	}
	return false;
}

/**
 * 递归创建目录
 * @param string $dirName 目录
 * @param int $chmod 权限
 * @return bool
 */
function dir_create($dirName, $chmod = 0755)
{
    $dirName = str_replace("\\", "/", $dirName);
    $dirPath = rtrim($dirName, '/');
    if (is_dir($dirPath))
        return true;
    $dirs = explode('/', $dirPath);
    $dir = '';
    foreach ($dirs as $v) {
        $dir .= $v . '/';
        is_dir($dir) or @mkdir($dir, $chmod, true);
    }
    return is_dir($dirPath);
}

/**
 * 区分大小写的判断文件判断
 * @param string $file 需要判断的文件
 * @return boolean
 */
function file_exists_case($file)
{
    if (is_file($file)) {
        //windows环境下检测文件大小写
        if (IS_WIN && C("CHECK_FILE_CASE")) {
            if (basename(realpath($file)) != basename($file)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

/**
 * 移除URL中的指定GET变量
 * @param string $var 要移除的GET变量名称
 * @param string $url 操作的url
 * @return string 移除GET变量后的URL地址
 */
function remove_url_param($var, $url = null)
{
    return Took\Route::removeUrlParam($var, $url);
}

/**
 * 根据大小返回标准单位 KB  MB GB等
 */
function get_size($size, $decimals = 2)
{
    switch (true) {
        case $size >= pow(1024, 3):
            return round($size / pow(1024, 3), $decimals) . " GB";
        case $size >= pow(1024, 2):
            return round($size / pow(1024, 2), $decimals) . " MB";
        case $size >= pow(1024, 1):
            return round($size / pow(1024, 1), $decimals) . " KB";
        default:
            return $size . 'B';
    }
}

/**
 * 数组转为常量
 * @param array $arr 数据
 * @return bool
 */
function array_define($arr)
{
    foreach ($arr as $k => $v) {
        $k = strtoupper($k);
        if (is_string($v)) {
            define($k, $v);
        } elseif (is_numeric($v)) {
            define($k, $v);
        } elseif (is_bool($v)) {
            $v = $v ? 'true' : 'false';
            define($k, $v);
        }
    }
    return true;
}

/**
 * 将数组键名变成大写或小写
 * @param array $arr 数组
 * @param int $type 转换方式 1大写   0小写
 * @return array
 */
function array_change_key_case_d($arr, $type = 0)
{
    $function = $type ? 'strtoupper' : 'strtolower';
    $newArr = array(); //格式化后的数组
    if (!is_array($arr) || empty($arr))
        return $newArr;
    foreach ($arr as $k => $v) {
        $k = $function($k);
        if (is_array($v)) {
            $newArr[$k] = array_change_key_case_d($v, $type);
        } else {
            $newArr[$k] = $v;
        }
    }
    return $newArr;
}

/**
 * 不区分大小写检测数据键名是否存在
 */
function array_key_exists_d($key, $arr)
{
    return array_key_exists(strtolower($key), array_change_key_case_d($arr));
}

/**
 * 数组转对象
 */
function array_to_object($arr)
{
    if (!is_array($arr) || empty($arr)) {
        return null;
    }
    return (object)$arr;
}

/**
 * 将数组中的值全部转为大写或小写
 * @param array $arr
 * @param int $type 类型 1值大写 0值小写
 * @return array
 */
function array_change_value_case($arr, $type = 0)
{
    $function = $type ? 'strtoupper' : 'strtolower';
    $newArr = array(); //格式化后的数组
    foreach ($arr as $k => $v) {
        if (is_array($v)) {
            $newArr[$k] = array_change_value_case($v, $type);
        } else {
            $newArr[$k] = $function($v);
        }
    }

    return $newArr;
}

/**
 * 多个PHP文件合并
 * @param array $files 文件列表
 * @param bool $space 是否去除空白
 * @param bool $tag 是否加<?php标签头尾
 * @return string 合并后的字符串
 */
function file_merge($files, $space = false, $tag = false)
{
    $str = ''; //格式化后的内容
    foreach ($files as $file) {
        $con = trim(file_get_contents($file));
        if ($space)
            $con = compress($con);
        $str .= substr($con, -2) == '?>' ? trim(substr($con, 5, -2)) : trim($con, 5);
    }
    return $tag ? '<?php if(!defined("TOOK_PATH")){exit("No direct script access allowed");}' . $str . "\t?>" : $str;
}

/**
 * 去空格，去除注释包括单行及多行注释
 * @param string $content 数据
 * @return string
 */
function compress($content)
{
    $str = ""; //合并后的字符串
    $data = token_get_all($content);
    $end = false; //没结束如$v = "tookphp"中的等号;
    for ($i = 0, $count = count($data); $i < $count; $i++) {
        if (is_string($data[$i])) {
            $end = false;
            $str .= $data[$i];
        } else {
            switch ($data[$i][0]) { //检测类型
                //忽略单行多行注释
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //去除格
                case T_WHITESPACE:
                    if (!$end) {
                        $end = true;
                        $str .= " ";
                    }
                    break;
                //定界符开始
                case T_START_HEREDOC:
                    $str .= "<<<TookPHP\n";
                    break;
                //定界符结束
                case T_END_HEREDOC:
                    $str .= "TookPHP;\n";
                    //类似str;分号前换行情况
                    for ($m = $i + 1; $m < $count; $m++) {
                        if (is_string($data[$m]) && $data[$m] == ';') {
                            $i = $m;
                            break;
                        }
                        if ($data[$m] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                    break;

                default:
                    $end = false;
                    $str .= $data[$i][1];
            }
        }
    }
    return $str;
}

/**
 * 获得常量
 * @param   string $name 常量名称，默认为获得所有常量
 * @param   void $value 常量不存在时的返回值
 * @param   string $type 常量类型，默认为用户自定义常量,参数为true获得所有常量
 * @return  array   常量数组
 */
function get_defines($name = "", $value = null, $type = 'user')
{
    if ($name) {
        $const = get_defined_constants();
        return defined($name) ? $const[$name] : $value;
    }
    $const = get_defined_constants(true);
    return $type === true ? $const : $const[$type];
}

/**
 * 抛出异常
 * @param string $msg 错误信息
 * @param string $type 异常类
 * @param int $code 编码
 * @throws
 */
function throw_exception($msg, $type = "Took\\Exception", $code = 0)
{
    if (class_exists($type, false)) {
        throw new $type($msg, $code, true);
    } else {
        halt($msg);
    }
}

/**
 * 错误中断
 * @param string | array $error 错误内容
 */
function halt($error)
{
    $e = array();
    if (DEBUG) {
        if (!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['line'] = $trace[0]['line'];
            $e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : "";
            $e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : "";
            ob_start();
            debug_print_backtrace();
            $e['trace'] = htmlspecialchars(ob_get_clean());
        } else {
            $e = $error;
        }
    } else {
        //错误显示url
        if ($_url = C('ERROR_URL')) {
            go($_url);
        } else {
            $e['message'] = C('ERROR_MESSAGE');
        }
    }
    //显示DEBUG模板，开启DEBUG显示trace
    require TOOK_TPL_PATH . 'halt.html';
    exit;
}

/**
 * 错误中断
 * @param $error 错误内容
 */
function error($error)
{
    halt($error);
}

/**
 * trace记录
 * @param string $value 错误信息
 * @param string $level
 * @param bool $record
 * @return mixed
 */
function trace($value = '[TOOKPHP]', $level = 'DEBUG', $record = false)
{
    static $_trace = array();
    $level = $level ? $level : 'DEBUG';
    if ('[TOOKPHP]' === strtoupper($value)) { // 获取trace信息
        return $_trace;
    } else {
        $info = '' . print_r($value, true);
        //调试模式时处理ERROR类型
        if (DEBUG && 'ERROR' == $level) {
            throw_exception($info);
        }
        if (!isset($_trace[$level])) {
            $_trace[$level] = array();
        }
        $_trace[$level][] = $info;
        if ((defined('IS_AJAX') && IS_AJAX) || $record) {
            Took\Log::record($info, $level, $record);
        }
    }
}

/**
 * 将错误记录到日志
 * @param string $error 错误信息
 */
function log_write($error)
{
    $trace = debug_backtrace();
    $e['message'] = $error;
    $e['file'] = $trace[0]['file'];
    $e['line'] = $trace[0]['line'];
    $e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : "";
    $e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : "";
    $msg = ("[Error]" . $e['message'] . " [Time]" . date("y-m-d h:i") . " [File]" . $e['file'] . " [Line]" . $e['line']);
    //写入日志
    Took\Log::write($msg);
}

/**
 * 404错误
 * @param string $msg 提示信息
 * @param string $url 跳转url
 */
function _404($msg = "", $url = "", $wait = 3, $tpl= "")
{
    //写入日志
    Took\Log::write($msg);
    if (empty($url) or C("404_URL")) {
        $url = C("404_URL");
    }
    _error(404, $msg, $url, $wait, $tpl);
}

/**
 * 显示错误页面
 * @param integer $code 页面代码
 * @param string $msg 提示信息
 * @param string $url 跳转URL
 * @param integer $wait 跳转等待时间
 * @param string $tpl 指定模板
 */
function _error($code = 404, $msg = "", $url = "", $wait = 3, $tpl= "") {
    $code = !empty($code) ? $code : 404;
    $msg = !empty($msg) ? $msg : '对不起，您所访问的页面出现错误！';
    $wait = (int) $wait;
    if(empty($tpl)) {
        $tpl = '_'.$code.'.html';
    }
    set_http_state($code);
    $bases = array(
        get_view_file( MODULE_VIEW_PUBLIC_PATH . $tpl ),
        get_view_file( TOOK_TPL_PATH . $tpl ),
        get_view_file( TOOK_TPL_PATH . '_error.html' ),
    );
    $tpl = get_exists_file($bases);
    require $tpl;
    exit;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 跳转网址
 * @param string $url 跳转urlg
 * @param int $time 跳转时间
 * @param string $msg
 */
function go($url, $time = 0, $msg = '')
{
    $url = Took\Route::getUrl($url);
    if (!headers_sent()) {
        $time == 0 ? header("Location:" . $url) : header("refresh:{$time};url={$url}");
        exit($msg);
    } else {
        echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time)
            exit($msg);
    }
}

/**
 * 打印输出数据
 * @param void $var
 */
function show($var)
{
    if (is_bool($var)) {
        var_dump($var);
    } else if (is_null($var)) {
        var_dump(NULL);
    } else {
        $style = 'position:relative;z-index:1000; padding:10px;border-radius:5px; background:'.chr(35).'f5f5f5;border:1px solid '.chr(35).'aaaaaa;font-size:14px;line-height:18px;opacity:0.9;';
        echo "<pre style='".$style."'>".print_r($var, true)."</pre>";
    }
}

/**
 * firebug调试模式
 * 需要firefox下安装firebug和firephp插件
 * @param $data 打印的数据
 */
function firephp($data)
{
    vendor('FirePHPCore.fb');
    ob_start();
    $firephp = \FirePHP::getInstance(true);
    $firephp->log($data, 'Iterators');
    ob_flush();
    ob_clean();
}

/**
 * 返回错误类型
 * @param int $type
 * @return string
 */
function FriendlyErrorType($type)
{
    switch ($type) {
        case E_ERROR: // 1 //
            return 'E_ERROR';
        case E_WARNING: // 2 //
            return 'E_WARNING';
        case E_PARSE: // 4 //
            return 'E_PARSE';
        case E_NOTICE: // 8 //
            return 'E_NOTICE';
        case E_CORE_ERROR: // 16 //
            return 'E_CORE_ERROR';
        case E_CORE_WARNING: // 32 //
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR: // 64 //
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING: // 128 //
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR: // 256 //
            return 'E_USER_ERROR';
        case E_USER_WARNING: // 512 //
            return 'E_USER_WARNING';
        case E_USER_NOTICE: // 1024 //
            return 'E_USER_NOTICE';
        case E_STRICT: // 2048 //
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR: // 4096 //
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED: // 8192 //
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED: // 16384 //
            return 'E_USER_DEPRECATED';
    }
    return $type;
}

/**
 * 验证扩展是否加载
 * @param string $ext
 * @return bool
 */
function extension_exists($ext)
{
    $ext = strtolower($ext);
    $loaded_extensions = get_loaded_extensions();
    return in_array($ext, array_change_value_case($loaded_extensions, 0));
}

// 不区分大小写的in_array实现
function in_array_case($value,$array){
    return in_array(strtolower($value),array_map('strtolower',$array));
}

/**
 * 字符串转数组
 * @param string|array $string
 * @param string $delimiter
 * @return array
 */
function string_to_array($string, $delimiter = ',')
{
    if(!is_array($string)) {
        $delimiter = empty($delimiter) ?  ',' : $delimiter;
        $string = explode($delimiter, $string);
        foreach($string as $k=>$d) {
            $string[$k] = trim($d);
        }
    }
    return array_filter((array)$string);
}

/**
 * 数组进行整数映射转换
 * @param array $data
 * @param array $map
 */
function int_to_string(&$data, array $map = array('status' => array('0' => '禁止', '1' => '启用')))
{
    $map = (array)$map;
    foreach ($data as $n => $d) {
        foreach ($map as $name => $m) {
            if (isset($d[$name]) && isset($m[$d[$name]])) {
                $data[$n][$name . '_text'] = $m[$d[$name]];
            }
        }
    }
}

/**
 * 取得模板文件完整路径
 * @param string $file
 * @return string|null
 */
function get_view_file($file)
{
    $file = str_replace('\\', '/', trim($file));
    $ext = C('TPL_EXT');
    if (empty($file)) {
        //没有传参时使用 动作为为文件名
        return MODULE_VIEW_CONTROLLER_PATH . ACTION . $ext;
    }
    if(false !== strpos($file, ':'.'/')){
        if (!preg_match('/\.[a-z]+$/i', $file)) {
            $file .= $ext;
        }
        return $file;
    }
    if (!is_file($file)) {
        $files = array();
        $temp = explode(',', $file);
        foreach($temp as $row) {
            $find = preg_match('/\#(.+?)\#/',trim($row),$match);
            if($find) {
                $array = explode(':',$match[1],2);
                if(count($array)>1) {
                    $base = APP_PATH. trim($array[0]) .'/View/';
                    $file = $base. trim($array[1]);
                }
                else {
                    $base = MODULE_VIEW_PATH;
                    $file = $base. trim($array[0]);
                }
            }
            else {
                $row = trim($row);
                if (!preg_match('/\.[a-z]+$/i', $row)) {
                    $row .= $ext;
                }
                if(realpath($row)) {
                    $file = str_replace('\\', '/', realpath($row));
                }
                else {
                    $array = explode(':',trim($row),2);
                    if(count($array)>1) {
                        $base = APP_PATH. trim($array[0]) .'/View/';
                        $file = $base. trim($array[1]);
                    } else {
                        if(0 === strpos($file,'/')) {
                            $file = ROOT_PATH.ltrim($file, __ROOT__.'/');
                        }
                        else {
                            $base = MODULE_VIEW_CONTROLLER_PATH;
                            $file = $base. trim($array[0]);
                            if(false !== stripos($file, MODULE_VIEW_CONTROLLER_PATH)) {
                                $file = MODULE_VIEW_CONTROLLER_PATH . substr($file, stripos($file, MODULE_VIEW_CONTROLLER_PATH)+strlen(MODULE_VIEW_CONTROLLER_PATH));
                            }
                            else if(false === stripos($file,dirname(APP_MODULE_PATH))) {
                                $file = MODULE_VIEW_CONTROLLER_PATH . $file;
                            }
                        }
                    }
                }
            }
            //添加后缀
            if (!preg_match('/\.[a-z]+$/i', $file)) {
                $file .= $ext;
            }
            $files[] = $file;
        }
        $file = implode(',',$files);
        if(realpath($file)) {
            return str_replace('\\', '/', realpath($file));
        }
    }
    return $file;
}

/**
 * 获取存在的文件
 * @param array $files
 * @return string|null
 */
function get_exists_file($files) {
    foreach($files as $file) {
        if(file_exists($file)) {
            return $file;
        }
    }
    return null;
}

/**
 * 批量复制文件
 * @param array $data
 * @return bool
 */
function copy_files($data) {
    foreach($data as $source=>$dest) {
        if(is_array($dest)) {
            foreach($dest as $f) {
                is_file($source) AND @copy($source, $f);
            }
        }
        else {
            is_file($source) AND @copy($source, $dest);
        }
    }
    return true;
}

/**
 * 上传文件至文件服务器中
 * @param array $config 上传文件配置
 * @param string $driver 上传驱动
 * @return mixed 数据库保存的文件名
 */
function store_upload_file($config = array(), $driver = '')
{
    $config = array_merge(C('UPLOAD_TYPE_CONFIG'), ['allowEmptyFile'=>true], (array)$config);
    $driver = !empty($driver) ? $driver : C('FILE_UPLOAD_TYPE');
    $upload = new \Took\Upload($config, $driver);
    $fileInfo = $upload->upload();
    $err = $upload->getError();
    if(!empty($err)){
        halt($err);
    }
    return $fileInfo;
}
