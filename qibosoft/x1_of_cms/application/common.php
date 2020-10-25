<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use app\common\model\Plugin;
use app\common\model\Module;
use app\common\model\User AS UserModel;
use app\common\model\Msg AS MsgModel;
use think\Db;
use think\Request;
use app\common\controller\Base;
use app\common\util\Cache2;

error_reporting(E_ERROR | E_PARSE );

if (is_file(APP_PATH.'function.php')) {      //用户二开的方法优先级更高,可以把下面默认的替换掉
    include_once APP_PATH.'function.php';
}

if(!function_exists('fun')){
    /**
     * 扩展函数,第一项是函数文件名@方法名,之后可以设置任意多项参数,它会对应到你自己定义的函数,比如这里第二项,会对应到你的函数第一项
     * 注意,唯一不足的是:用不了引用参数
     * @param string $fun 文件名@方法名
     * @return void|mixed
     */
    function fun($fun='sort@get'){
        static $fun_array = [];
        list($class_name,$action) = explode('@',$fun);
        $class = "app\\common\\fun\\".ucfirst($class_name);
        $obj = $fun_array[$class_name];
        if(empty($obj)){
            if(!class_exists($class)){
                return ;
            }
            $obj = $fun_array[$class_name] = new $class;
        }
        if(!method_exists($obj, $action)){
            return ;
        }
        
        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);
        
        static $default_params_array = [];
        $_params = $default_params_array[$fun];
        if (!isset($_params)) {
            $_params = [];
            $_obj = new \ReflectionMethod($obj, $action);
            $_array = $_obj->getParameters();
            foreach($_array AS $key=>$value){
                if($value->isOptional()){
                    $_params[$key] = $value->getDefaultValue();
                }else{
                    $_params[$key] = null;
                }
            }
            $default_params_array[$fun] = $_params;
        }
        
        foreach($_params AS $key=>$value){
            if(isset($params[$key])){
                $_params[$key] = $params[$key];
            }
        }
        
        return call_user_func_array([$obj, $action], $_params);     //这个函数没办法处理传递引用参数 func_get_args 也是变量的复制,没办法传递
        //         try {
        //             $reuslt = $_obj->invokeArgs($obj, $_params);
        //         } catch(\Exception $e) {
        //             $_params[0] = &$quote;
        //             arsort($_params);
        //             $reuslt = $_obj->invokeArgs($obj, $_params);
        //         }
        
        //         return $reuslt;
        //return call_user_func_array([$obj, $action], $_params);     //这个函数没办法处理传递引用参数
    }
}


if (!function_exists('get_real_path')) {
    /**
     * 解决有的虚拟服务器不支持../这样的相对路径的问题
     * @param unknown $path
     * @return string
     */
    function get_real_path($path) {
        if(!strstr($path,'..')){
            return $path;
        }
        $path = str_replace('\\','/',$path);
        $path = str_replace('//','/',$path);
        $detail = explode('/',$path);
        foreach($detail AS $key=>$value){
            if($value=='.'||$value===''){
                unset($detail[$key]);
            }
        }
        $detail = array_values($detail);
        $max = count($detail)-1;
        for($i=$max;$i>=0;$i--){
            if (!isset($detail[$i])) {
                continue;
            }
            if($detail[$i]=='..'){
                unset($detail[$i]);
                if($detail[$i-1]=='..'){
                    if($detail[$i-2]=='..'){
                        if($detail[$i-3]=='..'){
                            unset($detail[$i-6]);
                            unset($detail[$i-7]);
                        }
                        unset($detail[$i-4]);
                        unset($detail[$i-5]);
                    }
                    unset($detail[$i-2]);
                    unset($detail[$i-3]);
                }
                unset($detail[$i-1]);
            }
        }
        return (substr($path,0,1)=='/'?'/':'').implode('/',$detail);
    }
}

if (!function_exists('clear_js')) {
    /**
     * 过滤js内容
     * @param string $str 要过滤的字符串
     * @return mixed|string
     */
    function clear_js($str = '')
    {
        $search ="/<script[^>]*?>.*?<\/script>/si";
        $str = preg_replace($search, '', $str);
        return $str;
    }
}


if (!function_exists('hook_listen')) {
    /**
     * 监听标签的行为
     * 钩子若执行错误，错误日志会写在 runtime\hook_run_error.php 这个文件里边
     * @param  string $tag    标签名称
     * @param  mixed  $params 传入参数
     * @param  mixed  $extra  额外参数
     * @param  bool   $once   只获取一个有效返回值
     * @return string|mixed|mixed[]
     */
    function hook_listen($tag = '', &$params = null, $extra = null, $once = false) {        
        if ($once===true && hook_if_load($tag)===false) {   //这个纯属是为了兼容以前的模板中放的钩子            
            get_hook($tag,$params,$extra,['from'=>'hook'],$once);
        }
        try {
            $result = \think\Hook::listen($tag, $params, $extra, $once);
        } catch(\Exception $e) {
            if($e->getCode()===0||$e->getCode()===1){   //成功或报错页，即$this->error('');与$this->success('');
                throw $e;
            }else{
                //钩子若执行错误，错误日志会写在 runtime\hook_run_error.php 这个文件里边
                file_put_contents(RUNTIME_PATH.'hook_run_error.php', '<?php die();'.var_export($e,true)."\r\n$tag\r\n" );
            }            
        }
        return $result;
    }
}

if (!function_exists('hook_if_load')) {
    /**
     * 检查是否重复加载钩子
     * @param string $tag
     * @return boolean
     */
    function hook_if_load($tag=''){
        static $array = [];
        if ($array[$tag]) {
            return true;
        }else{
            $array[$tag] = true;
            return false;
        }
    }
}

if (!function_exists('get_hook')) {
    /**
     * 齐博首创 钩子文件扩展接口
     * 详细使用教程 https://www.kancloud.cn/php168/x1_of_qibo/1010065
     * @param string $type 钩子标志,不能重复
     * @param array $data POST表单数据
     * @param array $info 数据库资料
     * @param array $array 其它参数
     * @param string $use_common 默认同时调用全站通用的
     * @param string $dirname 可以指定模块目录，比如在模型里边被其它调用的话，就需要预先指定目录，避免获取不到真实目录
     * @return unknown|NULL
     */
    function get_hook($type='',&$data=[],$info=[],$array=[],$use_common=true,$dirname=''){
        if (hook_if_load($type)===true && $array['from']!='hook') {
            return NULL;
        }
        $path_array = [];
        $dirname = $dirname?:config('system_dirname');
        if ( empty($dirname) ) {
            if (defined('IN_PLUGIN')) {
                $dirname = input('plugin_controller');
            }else{
                $dispatch=request()->dispatch();
                if ($dispatch['module'][0]) {
                    $dirname = $dispatch['module'][0];
                }else{
                    $dirname = 'index';
                }
            }
        }
        $path_array[] = (defined('IN_PLUGIN')?PLUGINS_PATH:APP_PATH).($dirname?$dirname.DS:'').'ext'.DS.$type.DS;
        if ($use_common===true) {
            $path_array[] = APP_PATH.'common'.DS.'ext'.DS.$type.DS;
        }
        
        $file_array = [];
        foreach ($path_array AS $path){
            if (is_dir($path)) {
                $sarray = [];
                $dir = opendir($path);
                while($file = readdir($dir)){
                    if(preg_match("/^([\w\.-]*)\.php$/i", $file,$sar)){
                        if (in_array($sar[1], $file_array)) {
                            continue ; //出现同名,就跳过
                        }
                        $sarray[$path.DS.$file] = $sar[1];
                    }
                }
                asort($sarray);
                $file_array = array_merge($file_array,$sarray);
            }
        }
        
        if ($file_array) {
            foreach($file_array AS $file=>$v){
                $result = include($file);
                if ($result===true||$result===false) {
                    return $result;
                }elseif(is_string($result) || is_array($result)){
                    return $result;
                }
            }
        }
        
        return NULL;
        
    }
}

if (!function_exists('format_time')) {
    /**
     * 时间戳格式化
     * @param string $time 时间戳
     * @param string $format 输出格式 设置为true的话,就按 刚刚 几分钟前的格式显示
     * @param string $type 当$format设置为true的时候,设置超过一个月后的时间格式
     * @return false|string
     */
    function format_time($time = '', $format='Y-m-d H:i',$type='Y-m-d H:i') {
        if(!preg_match('/^([\d]+)$/', $time)){
            $time = strtotime($time);
        }
        if($format===true){
            $_time = time() - $time;
            if($_time<60){
                $msg = '刚刚';
            }elseif($_time<1800){
                $msg = intval($_time/60).'分钟前';
            }elseif($_time<3600){
                $msg = '半小时前';
            }elseif($_time<3600*24){
                $msg = intval($_time/3600).'小时前';
            }elseif($_time<3600*24*30){
                $msg = intval($_time/(3600*24)).'天前';
            }elseif($_time<3600*24*30*12){
                $msg = intval($_time/(3600*24*30)).'个月前';
            }else{
                $msg =  empty($time) ? '' : date($type,$time);
            }
        }else{
            $msg = !$time ? '' : date($format, intval($time));
        }
        return $msg;
    }
}


if (!function_exists('plugin_action_exists')) {
    /**
     * 检查插件控制器是否存在某操作
     * @param string $name 插件名
     * @param string $controller 控制器
     * @param string $action 动作
     * @return bool
     */
    function plugin_action_exists($name = '', $controller = '', $action = '')
    {
        $dir = '';
        if (strpos($name, '/')) {
            list($name, $controller, $action) = explode('/', $name);
        }
        if(strpos($controller,'.')){
            list($dir,$controller) = explode('.', $controller);
        }
        $class = "plugins\\{$name}\\".ENTRANCE."\\". ($dir?"$dir\\":'') . format_class_name($controller);
        return (method_exists($class, $action) ||method_exists($class, '_initialize'));
    }
}

if (!function_exists('plugin_model_exists')) {
    /**
     * 检查插件模型是否存在
     * @param string $name 插件名
     * @return bool
     */
    function plugin_model_exists($name = '')
    {
        $class = "plugins\\{$name}\\model\\".format_class_name($name);
        return class_exists($class);
    }
}



if (!function_exists('downFile')) {
    /**
     * 下载远程文件
     * @param unknown $url 远程文件网址
     * @param string $filename 保存在空间上哪个目录
     * @param number $type 下载方式
     * @throws \Exception
     * @return void|boolean
     */
    function downFile($url,$filename='',$type=0){
        if($url==''){
            return false;
        }
        if($type===1){
            $fp_output = fopen($filename, 'w');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp_output);
            curl_exec($ch);
            curl_close($ch);
            if(filesize($filename)>2){
                return ;
            }
        }elseif($type===2){
            ob_end_clean();
            ob_start();
            readfile($url);
            $data=ob_get_contents();
            ob_end_clean();
            if($data!=''){
                file_put_contents($filename,$data);
                return ;
            }
        }
        
        if( copy($url,$filename) ){
            return ;
        }
        
        if(($data=file_get_contents($url))==false){
//             $ch=curl_init();
//             $timeout = 600;
//             curl_setopt($ch,CURLOPT_URL,$url);
//             curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//             curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);//最长执行时间
//             curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);//最长等待时间
//             $data=curl_exec($ch);
//             curl_close($ch);
            $data = http_curl($url);
        }
        if($data!=''){
            write_file($filename,$data);
        }
    }
}

if (!function_exists('get_plugin_model')) {
    /**
     * 获取插件模型实例
     * @param  string $name 插件名
     * @return object
     */
    function get_plugin_model($name)
    {
        $class = "plugins\\{$name}\\model\\{$name}";
        return new $class;
    }
}


if (!function_exists('plugin_action')) {
    /**
     * 执行插件动作
     * 也可以用这种方式调用：plugin_action('插件名/控制器/动作', [参数1,参数2...])
     * @param string $name 插件名
     * @param string $controller 控制器
     * @param string $action 动作
     * @param mixed $params 参数
     * @return mixed
     */
    function plugin_action($name = '', $controller = '', $action = '', $params = [])
    {
        $dir = '';
        if (strpos($name, '/')) {
            $params = is_array($controller) ? $controller : (array)$controller;
            list($name, $controller, $action) = explode('/', $name);
        }
        if (!is_array($params)) {
            $params = (array)$params;
        }
        if(strpos($controller,'.')){
            list($dir,$controller) = explode('.', $controller);
        }
        $class = "plugins\\{$name}\\".ENTRANCE."\\". ($dir?"$dir\\":'') . format_class_name($controller);
        $obj = new $class;
        
        //反射，获取方法里边的参数
        $_params = [];
        if (!empty($params)) {
            $_obj = new \ReflectionMethod($obj, $action);
            $_array = $_obj->getParameters();
            foreach($_array AS $value){
                $_params[$value->name] = $params[$value->name];
            }
            
            //类似给函数赋值一样，可以只给前面的赋值，后面的可以保留原值
            if( end($_params)==''  ){
                $ar = array_reverse($_params);
                foreach($ar AS $key=>$value){
                    if($value==''){
                        unset($ar[$key]);
                        break;
                    }
                }
                $_params = array_reverse($ar);
            }
        }

        //把插件的配置文件也像模块那样引入进去
        if(is_file(ROOT_PATH."plugins/{$name}/config.php")){
            $array = include(ROOT_PATH."plugins/{$name}/config.php");
            config($array) ;
        }
        return call_user_func_array([$obj, $action], $_params);
    }
}



if (!function_exists('urls')) {
    /**
     * 重写 url 对于缺少模块与控制器的URL地址,自动补上
     * @param string $url
     * @param string $vars
     * @param string $suffix
     * @param string $domain
     * @return unknown
     */
    function urls($url = '', $vars = '', $suffix = true, $domain = false)
    {
        $url  = full_url($url);
        list($module) = explode('/',$url);
        $_url = url($url, $vars, $suffix, $domain);
        if($module=='index' && ENTRANCE!='index'){
            $_url = str_replace(array(ADMIN_FILENAME,'member.php'), 'index.php', $_url);
        }elseif($module=='member' && ENTRANCE!='member'){
            if(preg_match('/^\/member\//i', $_url)){
                $_url = '/member.php'.$_url;
            }else{
                $_url = str_replace(array(ADMIN_FILENAME,'index.php'), 'member.php', $_url);
                $_url = preg_replace("/^(http|https):\/\/([^\/]+)\/([\w-]+)\//i", "\\1://\\2/member.php/\\3/", $_url);
            }            
        }
        return $_url ;
    }
}

if (!function_exists('auto_url')) {
    /**
     * 自适应插件或模块链接
     * @param string $url
     * @param string $vars
     * @param string $suffix
     * @param string $domain
     * @return unknown
     */
    function auto_url($url = '', $vars = '', $suffix = true, $domain = false)
    {
        $detail = Request::instance()->dispatch();
        if ($detail['module'][1]=='plugin' && $detail['module'][2]=='execute') {
            return purl($url, $vars);
        }else{
            return url(full_url($url), $vars, $suffix, $domain);
        }
    }
}

if (!function_exists('purl')) {
    /**
     * 生成插件操作链接
     * @param string $url 链接：插件名称/控制器/操作
     * @param array $param 参数
     * @param string $module 模块名，admin需要登录验证，index不需要登录验证
     */
    function purl($url = '', $param = [], $module = '')
    {
        $params = [];
        if (strstr($url,'/')) {
            $url = explode('/', $url);            
            if( count($url)==3 ){
                $params['plugin_name'] = $url[0];
                $params['plugin_controller'] = $url[1];
                $params['plugin_action'] = $url[2];                
            }elseif(count($url)==2){
                $params['plugin_name'] = input('route.plugin_name');
                $params['plugin_controller'] = $url[0];
                $params['plugin_action'] = $url[1];  
            }
        }else{
            $params['plugin_name']    = input('plugin_name');
            $params['plugin_controller'] = input('plugin_controller');            
            $params['plugin_action'] = $url=='' ? input('plugin_action') : $url;
        }
        empty($param) && $param=[];
        if (!is_array($param)) {
            parse_str($param, $param);
        }
        // 合并参数
        $params = array_merge($params, $param);
        //没有特别指定的话，就进入相应的前台或后台
        $module || $module = ENTRANCE;
        $url = url($module .'/plugin/execute', $params);
        if($module=='index' && ENTRANCE!='index'){
            $url = str_replace(array(ADMIN_FILENAME,'member.php'), 'index.php', $url);
        }elseif($module=='member' && ENTRANCE!='member'){
            if(preg_match('/^\/member\//i', $url)){
                $url = '/member.php'.$url;
            }else{
                $url = str_replace(array(ADMIN_FILENAME,'index.php'), 'member.php', $url);
                $url = preg_replace("/^(http|https):\/\/([^\/]+)\/([\w-]+)\//i", "\\1://\\2/member.php/\\3/", $url);
            }            
        }
        return $url;
    }
}


if (!function_exists('iurl')) {
    /**
     * 强制使用前台URL地址,比如会员中心与后台是最常使用的.他们要访问前台内容
     * @param string $url
     * @param string $vars
     * @param string $suffix
     * @param string $domain
     * @param string $type 频道或者是插件
     * @return mixed
     */
    function iurl($url = '', $vars = '', $suffix = true, $domain = false , $type = '')
    {
        $detail = Request::instance()->dispatch();
        
        if($type==''){  //主要是标签那里使用,判断是频道还是插件
            static $typedb = [];
            $_detail = explode('/',$url);
            if(count($_detail)==3){
                $module = $_detail[0];
                
                if(empty($typedb[$module])){
                    if(is_dir(APP_PATH.$module)){
                        $typedb[$module] = 'module';
                    }else{
                        $typedb[$module] = 'plugin';
                    }
                }
                
                $type = $typedb[$module];
            }
        }
        
        if($type=='m'||$type=='module'){
            $_url = url(full_url($url), $vars, $suffix, $domain);
        }elseif($type=='plugin'){
            $_url = purl($url, $vars, '');       
        //是否在插件里
        }elseif ($detail['module'][1]=='plugin' && $detail['module'][2]=='execute') {
            //如果是index/xxx/xxx模块要特殊处理
            $_url = preg_match('/^index\/([\w]+)\/([\w]+)/', $url) ? url($url, $vars, $suffix, $domain) : purl($url, $vars, 'index');
        }else{
            $_url = url(full_url($url), $vars, $suffix, $domain);
        }
        $url = str_replace(array(ADMIN_FILENAME,'member.php'), 'index.php', $_url);
        if(config('webdb.hiden_index_php') && preg_match('/\/index\.php\//', $url)){
            $url = str_replace('/index.php/', '/', $url);
        }
        return $url;
    }
}

if (!function_exists('murl')) {
    /**
     * 强制使用会员中心URL地址
     * @param string $url
     * @param string $vars
     * @param string $suffix
     * @param string $domain
     * @param string $type 强制指定频道或插件
     * @return string|mixed
     */
    function murl($url = '', $vars = '', $suffix = true, $domain = false,$type='')
    {
        $detail = Request::instance()->dispatch();
        
        if($type==''){
            static $typedb = [];
            $_detail = explode('/',$url);
            if(count($_detail)==3){
                $module = $_detail[0];
                if(empty($typedb[$module])){
                    if(is_dir(APP_PATH.$module)){
                        $typedb[$module] = 'module';
                    }else{
                        $typedb[$module] = 'plugin';
                    }
                }
                $type = $typedb[$module];
            }
        }
        
        if($type=='m'||$type=='module'){
            $_url = url($url, $vars, $suffix, $domain);
        }elseif($type=='p'||$type=='plugin'){
            $_url = purl($url, $vars);
        }
        
//         $_detail = explode('/',$url);
//         if(count($_detail)==3){
//             if($_detail[0]=='member'){
//                 $_url = url($url, $vars, $suffix, $domain);
//             }elseif($type=='p'){
//                 $_url = purl($url, $vars);
//             }
//         }
        
        if(empty($_url)){
            //是否是插件
            if ($detail['module'][1]=='plugin' && $detail['module'][2]=='execute') {
                $_url = purl($url, $vars, 'index');
            }else{
                $_url = url($url, $vars, $suffix, $domain);
            }
        }
        if(preg_match('/^\/[\w]+\//', $_url)){
            $url = '/member.php'.$_url;
        }else{
            $url = str_replace([ADMIN_FILENAME.'/admin','index.php/index'],'member.php/member', $_url);
            $url = str_replace([ADMIN_FILENAME,'index.php'],'member.php', $url);
            $url = preg_replace("/^(http|https):\/\/([^\/]+)\/([\w-]+)\//i", "\\1://\\2/member.php/\\3/", $url);
        }        

        //if(!preg_match('/^member.php/', $url)){
            //$url = '/index.php'.$url;
        //}
        return $url;
    }
}

if (!function_exists('full_url')) {
    /**
     * 补全缺少模块与控制器的URL
     * @param string $url
     * @return string
     */
    function full_url($url=''){
        $detail = explode('/',$url);
        if (count($detail)==3) {
            return $url;
        }
        static $_m = null;
        $_m===null && $_m = Request::instance()->dispatch();
        $m = $_m['module'];
        
        if(count($detail)==1){
            $url = $m[0] . '/' . $m[1] . '/' . $url;
        }elseif(count($detail)==2){
            $url = $m[0] . '/' . $url;
        }
        return $url;
    }
}

if (!function_exists('format_class_name')) {
    /**
     * 主要是插件那里用到,统一TP那样的文件命名原理,文件名中的某个大写字母,在URL中要用_隔开
     * @param unknown $name
     * @return string
     */
    function format_class_name($name){
        $detail = explode('_',$name);
        $classname = '';
        foreach($detail AS $value){
            $value && $classname .= ucfirst($value);
        }
        return $classname;
    }
}

if (!function_exists('into_sql')) {
    /**
     * 批量导入SQL数据
     * @param unknown $sql SQL数据,可以是多条
     * @param string $replace_pre 默认true替换为当前数据表前缀
     * @param number $type 2是遇到错误直接终止,1是显示错误,但不终止程序,0是屏蔽错误
     */
    function into_sql($sql, $replace_pre=true,$type=2){
        if(preg_match('/\.sql$/', $sql)||is_file($sql)){
            $sql = check_bom(read_file($sql));
        }
        $prefix = $replace_pre===true ? ['qb_'=>config('database.prefix')] : [];
        $sql_list = parse_sql($sql,$prefix);
        $result = false;
        foreach ($sql_list as $v) {
            if($type==2){   //直接终止
                $result = Db::execute($v);
            }else{
                try {
                    $result = Db::execute($v);
                } catch(\Exception $e) {
                    if($type==1){   //显示错误,不终止后面的程序运行
                        echo '<br>导入SQL失败，请检查install.sql的语句是否正确<pre>'.$v."\n\n".$e.'</pre>';
                    }else{
                        //为0的时候,屏蔽错误
                    }
                }
            }
        }
        return $result;
    }
}

if (!function_exists('parse_sql')) {
    /**
     * 分割sql语句
     * @param  string $content sql内容
     * @param  array $prefix 替换前缀
     * @param  bool $limit  如果为1，则只返回一条sql语句，默认返回所有     * 
     * @return array|string 除去注释之后的sql语句数组或一条语句
     */
    function parse_sql($sql = '', $prefix = [], $limit = 0) {
        // 被替换的前缀
        $from = '';
        // 要替换的前缀
        $to = '';
        
        // 替换表前缀
        if (!empty($prefix)) {
            $to   = current($prefix);
            $from = current(array_flip($prefix));
        }
        
        if ($sql != '') {
            // 纯sql内容
            $pure_sql = [];
            
            // 多行注释标记
            $comment = false;
            
            // 按行分割，兼容多个平台
            $sql = str_replace(["\r\n", "\r"], "\n", $sql);
            $sql = explode("\n", trim($sql));
            
            // 循环处理每一行
            foreach ($sql as $key => $line) {
                // 跳过空行
                if ($line == '') {
                    continue;
                }
                
                // 跳过以#或者--开头的单行注释
                if (preg_match("/^(#|--)/", $line)) {
                    continue;
                }
                
                // 跳过以/**/包裹起来的单行注释
                if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                    continue;
                }
                
                // 多行注释开始
                if (substr($line, 0, 2) == '/*') {
                    $comment = true;
                    continue;
                }
                
                // 多行注释结束
                if (substr($line, -2) == '*/') {
                    $comment = false;
                    continue;
                }
                
                // 多行注释没有结束，继续跳过
                if ($comment) {
                    continue;
                }
                
                // 替换表前缀
                if ($from != '') {
                    $line = str_replace('`'.$from, '`'.$to, $line);
                }
                if ($line == 'BEGIN;' || $line =='COMMIT;') {
                    continue;
                }
                // sql语句
                array_push($pure_sql, $line);
            }
            
            // 只返回一条语句
            if ($limit == 1) {
                return implode($pure_sql, "");
            }
            
            // 以数组形式返回sql语句
            $pure_sql = implode($pure_sql, "\n");
            $pure_sql = explode(";\n", $pure_sql);
            return $pure_sql;
        } else {
            return $limit == 1 ? '' : [];
        }
    }
}


if (!function_exists('str_array')) {
    /**
     * 把字符串转为数组  换行符或者是, 隔开的字符串
     * 第二项,最好指定,不然的话,只有一项参数的话,容易判断失败
     * @param string $value 字符串
     * @param string $explode 指定用什么符号做切割分隔,留空则自动识别,如果只有一个参数的话,容易判断失败
     * @return string|array|unknown[]
     */
    function str_array($value = '',$explode='') {
        $value =  trim($value, " ,;\r\n|");
        if( strpos($value,"\n") || $explode=="\n" ){    //常用换行符做分割,比如后台参数
            $value = str_replace("\r","",$value);
            $exp = "\n";
        }elseif($explode!==''){
            $exp = $explode;
        }elseif( strpos($value,"|") ){
            $exp = "|";
        }elseif( strpos($value,",") ){
            $exp = ",";
        }elseif( strpos($value,";") ){
            $exp = ";";
        }elseif( strpos($value," ") ){
            $exp = " ";
        }else{
            return [$value];
        }
        $array = explode($exp,$value);
        if ( $exp == "\n" && strpos($value, '|') ) {
            $ar = [];
            foreach ($array as $val) {
                list($k, $v) = explode( '|' , $val);
                $ar[$k] = $v;
            }
        } else {
            $ar = $array;
        }
        return $ar;
    }
}




if(!function_exists('get_post')){
    /**
     * 获取数据,POST优化级最高 然后是GET 最后是路由
     * @param string $type 指定要什么数据
     * @return mixed
     */
	function get_post($type=''){
	    if($type=='post'){
	        $array = input('post.');
	    }elseif($type=='get'){
	        $array = input('get.');
	    }elseif($type=='route'){
	        $array = input('route.');
	    }else{
	        //优先级 post > get > route
	        $array_route = input('route.');
	        is_array($array_route) || $array_route=[];
	        $array_get = input('get.');
	        is_array($array_get) || $array_get=[];
	        $array_post = input('post.');
	        is_array($array_post) || $array_post=[];
	        $array = array_merge($array_route,$array_get,$array_post);
	    }
	    return $array;	    
	}
}

//根据用户的用户组ID得到用户组的名称
if (!function_exists('getGroupByid')) {
    /**
     * 取用户组的数据, 默认只取对应用户组ID的标题名称,也可以取用户组的所有数据
     * @param unknown $gid 用户组ID,如果为NULL的话,就取出所有
     * @param string $only_title 默认只取名称,设置为false的话,可以取所有数据
     * @return unknown
     */
    function getGroupByid($gid = null , $only_title = true)
    {
        $group = cache('group_title');
        if (empty($group)) {
            $group = model('common/group')->getList();
            cache('group_title',$group,3600);
        }
        foreach ($group AS $key=>$rs){
            if ($rs['type']==0) {
                $rs['_level'] = [];
                if (strstr($rs['level'],'=')) {
                    $detail = explode(',', $rs['level']);
                    foreach ($detail AS $v){
                        list($day,$money) = explode('=', $v);
                        if ($day>0 && $money>0) {
                            $rs['_level'][$day] = $money;
                        }
                    }
                }else{
                    $rs['_level'][$rs['daytime']] = $rs['level'];
                }
            }
            $group[$key] = $rs;
        }
        if($gid>0){
            return $only_title==true ? $group[$gid]['title'] : $group[$gid];            
        }else{
            if($only_title==true){
                $array = [];
                foreach($group AS $key=>$rs){
                    $array[$rs['id']] = $rs['title'];
                }
                return $array;
            }else{
                return $group;
            }
        }        
    }
}

if (!function_exists('get_user')) {
    /**
     * 一般是根据UID获取用户的信息.
     * @param string $value UID值
     * @param string $type 一般都是UID,默认也是
     * @return array
     */
    function get_user($value='',$type='uid'){
        static $mod = null;
        $rarray = [];
        if($value && $type=='uid' && is_numeric($value)){
            static $user_array = [];
            $rarray = $user_array[$value];
            if($rarray===null){
                if(!$rarray=cache('user_'.$value)){
                    $mod===null && $mod = model('common/user');
                    $rarray = $mod->getById($value) ?: [];
                    cache('user_'.$value,$rarray,3600*12);
                }
                $user_array[$value] = $rarray;
            }		    
        }elseif($value!==''){
            $mod===null && $mod = model('common/user');
		    $rarray = $mod->get_info($value,$type);
        }
		return $rarray;
    }
}

if (!function_exists('get_user_name')) {
    /**
     * 获取用户帐号或昵称
     * @param unknown $value
     * @return unknown|mixed
     */
    function get_user_name($value)
    {
        $info = get_user($value);
        if( !empty($info)  ){
            return config('webdb.show_nickname') ? $info['nickname'] : $info['username'];
        }
    }
}

if (!function_exists('get_user_icon')) {
    /**
     * 获取用户头像
     * @param unknown $value
     * @return string|void|string
     */
    function get_user_icon($value)
    {
        static $domain = null;
        if($domain === null){
            $domain = request()->domain() ;
        }
        $info = get_user($value);
        if(empty($info) || empty($info['icon'])){
            return $domain.'/public/static/images/nobody.gif';
        }
        return tempdir($info['icon']);
    }
}

if (!function_exists('get_user_money')) {
    /**
     * 获取用户虚拟币
     * @param number $type 虚拟币类型
     * @param number $uid 用户的UID
     * @return number
     */
    function get_user_money($type=0,$uid=0)
    {
        if ($type==0) {
            return get_user($uid)['money'];
        }elseif($type==-1){
            return get_user($uid)['rmb'];
        }
        static $array = null;
        if(empty($array[$uid])){
            $array[$uid] = \plugins\marketing\model\Money::where('uid',$uid)->column('type,money');
        }
        return intval($array[$uid][$type]);
    }
}

if(!function_exists('getArray')){
    /**
     * 把数据库取出的对象数据转成数组
     * @param unknown $row_list
     * @return array|NULL[]|unknown
     */
    function getArray($row_list)
    {
        if (is_array($row_list)) {
            if (empty($row_list)) return [];
            if (is_object(current($row_list))) {
                $items = [];
                foreach ($row_list as $key => $value) {
                    $items[$key] = $value->toArray();
                }
                return $items;
            }
            return $row_list;
        }
        //if ($row_list->isEmpty()) return [];
        if (is_object($row_list)) {
            return $row_list->toArray();
        }
        return $row_list;
    }
}


if(!function_exists('table_field')){
    /**
     * 数据表字段信息处理函数
     * @param unknown $table 表名
     * @param string $field 赋值的话,判断某个字段是否存在,留空的话,取所有字段
     * @param string $add_pre 是否需要补全数据表前缀,true 需要再补全,false 不需要再补.
     * @return boolean|unknown
     */
    function table_field($table,$field='',$add_pre=true){
        $add_pre == true && $table = config('database.prefix') .$table;
        $array = Db::getTableFields($table);
        if(!empty($field)){
            if(in_array($field,$array) ){
                return true;
            }else{
                return false;
            }
        }else{  //返回所有字段
            return $array;
        }
      //$result = Db::query("SHOW CREATE TABLE `{$table}`");
      //preg_match_all("/`([^`]+)`/is", $result[0]['Create Table'],$array);
    }
}

if(!function_exists('is_table')){

    /**
     * 判断数据表是否存在
     * @param unknown $table 数据表名,可以不加区分符前缀
     * @param string $add_pre 默认值true 自动补全前缀
     * @return boolean
     */
    function is_table($table,$add_pre=true){
        $add_pre == true && $table = config('database.prefix') .$table;
        $result = Db::query("SHOW TABLES LIKE '{$table}'");
        return empty($result) ? false : true;
    }
}



if(!function_exists('copy_dir')){    
    /**
     * 复制目录
     * @param unknown $path 原目录名
     * @param unknown $newp 新目录名
     * @param string $isover 默认值为true 强制替换原来的文件
     */
    function copy_dir($path,$newp,$isover=true){
        if(!is_dir($newp)){
            if(!mkdir($newp) && !makepath($newp) ){
                showerr($newp.'目录创建失败');
            }
        }
        if (file_exists($path)){
            if(is_file($path)){
                if($isover==true || !is_file($newp)){
                    copy($path,$newp);
                }
            } else{
                $handle = opendir($path);
                while (($file = readdir($handle))!=false) {
                    if ( ($file!=".") && ($file!="..") ){
                        if (is_dir("$path/$file")){
                            copy_dir("$path/$file","$newp/$file",$isover);
                        } else{
                            if($isover==true || !is_file("$newp/$file")){
                                copy("$path/$file","$newp/$file");
                            }
                        }
                    }
                }
                closedir($handle);
            }
        }
    }
}

if(!function_exists('sort_get_father')){
    /**
     * 模块中获取当前栏目的所有父ID
     * @param number $id
     * @param string $sys_type
     * @return void|number
     */
    function sort_get_father($id=0,$sys_type=''){
        if($id<1){
            return ;
        }
        $array = sort_config($sys_type);
        $pid = $array[$id]['pid'];
        if($pid>0){
            $farray[$pid] = $array[$pid]['name'];
            $ar = sort_get_father($pid,$sys_type);
            if(!empty($ar)){
                $farray = $ar+$farray;
            }
            return $farray;
        }
    }
}

if(!function_exists('category_get_father')){
    /**
     * 模块中获取当前辅栏目的所有父ID
     * @param number $id
     * @param string $sys_type
     * @return void|number
     */
    function category_get_father($id=0,$sys_type=''){
        if($id<1){
            return ;
        }
        $array = category_config($sys_type);
        $pid = $array[$id]['pid'];
        if($pid>0){
            $farray[$pid] = $array[$pid]['name'];
            $ar = category_get_father($pid,$sys_type);
            if(!empty($ar)){
                $farray = $ar+$farray;
            }
            return $farray;
        }
    }
}

if(!function_exists('get_sort')){
    
    /**
     * 获取具体某个频道下面的栏目相关信息
     * @param number $id 为0时，取出所有栏目，大于0时，根据$type参数取值
     * @param string $field 取某个字段对应的值,config或者是不存在的字段名,则取出所有配置参数
     * @param string $type father时取出所有父级栏目,sons时取出所有下一级栏目,other时优先取子栏目,若无再取同级,若无再取父级兄弟栏目
     * @param string $sys_type 指定频道模块
     * @return void|number|number[]|array[]|unknown[]|number[]|unknown[]|array|unknown
     */
    function get_sort($id=0,$field='name',$type='',$sys_type=''){
        $array = sort_config($sys_type);
        $_type = $type==='' ? $field : $type;   //兼容处理
        if($id>0){
            if($_type=='father'){    //所有父栏目，也包括自身,一般用在面包屑导航
                $farray = sort_get_father($id,$sys_type);
                $self_array = [$id=>$array[$id]['name']];
                return empty($farray) ? $self_array : $farray+$self_array;
            }elseif($_type=='sons'){  //所有下一级级栏目，也包括自身，一般用在查询数据库
                $s_array = [
                        $id => $field=='name' ? $array[$id]['name'] : $id,
                ];
                $_pid = 0;
                $fpid = [];     //把所有父分类都加入到容器
                foreach($array AS $key=>$rs){
                    if(!$rs['pid'])continue;
                    //$rs['pid']==$id 仅仅第一层直属下级, $rs['pid']==$_pid 下级的下级
                    if($rs['pid']==$id||$rs['pid']==$_pid||in_array($rs['pid'], $fpid)){
                        $s_array[$key] = $field=='name' ? $array[$key]['name'] : $key;
                        if(!in_array($rs['pid'], $fpid)){   //把所有父分类都加入容器
                            $fpid[] = $rs['pid'];
                        }
                        $_pid = $key;
                    }
                }
                return $s_array;
            }elseif($_type=='brother'){  //取同级栏目
                $s_array = [];
                $_pid = $array[$id]['pid'];
                foreach($array AS $key=>$rs){
                    if($rs['pid']==$_pid){
                        $s_array[$key]=$rs['name'];
                    }
                }
                return $s_array;
            }elseif($_type=='other'){    //取父级兄弟栏目及本级兄弟栏目及子栏目，一般用在栏目页面方便展示布局
                $m_array = [];
                $pid = $array[$id]['pid'];
                $fpid = $pid ? $array[$pid]['pid'] : null;
                $_pid = null;
                foreach($array AS $key=>$rs){
                    if($fpid!==null&&$rs['pid']==$fpid){  //父级栏目
                        $m_array[$key] = $rs['name'];
                    }elseif($rs['pid']==$pid){   //同级栏目
                        $m_array[$key] = $rs['name'];
                    }elseif ($rs['pid']==$_pid){    //子栏目
                        $m_array[$key] = $rs['name'];
                    }
                    if($key==$id){
                        $_pid = $id;
                    }
                }
                return $m_array;
            }elseif($_type=='config'){
                return $array[$id];
            }elseif(isset($array[$id][$_type])){
                return $array[$id][$_type];
            }else{
                return $array[$id];
            }
        }elseif($_type=='other' && $array){  //fid不存在的话,就只取一级栏目
            $farray = [];
            foreach($array AS $key=>$rs){
                if($rs['pid']==0){
                    $farray[$key]=$rs['name'];
                }
            }
            return $farray;
        }elseif ($_type=='all'){
            $farray = [];
            foreach($array AS $key=>$rs){
                $farray[$key]=$rs['name'];
            }
            return $farray;
        }
        return $array;
    }
}



//模块的栏目配置参数
if(!function_exists('sort_config')){
    /**
     * 获取模块里边的栏目配置参数
     * @param string $sys_type 可以指定其他频道的目录名
     * @param unknown $pid 可以指定只调取哪些父栏目的下的子栏目数据
     * @param string $field_name 默认指定取什么字段,设置true的话,就可以获取所有字段
     * @return array|unknown
     */
    function sort_config($sys_type='',$pid=null,$field_name='name'){
        if(empty($sys_type)){
            $sys_type=config('system_dirname');
        }
        if(empty($sys_type)){
            return [];
        }
        static $sort_array = [];
        $array = $sort_array[$sys_type];
        if(empty($array)){
            $array = cache('sort_config_'.$sys_type);
            if (empty($array)) {
                if (!modules_config($sys_type)&&!plugins_config($sys_type)) {
                    return [];
                }elseif (!is_file(APP_PATH.$sys_type.'/model/Sort.php')&&!is_file(PLUGINS_PATH.$sys_type.'/model/Sort.php')) {
                    return [];
                }
                //$array = model($sys_type.'/sort')->getTreeList();
                $array = get_model_class($sys_type,'sort')->getTreeList();
                cache('sort_config_'.$sys_type,$array);
            }
            $sort_array[$sys_type] = $array;
        }
        
        if($pid!==null){    //取子栏目
            $_array = [];
            foreach ($array AS $id=>$rs){
                if($rs['pid']==$pid){
                    $_array[$id] = (is_string($field_name)&&$field_name&&isset($rs[$field_name]))?$rs[$field_name]:$rs;
                }
            }
            return $_array;
        }else{
            return $array;
        }        
    }
}

if(!function_exists('get_category')){
    /**
     * 获取具体某个频道下面的辅栏目相关信息
     * @param number $id 为0时，取出所有栏目，大于0时，根据$type参数取值
     * @param string $field 取某个字段对应的值,config或者是不存在的字段名,则取出所有配置参数
     * @param string $type father时取出所有父级栏目,sons时取出所有下一级栏目,other时优先取子栏目,若无再取同级,若无再取父级兄弟栏目
     * @param string $sys_type 指定频道模块
     * @return void|number|number[]|array[]|unknown[]|number[]|unknown[]|array|unknown
     */
    function get_category($id=0,$field='name',$type='',$sys_type=''){
        $array = category_config($sys_type);
        $_type = $type==='' ? $field : $type;   //兼容处理
        if($id>0){
            if($_type=='father'){    //所有父栏目，也包括自身,一般用在面包屑导航
                $farray = category_get_father($id,$sys_type);
                $self_array = [$id=>$array[$id]['name']];
                return empty($farray) ? $self_array : $farray+$self_array;
            }elseif($_type=='sons'){  //所有下一级级栏目，也包括自身，一般用在查询数据库
                $s_array = [
                        $id => $field=='name' ? $array[$id]['name'] : $id,
                ];
                $_pid = 0;
                foreach($array AS $key=>$rs){
                    if(!$rs['pid'])continue;
                    if($rs['pid']==$id||$rs['pid']==$_pid){
                        $s_array[$key] = $field=='name' ? $array[$key]['name'] : $key;
                        $_pid = $key;
                    }
                }
                return $s_array;
            }elseif($_type=='brother'){  //取同级栏目
                $s_array = [];
                $_pid = $array[$id]['pid'];
                foreach($array AS $key=>$rs){
                    if($rs['pid']==$_pid){
                        $s_array[$key]=$rs['name'];
                    }
                }
                return $s_array;
            }elseif($_type=='other'){    //取父级兄弟栏目及本级兄弟栏目及子栏目，一般用在栏目页面方便展示布局
                $m_array = [];
                $pid = $array[$id]['pid'];
                $fpid = $pid ? $array[$pid]['pid'] : null;
                $_pid = null;
                foreach($array AS $key=>$rs){
                    if($fpid!==null&&$rs['pid']==$fpid){  //父级栏目
                        $m_array[$key] = $rs['name'];
                    }elseif($rs['pid']==$pid){   //同级栏目
                        $m_array[$key] = $rs['name'];
                    }elseif ($rs['pid']==$_pid){    //子栏目
                        $m_array[$key] = $rs['name'];
                    }
                    if($key==$id){
                        $_pid = $id;
                    }
                }
                return $m_array;
            }elseif($_type=='config'){
                return $array[$id];
            }elseif(isset($array[$id][$_type])){
                return $array[$id][$_type];
            }else{
                return $array[$id];
            }
        }elseif($_type=='other' && $array){  //fid不存在的话,就只取一级栏目
            $farray = [];
            foreach($array AS $key=>$rs){
                if($rs['pid']==0){
                    $farray[$key]=$rs['name'];
                }
            }
            return $farray;
        }elseif ($_type=='all'){
            $farray = [];
            foreach($array AS $key=>$rs){
                $farray[$key]=$rs['name'];
            }
            return $farray;
        }
        return $array;
    }
}

if(!function_exists('category_config')){
    /**
     * 获取模块里边的辅栏目配置参数
     * @param string $sys_type 可以指定其他频道的目录名
     * @param unknown $pid 可以指定只调取哪些父栏目的下的子栏目数据
     * @return array|unknown
     */
    function category_config($sys_type='',$pid=null){
        if(empty($sys_type)){
            $sys_type=config('system_dirname');
        }
        if(empty($sys_type)){
            return [];
        }
        static $sort_array = [];
        $array = $sort_array[$sys_type];
        if(empty($array)){
            $array = cache('category_config_'.$sys_type);
            if (empty($array)) {
                $obj = get_model_class($sys_type,'category');
                if ($obj===false) {
                    return [];
                }
                $array = $obj->getTreeList();
                cache('category_config_'.$sys_type,$array);
            }
            $sort_array[$sys_type] = $array;
        }
        
        if($pid!==null){    //取子栏目
            $_array = [];
            foreach ($array AS $id=>$rs){
                if($rs['pid']==$pid){
                    $_array[$id] = $rs['name'];
                }
            }
            return $_array;
        }else{
            return $array;
        }
    }
}

if(!function_exists('modules_config')){
    /**
     * 获取系统安装的频道模块信息
     * @param unknown $id 可以为频道ID也可以是频道目录名 为空的话,就是取出所有
     * @param string $getcache 是否取缓存数据
     * @return NULL|unknown|string|array|NULL[]|string|array|unknown|NULL[]
     */
    function modules_config($id=null , $getcache=true){
        static $data = null;
        $array = $getcache===true ? ($data?:cache('cache_modules_config')) : '';
        if(empty($array)){
            $result = Module::getList(['ifopen'=>1]);
            foreach($result AS $rs){
                $array[$rs['id']] = $rs;
            }
            cache('cache_modules_config',$array);
        }
        $data = $array;
        if(is_numeric($id)){ //根据模块ID返回数组
            return $array[$id];
        }elseif($id!==null){ //根据模块目录名返回数组
            foreach($array AS $rs){
                if($rs['keywords']==$id){
                    return $rs;
                }
            }
            return [];
        }else{
            return $array;
        }
    }
}


if(!function_exists('plugins_config')){
    /**
     * 获取插件配置参数,也即缓存数据,可以给赋值数字或关键字目录名,取相应频道的配置参数,为NULL取所有频道的配置参数
     * @param unknown $id 可为数字或目录名关键字,也可为null 为NULL的话,取出所有
     * @param string $getcache 默认是取缓存,设置为false不要缓存
     * @return NULL|unknown|string|array|NULL[]|string|array|unknown|NULL[]
     */
    function plugins_config($id=null , $getcache=true){
        static $data = null;
        $array = $getcache===true ? ($data?:cache('cache_plugins_config')) : '';
        if(empty($array)){
            $result = Plugin::getList(['ifopen'=>1]);
            foreach($result AS $rs){
                $array[$rs['id']] = $rs;
            }
            cache('cache_plugins_config',$array);
        }
        $data = $array;
        if(is_numeric($id)){ //根据插件ID返回数组
            return $array[$id];
        }elseif($id!==null){ //根据插件目录名返回数组
            foreach($array AS $rs){
                if($rs['keywords']==$id){
                    return $rs;
                }
            }
            return [];
        }else{
            return $array;
        }
    }
}


if(!function_exists('get_ip')){
    /**
     *获取用户当前的IP地址 
     */
    function get_ip(){        
        static $onlineip  =   NULL;
        if ($onlineip !== NULL) return $onlineip;
        
        if($_SERVER['HTTP_CLIENT_IP']){
            $onlineip=$_SERVER['HTTP_CLIENT_IP'];
        }elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
            $onlineip=$_SERVER['HTTP_X_FORWARDED_FOR'];	//HTTP_X_REAL_FORWARDED_FOR
        }else{
            $onlineip=$_SERVER['REMOTE_ADDR'];
        }
        $onlineip = preg_replace("/^([\d\.]+).*/", "\\1", filtrate($onlineip));
        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipArray);
        $onlineip = $onlineipArray[0] ? $onlineipArray[0] : '0.0.0.0';
        return $onlineip;
    }
}

if(!function_exists('ipfrom')){
    /**
     * 根据IP获取来源地
     * 示例Array
        (
            [0] => 广东省茂名市
            [city] => 茂名市
            [province] => 广东省
        )
     * @param string $ip
     * @return string|unknown[]|mixed[]|string[]
     */
    function ipfrom($ip=''){
        if(!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {
            return '';
        }elseif(preg_match("/(127\.0\.0\.1|192\.168\.\d{1,3}\.\d{1,3})/", $ip)){
            return '局域网';
        }
        $str = file_get_contents("http://api.map.baidu.com/location/ip?ak=MGdbmO6pP5Eg1hiPhpYB0IVd&ip=".$ip."&coor=bd09ll");
        $array = json_decode($str ,true);
        $city = $array['content'] ? $array['content']['address_detail']['city'] : '';
        if (empty($city)) {
            return '未知地址';
        }
        return [
            0=>$array['content']['address'],
            'city'=>$city,
            'province'=>$array['content']['address_detail']['province'],
        ];
    }
}



if(!function_exists('mymd5')){
    /**
     * 站点私有的加密与解密函数.
     * @param unknown $string
     * @param string $action 默认EN加密,DE解密
     * @param string $rand 加密混淆码
     * @return string|mixed|string|NULL|boolean
     */
	function mymd5($string,$action="EN",$rand=''){ //字符串加密和解密 
		//global $webdb;
		if($action=="DE"){//处理+号在URL传递过程中会异常
			$string = str_replace('QIBOADD','+',$string);
			$string = str_replace('QIBOEDD','=',$string);
		}
		$secret_string = config('webdb.mymd5').md5_file(APP_PATH.'database.php').ROOT_PATH.$rand.'5*j,.^&;?.%#@!'; //绝密字符串,可以任意设定 
		if(!is_string($string)){
			$string=strval($string);
		}
		if($string==="") return ""; 
		if($action=="EN") $md5code=substr(md5($string),8,10); 
		else{ 
			$md5code=substr($string,-10); 
			$string=substr($string,0,strlen($string)-10); 
		}
		//$key = md5($md5code.$_SERVER["HTTP_USER_AGENT"].$secret_string);
		$key = md5($md5code.$secret_string); 
		$string = ($action=="EN"?$string:base64_decode($string)); 
		$len = strlen($key); 
		$code = "";
		for($i=0; $i<strlen($string); $i++){ 
			$k = $i%$len; 
			$code .= $string[$i]^$key[$k]; 
		}
		$code = ($action == "DE" ? (substr(md5($code),8,10)==$md5code?$code:NULL) : base64_encode($code)."$md5code");
		if($action=="EN"){//处理+号在URL传递过程中会异常
			$code = str_replace('+','QIBOADD',$code);
			$code = str_replace('=','QIBOEDD',$code);
		}
		return $code; 
	}
}

if(!function_exists('rands')){
    /**
     * 生成随机数,
     * @param unknown $length
     * @param number $strtolower 为true的时候,强制转为小写
     * @return string
     */
	function rands($length,$strtolower=true) {
	    if (function_exists('openssl_random_pseudo_bytes')) {
	        $hash = substr( bin2hex(openssl_random_pseudo_bytes($length*2)) , 0 , $length);
	    }else{
	        $hash = '';
	        $chars = substr((double)microtime(),mt_rand(2,6)).'ABCDEFGHIJK'.substr(time(),-mt_rand(0,9)).'LMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	        $max = strlen($chars) - 1;
	        //mt_srand((double)microtime() * 1000000);
	        for($i = 0; $i < $length; $i++) {
	            $hash .= $chars[mt_rand(0, $max)];
	        }
	    }
		if($strtolower){
			$hash=strtolower($hash);
		}
		return $hash;
	}
}

if(!function_exists('filtrate')){
    /**
     * 过滤一些不太安全的字符
     * @param unknown $msg
     * @return mixed
     */
	function filtrate($msg){
		//$msg = str_replace('&','&amp;',$msg);
		//$msg = str_replace(' ','&nbsp;',$msg);
		$msg = str_replace('"','&quot;',$msg);
		$msg = str_replace("'",'&#39;',$msg);
		$msg = str_replace("<","&lt;",$msg);
		$msg = str_replace(">","&gt;",$msg);
		//$msg = str_replace("\t","   &nbsp;  &nbsp;",$msg);
		//$msg = str_replace("\r","",$msg);
		//$msg = str_replace("   "," &nbsp; ",$msg);
		return $msg;
	}
}

if(!function_exists('read_file')){
    /**
     * 读文件,相当于file_get_contents函数
     * @param unknown $filename
     * @param string $method
     * @return unknown
     */
	function read_file($filename,$method="rb"){
		if($handle=@fopen($filename,$method)){
			@flock($handle,LOCK_SH);
			$filedata=@fread($handle,@filesize($filename));
			@fclose($handle);
		}
		return $filedata;
	}
}

if(!function_exists('write_file')){
    /***
     * 把内容写入文件
     * @param unknown $filename 文件名
     * @param unknown $data 内容
     * @param string $method 默认不追加写入,要追加写入,可以改为 'a'
     * @param number $iflock 锁定文件不能同时多个人同时写入
     * @return number
     */
	function write_file($filename,$data,$method="rb+",$iflock=1){
		@touch($filename);
		$handle=@fopen($filename,$method);
		if(!$handle){
			return "此文件不可写:$filename";
		}
		if($iflock){
			@flock($handle,LOCK_EX);
		}
		@fputs($handle,$data);
		if($method=="rb+") @ftruncate($handle,strlen($data));
		@fclose($handle);
		@chmod($filename,0777);	
		if( is_writable($filename) ){
			return true;
		}else{
			return false;
		}
	}
}

if(!function_exists('query')){
    /**
     * 数据库操作方法,可以执行原生数据库语句
     * 也可以直接使用TP的数据库方法,比如 query('memberdata')->where('uid',1)->value('username'); 
     * @param unknown $sql 可以是数据库语句,也可以是数据库表名,不带前缀
     * @param array|string $array 可以是数组也可以是URL字符串
     * @param number $cache_time 缓存时间 必须第二项设置了才生效
     * @return mixed|\think\cache\Driver|boolean|unknown|string|number
     */
	function query($sql,$array=[],$cache_time=0){	
	    if(preg_match('/^([\w]+)$/i', $sql)){
	        if ($cache_time>0) {
	            $key = md5($sql.http_build_query($array));
	            $_array = cache($key);
	            if ($_array) {
	                return $_array;
	            }
	        }
	        if (empty($array)) {   //只想使用DB类的情况
	            return Db::name($sql);
	        }elseif(!is_array($array)){
	            $str = $array;
	            $array = [];
	            //若有where=条件的话,必须放在最后面
	            $str =  preg_replace_callback("/&where=(.*?)$/is", function($array){
	                return '&where='.urlencode($array[1]);
	            }, $str);
	            parse_str($str, $array);
	            $array['where'] = $array['where'] ? \app\common\fun\Label::where($array['where']) : [];
	            if (isset($array['where']['uid']) && ($array['where']['uid']==0||$array['where']['uid']=='my')) {
	                $array['where']['uid'] = login_user('uid');
	            }
	            
	            //print_r($array);exit;
	        }
	        $result = false;
	        $array['where'] || $array['where']=[];
	        $obj = Db::name($sql)->where($array['where']);
	        $array['alias'] && $obj->alias($array['alias']);
	        $array['join'] && $obj->join($array['join']);
	        $array['union'] && $obj->union($array['union']);
	        $array['field'] && $obj->field($array['field']);
	        $array['having'] && $obj->having($array['having']);
	        $array['group'] && $obj->group($array['group']);
	        $array['order'] && $obj->order($array['order'],preg_match('/( asc| desc)$/i', $array['order'])?null:'desc');
	        $array['order'] || $obj->orderRaw("1 desc");
	        $array['limit'] && $obj->limit($array['limit']);
	        $array['rows'] && $obj->limit($array['rows']);
	        $array['page'] && $obj->page($array['page']);	        
	        $array['value'] && $result = $obj->value($array['value']);
	        $array['column'] && $result = $obj->column($array['column']);
	        $array['count'] && $result = $obj->count($array['count']);
	        $array['max'] && $result = $obj->max($array['max']);
	        $array['min'] && $result = $obj->min($array['min']);
	        $array['sum'] && $result = $obj->sum($array['sum']);
	        $array['avg'] && $result = $obj->avg($array['avg']);     //获取平均值
	        if($array['type']=='one'){
	            $result = getArray($obj->find());
	        }elseif($result===false){
	            $result = getArray($obj->select());
	        }
	        if ($cache_time>0) {
	            cache($key,$result);
	        }
	        return $result;
	    }else{
	        $table_pre = config('database.prefix');
	        $sql = str_replace([' qb_',' `qb_'],[" {$table_pre}"," `{$table_pre}"],$sql);
	        if( preg_match('/^(select|show) /i',trim($sql)) ){
	            try {
	                $result = Db::query($sql);
	            } catch(\Exception $e) {
	                return 'SQL执行失败，请检查语句是否正确<pre>'.$sql."\n\n".$e.'</pre>';
	            }
	        }else{
	            try {
	                $result = Db::execute($sql);
	            } catch(\Exception $e) {
	                return 'SQL执行失败，请检查语句是否正确<pre>'.$sql."\n\n".$e.'</pre>';
	            }
	        }
	        return $result;
	    }				
	}
}


if(!function_exists('delete_attachment')){
	function delete_attachment($a='',$b=''){
	}
}

if(!function_exists('delete_dir')){
    /**
     * 删除整个目录
     * @param unknown $path
     * @return string
     */
    function delete_dir($path){
        if (file_exists($path)){
            if(is_file($path)){
                if(	!@unlink($path)	){
                    $show.="$path,";
                }
            } else{
                $handle = opendir($path);
                while (($file = readdir($handle))!='') {
                    if (($file!=".") && ($file!="..") && ($file!="")){
                        if (is_dir("$path/$file")){
                            $show.=delete_dir("$path/$file");
                        } else{
                            if( !@unlink("$path/$file") ){
                                $show.="$path/$file,";
                            }
                        }
                    }
                }
                closedir($handle);
                if(!@rmdir($path)){
                    $show.="$path,";
                }
            }
        }
        return $show;
    }
}



if(!function_exists('get_word')){
    /**
     * 截取多少个字符
     * @param unknown $string
     * @param unknown $length
     * @param number $more
     * @param string $dot
     * @return unknown|string
     */
	function get_word($string, $length, $more=1 ,$dot = '..') {
		$more || $dot='';
		if(strlen($string) <= $length) {
			return $string;
		}

		$pre = chr(1);
		$end = chr(1);
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

		$strcut = '';
		if( 1 ) {

			$n = $tn = $noc = 0;
			while($n < strlen($string)) {

				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t <= 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}

				if($noc >= $length) {
					break;
				}

			}
			if($noc > $length) {
				$n -= $tn;
			}

			$strcut = substr($string, 0, $n);

		} else {
			$_length = $length - 1;
			for($i = 0; $i < $length; $i++) {
				if(ord($string[$i]) <= 127) {
					$strcut .= $string[$i];
				} else if($i < $_length) {
					$strcut .= $string[$i].$string[++$i];
				}
			}
		}

		$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		$pos = strrpos($strcut, chr(1));
		if($pos !== false) {
			$strcut = substr($strcut,0,$pos);
		}
		return $strcut.$dot;
	}
}


if(!function_exists('tempdir')){
    /**
     * 取得文件的显示路径
     * @param string $path
     * @return void|string
     */
    function tempdir($path=''){
        if($path==''){
            return ;
        }
        if (strstr($path,'"')) {
            $array = json_decode($path,true);
            $path = $array[0]['picurl']?:$path;
        }elseif(strstr($path,',')){
            $array = explode(',',$path);
            $path = $array[0]?:$path;
        }
        static $domain = null;
        if($domain === null){
            if ( config('webdb') && config('webdb.www_url')!='' ) {
                $domain = config('webdb.www_url');
            }elseif(!config('webdb')){  //比如app\common\behavior\Init获取缓存时使用;
                $domain = Db::name('config')->where('c_key','www_url')->where('type','1')->value('c_value');
            }
            $domain || $domain = request()->domain();
        }
        if(!preg_match('/:\/\//', $path)&&!preg_match('/^\/public\//', $path)){
            $path = $domain . PUBLIC_URL . $path;
        }
        elseif(strpos($path, 'http://thirdwx') !== false){
            $path = str_replace("http:","https:",$path);
        }elseif(strpos($path, 'http://thirdqq') !== false){
            $path = str_replace("http:","https:",$path);
        }
        return $path;
	}
}

if (!function_exists('get_cookie')) {
    /**
     * 取得COOKIE的值
     * @param unknown $name
     * @return mixed|void|boolean|NULL|unknown[]
     */
    function get_cookie($name=''){
        config('webdb.cookiePre') && $name = config('webdb.cookiePre') . $name;        
        return cookie($name);
        //return $_COOKIE[$webdb['cookiePre'].$name];
    }
}

if (!function_exists('set_cookie')) {
    /**
     * 设置COOKIE 
     * @param string $name 变量名
     * @param string $value COOKIE值,设置为null或为空的时候,就清空COOKIE
     * @param unknown $option 参数,可以是数字就是有效时间,也可以设置为数组,比如 ['expire' => 3600,'path'=>'/','domain'=>'']
     */
    function set_cookie($name='',$value='',$option=null){
        
        $value==='' && $value=null;
        
        if (is_numeric($option)) {
            $option = ['expire' => $option];
        } elseif (is_string($option)) {
            parse_str($option, $option);
        }
        
        if(empty($option['domain'])){
            config('webdb.cookieDomain') && $option['domain'] = config('webdb.cookieDomain');
        }
        config('webdb.cookiePre') && $name = config('webdb.cookiePre') . $name;
        
        cookie($name,$value,$option);
        //setCookie($webdb['cookiePre'].$name,$value,$cktime,$path,$domain,$S);
    }
}

if (!function_exists('makeTemplate')) {
    /**
     * 模板路径处理函数
     * member@xxx index@xxx admin@xxx 如果是跨前后台的话,将强制使用默认的default目录的文件 
     * @param unknown $template
     * @param string $check 是否检查文件是否存在
     * @return string
     */
    function makeTemplate($template,$check=true)
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            $detail = Request::instance()->dispatch();
            if($detail['type']=='method'){
                $_method = $detail['method'][1];
                list(,,$_module,,$_controller) = explode('\\',$detail['method'][0]);
            }else{
                $_module = $detail['module'][0]?:'index';   //模块,针对主页短路由的特别处理
                $_controller = $detail['module'][1]?:'index';   //控制器,针对主页短路由的特别处理
                $_method = $detail['module'][2]?:'index';   //方法,针对主页短路由的特别处理
            }            
            if (strpos($template, '@')) {
                list($module, $template) = explode('@', $template);
            }
            if (0 !== strpos($template, '/')) {
                $template = str_replace(['/', ':'], config('template.view_depr'), $template);
            } else {
                $template = str_replace(['/', ':'], config('template.view_depr'), substr($template, 1));
            }
            if (config('template.view_base')) {
                if( in_array($module,['index','member','admin']) ){    //member@xxx index@xxx admin@xxx 特殊处理
                    if( ENTRANCE != $module){   //重要提醒*************若跨前后台,只调取默认default风格的文件
                        $path   = config('template.view_base') .'../../'.$module.'_style/default/'.$module.'/';
                    }else{
                        //$path   = config('template.view_base') .'../../'.$module.'_style/'.config('template.'.$module.'_style').'/'.$module.'/';
                        $path   = config('template.view_base') . $module . '/';
                    }                    
                }elseif($_controller=='plugin'&&$_method=='execute'){
                    $_module=input('param.plugin_action');
                    $__module = isset($module) ? $module : input('param.plugin_name');
                    $path   = config('template.view_base') .'plugins/'. $__module.'/'.input('param.plugin_controller').'/';
                }else{
                    $__module = isset($module) ? $module : $_module;
                    $path   = config('template.view_base') . ($__module ? $__module . DS : '');
                }
            } else {
                if($module=='index'){
                     $path = APP_PATH . $module . DS . 'view' . DS . config('template.index_style') . DS;
                }elseif($module=='member'){
                     $path = APP_PATH . $module . DS . 'view' . DS . config('template.member_style') . DS;
                     
                }elseif($module=='admin'){
                     $path = APP_PATH . $module . DS . 'view' . DS;
                }else{
                    $path = isset($module) ? APP_PATH . $module . DS . 'view' . DS . ENTRANCE . DS . config('template.index_style') . DS : config('template.view_path');
                }
            }
            
            //if (!strpos($template, '/')&&!$module) {
            if (!$module) {
                if($_controller=='plugin'&&$_method=='execute'){
                    if (config('template.view_base')) {
                        
                    }else{
                        $_method = input('param.plugin_action');
                        $path = PLUGINS_PATH . input('param.plugin_name') . DS . 'view' . DS . ENTRANCE . DS;
                        if(ENTRANCE === 'index'){
                            $path .= config('template.index_style') . DS;
                        }elseif(ENTRANCE === 'member'){
                            $path .= config('template.member_style') . DS;
                        }
                        $path .= input('param.plugin_controller') . DS;
                    }                    
                }else{
                    $path.=$_controller.DS;
                }
                if(empty($template)){
                    $path.=$_method;
                }
            }
            $template = ($path . $template . '.' . ltrim(config('template.view_suffix'), '.'));
        }
        
        $template = get_real_path($template);
        
        //自适应模板判断开始
        $array = pathinfo($template);
        $name = $array['basename']; 
        $path = $array['dirname'].'/';
        //偿试先查找是否有对应的wap_或pc_模板
        if(!defined('USE_PC_TEMPLATE') && IN_WAP===true){   //没有声明强制使用PC模板的时候,如果WAP端,就取WAP模板
            if(!preg_match('/^wap_/', $name)){
                if(is_file($path.'wap_'.$name)){
                    return $path.'wap_'.$name;
                }
            }
        }else{
            if(!preg_match('/^pc_/', $name)){
                if(is_file($path.'pc_'.$name)){
                    return $path.'pc_'.$name;
                }
            }
        }
        //自适应模板判断结束
        
        if ($check!==true || is_file($template)) {
            return $template;
        }else{
            //echo($template.'文件不存在!<br>');
        }
    }
}

if (!function_exists('getTemplate')) {
    /**
     * 取得模板的路径,同时也可以自动识别PC或WAP模板
     * member@xxx index@xxx admin@xxx 如果是跨前后台的话,将强制使用默认的default目录的文件 
     * @param unknown $template 可以为空
     * @return void|string
     */
    function getTemplate($template='' , $check=true)
    {
        $_template = $template;
        $template = makeTemplate($template , $check);
        if (empty($template)) {
	        if( config('template.view_base') ){
                if( config('template.default_view_base') ){ //没有使用默认风格
                    $view_base = config('template.view_base');
                    $index_style = config('template.index_style');
                    $member_style = config('template.member_style');
                    $admin_style = config('template.admin_style');
                    config('template.view_base',config('template.default_view_base'));
                    config('template.index_style','default');
                    config('template.member_style','default');
                    config('template.admin_style','default');
                    $template = makeTemplate($_template,true);
                    config('template.view_base',$view_base);
                    config('template.index_style',$index_style);
                    config('template.member_style',$member_style);
                    config('template.admin_style',$admin_style);
                }
            }else{
                if(ENTRANCE === 'index'){
                    if(config('template.default_view_path')!=''){   //寻找默认风格的模板    后台使用无效，不适用于后台
                        $view_path = config('template.view_path');
                        $style = config('template.index_style');
                        $member_style = config('template.member_style');
                        config('template.view_path',config('template.default_view_path'));
                        config('template.index_style','default');
                        config('template.member_style','default');
                        $template = makeTemplate($_template,true);
                        config('template.view_path',$view_path);
                        config('template.index_style',$style);
                        config('template.member_style',$member_style);
                    }
                }elseif(ENTRANCE === 'member'){
                    if(config('template.member_style')!='default'){   //寻找默认风格的模板    后台使用无效，不适用于后台
                        $view_path = config('template.view_path');
                        $style = config('template.index_style');
                        $member_style = config('template.member_style');
                        config('template.view_path',config('template.default_view_path'));
                        config('template.index_style','default');
                        config('template.member_style','default');
                        $template = makeTemplate($_template,true);
                        config('template.view_path',$view_path);
                        config('template.index_style',$style);
                        config('template.member_style',$member_style);
                    }
                }
            }            
            if(empty($template)){
                return ;
            }
        }
//         $array = pathinfo($template);
//         $name = $array['basename'];   //basename($template);
//         $path = $array['dirname'].'/';  //dirname($template);
        
//         if(!defined('USE_PC_TEMPLATE') && IN_WAP===true){   //没有声明强制使用PC模板的时候,如果WAP端,就取WAP模板
//             if(!preg_match('/^wap_/', $name)){
//                 if(is_file($path.'wap_'.$name)){
//                     return $path.'wap_'.$name;
//                 }
//             }            
//         }else{
//             if(!preg_match('/^pc_/', $name)){
//                 if(is_file($path.'pc_'.$name)){
//                     return $path.'pc_'.$name;
//                 }
//             }  
//         }
        return $template;
    }
 }
 
  if (!function_exists('sockOpenUrl')) {
      //通过sock方式访问远程数据.
      function sockOpenUrl($url,$method='GET',$postValue='',$Referer='Y'){
          if($Referer=='Y'){
              $Referer=$url;
          }
          $method = strtoupper($method);
          if(!$url){
              return '';
          }elseif(!preg_match("/^http/",$url)){
              $url="http://$url";
          }
          $urldb=parse_url($url);
          $port=$urldb['port']?$urldb['port']:(preg_match("/^https/",$url)?443:80);
          $host=$urldb['host'];
          $query='?'.$urldb['query'];
          $path=$urldb['path']?$urldb['path']:'/';
          $method=$method=='GET'?"GET":'POST';
          
          if(function_exists('fsockopen')){
              $fp = fsockopen($host, $port, $errno, $errstr, 30);
          }elseif(function_exists('pfsockopen')){
              $fp = pfsockopen($host, $port, $errno, $errstr, 30);
          }elseif(function_exists('stream_socket_client')){
              $fp = stream_socket_client($host.':'.$port, $errno, $errstr, 30);
          }else{
              die("服务器不支持以下函数:fsockopen,pfsockopen,stream_socket_client操作失败!");
          }
          if(!$fp)
          {
              echo "$errstr ($errno)<br />\n";
          }
          else
          {
              $out = "$method $path$query HTTP/1.1\r\n";
              $out .= "Host: $host\r\n";
              $out .= "Cookie: c=1;c2=2\r\n";
              $out .= "Referer: $Referer\r\n";
              $out .= "Accept: */*\r\n";
              $out .= "Connection: Close\r\n";
              if ( $method == "POST" ) {
                  $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                  $length = strlen($postValue);
                  $out .= "Content-Length: $length\r\n";
                  $out .= "\r\n";
                  $out .= $postValue;
              }else{
                  $out .= "\r\n";
              }
              fwrite($fp, $out);
              while (!feof($fp)) {
                  $file.= fgets($fp, 256);
              }
              fclose($fp);
              if(!$file){
                  return '';
              }
              $ck=0;
              $string='';
              $detail=explode("\r\n",$file);
              foreach( $detail AS $key=>$value){
                  if($value==''){
                      $ck++;
                      if($ck==1){
                          continue;
                      }
                  }
                  if($ck){
                      $stringdb[]=$value;
                  }
              }
              $string=implode("\r\n",$stringdb);
              //$string=preg_replace("/([\d]+)(.*)0/is","\\2",$string);
              return $string;
          }
      }
  }
 
 if (!function_exists('http_curl')) {
     /**
      * 访问远程数据.
      * 微信接口,用得很频繁
      * @param string $url 对方网址
      * @param array $data 要提交的数据
      * @param string $type 可以设置为 json 数据格式提交
      * @return mixed
      */
     function http_curl($url='',$data = [],$type=''){
         $headers = '';
         if($type=='json'){
             $headers = array("Content-Type:application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
             $data=json_encode($data);
         }
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
         //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
         //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         //curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
         if (!empty($data)){
             curl_setopt($curl, CURLOPT_POST, 1);
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//http_build_query($data);
         }
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         $headers && curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
         $output = curl_exec($curl);
         if (curl_errno($curl)) {
             echo 'Errno'.curl_error($curl);
         }
         curl_close($curl);
         return $output;
     }
 }
 
 if (!function_exists('wx_getAccessToken')) {
     /**
      * 获取微信的权限通信密钥
      * @param string $check 设置为true的话，提示相关错误
      * @return void|mixed|\think\cache\Driver|boolean
      */
     function wx_getAccessToken($check=false,$is_wxapp=false){
         if($is_wxapp){    //针对微信小程序
             if( config('webdb.wxapp_appid')=='' || config('webdb.wxapp_appsecret')==''){
                 if($check==TRUE){
                     showerr('系统没有设置小程序的AppID或者AppSecret');
                 }
                 return ;
             }
             $appid = config('webdb.wxapp_appid');
             $secret = config('webdb.wxapp_appsecret');
             $token_string = '_wxapp';
         }else{     //针对公众号
             if(config('webdb.weixin_type')<2 || config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')==''){
                 if($check==TRUE){
                     if(config('webdb.weixin_type')<2){
                         showerr('系统没有设置选择认证服务号还是认证订阅号');
                     }elseif(config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')==''){
                         showerr('系统没有设置公众号的AppID或者AppSecret');
                     }
                 }
                 return ;
             }
             $appid = config('webdb.weixin_appid');
             $secret = config('webdb.weixin_appsecret');
             $token_string = '';
         }
         $access_token = cache('weixin_access_token'.$token_string);
         if (empty($access_token)) {
             $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
             $string = http_curl($url);
             $res = json_decode($string);
             $access_token = $res->access_token;
             if ($access_token) {
                 cache('weixin_access_token'.$token_string,$access_token,1800);
             }else{
                 $msg = $is_wxapp?',微信小程序(不是公众号)接口问题':',微信公众号接口问题';
                 if (strstr($string, '40125')) {
                     $msg = ',初步判断是 '.($is_wxapp?'微信小程序(不是公众号)':'微信公众号(不是小程序)').' 密钥有误,请重新配置试试';
                 }
                 showerr('获取access_token失败'.$msg.',详情如下：'.$string);
             }
         }
         return $access_token;
     }
 }
 
 if (!function_exists('wx_check_attention')) {
     /**
      * 检查是否有关注公众号
      * 务必注意使用方法::  ===ture 三个等于号才能判断已关注, ===false 才能判断没有关注 else 就是出错信息. 
      * @param unknown $openid 可以是用户UID,也可以是用户的公众号ID
      * @return boolean
      */
     function wx_check_attention($openid=''){
         if(!$openid){
             return false;
         }
         if( config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')=='' ){
             return false;
         }
         if(is_numeric($openid)){
             $array = get_user($openid);
             $openid = $array['weixin_api'];
             if (empty($openid)) {
                 return false;
             }
         }         
         $ac=wx_getAccessToken();
         $s=json_decode( http_curl("https://api.weixin.qq.com/cgi-bin/user/info?access_token=$ac&openid=$openid&lang=zh_CN"));
         if($s->subscribe==1){
             return true;
         }elseif($s->errcode){
             return 'errcode:' . $s->errcode . ', errmsg:' . $s->errmsg;
         }else{ //$s->subscribe==0
             return false;
         }
     }
 }
 
 if (!function_exists('send_admin_msg')) {
     /**
      * 给管理员发系统提醒通知
      * @param string $title 消息标题
      * @param string $content 消息内容
      */
     function send_admin_msg($title='',$content=''){
         $array = query('memberdata',[
                 'where'=>['groupid'=>3],
                 'column'=>'uid,weixin_api',
         ]);
         foreach ($array AS $uid=>$weixin_api){
             send_msg($uid,$title,$content);
             if ($weixin_api) {
                 send_wx_msg($weixin_api,$content);
             }
         }
     }
 }
 
 if (!function_exists('send_msg')) {
     /**
      * 给用户发送站内短信息
      * @param number $touid 接收消息的用户UID
      * @param string $title 消息标题,可为空
      * @param string $content 消息内容
      * @param string $sys 等于true的话,不显示发送人,设置为UID数字,指定发件人
      * @return \app\common\model\Msg
      */
     function send_msg($touid=0,$title='',$content='',$sys=true){
         if ($sys===true) {
             $fromuid = 0;
         }elseif( is_numeric($sys) && $sys>0 ){
             $fromuid = $sys;
         }else{
             $fromuid = login_user('uid');
         }
         if ($title=='') {
             if($sys===true){
                 $title = '系统消息';
             }else{
                 $title = '来自 ' . login_user('username') . ' 的消息';
             }
         }
         if (!is_numeric($touid)) {
             $array = get_user($touid,'username');
             $touid = $array['uid'];
         }
         $data = [             
             'touid'=>$touid,
             'title'=>$title,
             'content'=>$content,
             'create_time'=>time(),
             'uid'=>intval($fromuid),
         ];
         $result = MsgModel::create($data);
         if ($fromuid) {
             \app\common\model\Friend::add($fromuid,$touid);    //给用户发消息,就相当于成为他的粉丝
         }         
         return $result;
     }
 }
 
 if (!function_exists('send_wx_msg')) {
     /**
      * 给用户发送微信信息
      * @param unknown $openid 微信ID 也可以是用户的UID
      * @param unknown $content 内容
      * @param array $array 资源ID
      * @return void|boolean|mixed 发送成功则返回true 发送失败会返回相应的错误代码
      */
     function send_wx_msg($openid,$content,$array=[]){
         static $num=0;
         if (empty($openid)) {
             return ;
         }
         $content = str_replace('target="_blank"', '', $content);   //微信中有这个会暴露出源代码
         if(class_exists("\\plugins\\weixin\\util\\Msg")){
             static $obj=null;             
             $obj===null && $obj = new \plugins\weixin\util\Msg;
             $uid = 0;
             if(is_numeric($openid)){
                 $uid = $openid;
                 $_array = get_user($openid);
                 $openid = $_array['weixin_api'];
                 if (empty($openid)) {
                     return '用户WXID不存在';
                 }
             }
             $num++;
             if (!defined('IN_TASK') && $num>5 && $uid) { //处理批量发送时,请求微信服务器容易卡死                 
                 $result = fun('Msg@send',$uid,'队列发送',$content,[
                     'msgtype'=>'wxmsg',
                 ]);
                 if ($result===true) {
                     return true;
                 }
             }             
             return $obj->send($openid,$content,$array);
         }
     }
 }
 
 if (!function_exists('task_config')) {
     /**
      * 获取定时任务列表
      * @param string $refresh 设置为true则强制更新缓存配置文件
      * @return mixed|\think\cache\Driver|boolean
      */
     function task_config($refresh=false){
         $taskdb = cache('timed_task');
         if (empty($taskdb)||$refresh==true) {
             $taskdb = \app\common\model\Timedtask::where('ifopen',1)->order('list desc,id desc')->column(true);
             cache('timed_task',$taskdb);
             write_file(RUNTIME_PATH.'Task_config.txt', date('Y-m-d H:i:s'));   //生成标志,给后台任务好核对是否变化过.
         }         
         return $taskdb;
     }
 }
 
 if (!function_exists('post_olpay')) {
     /**
      * 支付接口
      * @param array $array =array(
      *                     'money'=>'支付金额',                            //单位是元,比如 0.02
      *                     'return_url'=>'支付成功后返回的网址',     //回调网址最好加上http://开头,不加也可以
      *                     'banktype'=>'支付方式微信还是支付宝',  //参数是 weixin 或者是 alipay
      *                     'numcode'=>'订单号',                           //可以留空, 最好不为空,避免反复生成多余的订单
      *                     'title'=>'商品名称',
      *                     'callback_class'=>'',                                //可以为空,即不做处理, 支付成功后,异步处理的回调类,比如 mymd5("app\\shop\\model\\Order@pay@5") 最后一项是参数,多个参数用'|'隔开
      *                     );
      *                     bank,numcode,title 这3项可以为空,不过订单号numcode最好不为空,避免反复生成多余的订单
      *                     支付成功后要异步在后端执行操作的话,请传入回调执行参数 callback_class
      *                     callback_class=>mymd5("app\\shop\\model\\Order@pay@5") 最后一项是参数,多个参数用'|'隔开
      *                     这里的callback_class参数不能明名传输,必须要用mymd5加密.这个是可解密的函数
      * @param string $jump 是否立即跳转到支付页,false的话,只取得支付链接的地址,不马上跳转
      * @return string
      */
     function post_olpay( $array=['money'=>'','return_url'=>'','banktype'=>'','numcode'=>'','title'=>'','callback_class'=>'']  , $jump = FALSE){
         foreach ($array AS $key=>$value){
             $key=='numcode' && $value=mymd5($value);
             if ($key=='callback_class'&&strstr($value,'@')) {
                 $value = mymd5($value);
             }
             $url .= $key.'='.urlencode($value).'&';
         }
         //$return_url = urlencode($array['return_url']);
         //unset($array['return_url']);
         $url = iurl('index/pay/index') . '?' . $url;   //参数不能放进路由,因为微信支付有授权目录的限制
         if($jump==true){
             header("location:$url");
             exit;
         }
         return $url;
     }
 }
 
 if (!function_exists('edit_user')) {
     /**
      * 修改用户任意资料
      * @param array $array
      * @return boolean
      */
     function edit_user($array=[]){
         if (UserModel::edit_user($array)) {
             return true;
         }else{
             return false;
         }
     }
 }
 
 if (!function_exists('add_rmb')) {
     /**
      * 人民币日志,用户的余额增或减
      * @param number $uid
      * @param number $money 变动的金额,可以是负数
      * @param number $freeze_money 这项是正数,冻结金额,这里冻结的话,上面的值要对应的为负数
      * @param string $about 附注说明
      */
     function add_rmb($uid=0,$money=0,$freeze_money=0,$about=''){

         //$money = number_format($money,2);
         //$freeze_money = number_format($freeze_money,2);
         if( !$uid || ($money==0&&$freeze_money==0) ){
             return ;
         }
         $freeze = 0;
         if($freeze_money == -$money){
             $freeze = 1;	//冻结
         }
         
         //setInc/setDec
         $user = UserModel::get_info($uid);
         UserModel::edit_user([
                 'uid'=>$uid,
                 'rmb'=>$money+$user['rmb'],
                 'rmb_freeze'=>$freeze_money+$user['rmb_freeze'],
         ]);
         
         //cache('user_'.$uid,null);
         
         //添加日志
         \plugins\marketing\model\RmbConsume::create([
             'uid'=>$uid,
             'money'=> $money,
             'freeze_money'=>$freeze_money,
             'about'=>$about,
             'posttime'=>time(),
             'freeze'=>$freeze,
         ]);
    }
 }

 if (!function_exists('add_jifen')) {
     /**
      * 积分(包括用户自定义虚拟币)增减及日志 
      * @param number $uid 用户UID
      * @param number $money 可以是负数,就是减积分
      * @param string $about 附注说明
      * @param number $type 用户自定义的积分类型,默认0是系统积分
      */
     function add_jifen($uid=0,$money=0,$about='',$type=0){
         if ($type==-1) {
             add_rmb($uid,$money,0,$about);
             return ;
         }
         if ($type>0) { //用户自定义虚拟币
             \plugins\marketing\model\Money::add($uid,$money,$type);
         }else{
             if ($money>0) {
                 UserModel::where('uid',$uid)->setInc('money',$money);
             }else{
                 UserModel::where('uid',$uid)->setDec('money',abs($money));
             }
         }
         
         cache('user_'.$uid,null);
         //添加日志
         \plugins\marketing\model\Moneylog::create([
                 'uid'=>$uid,
                 'money'=>$money,
                 'about'=>$about,
                 'type'=>intval($type),
                 'posttime'=>time(),
         ]);
     }
 }

 if (!function_exists('jf_name')) {
     /**
      * 获取虚拟币名称
      * 其中 jf_name(0) 就是系统积分的名称
      * @param unknown $type 非数字的时候,就把所有名称以数组形式列出来
      * @return mixed|array
      */
     function jf_name($type=null,$all=false){
         $array = cache('money_types');
         if (empty($array)) {
             $array = \plugins\marketing\model\Moneytype::getList();
         }
         if ($all==false) {
             foreach($array AS $key=>$rs){
                 $array[$key] = $rs['name'];
             }
         }
         $default_name = config('webdb.MoneyName')?:'积分';
         $array = [$default_name] + ($array?:[]);
         if (is_numeric($type)) {
             return $array[$type];
         }else{
             return $array;
         }
     }
 }
 
 if (!function_exists('add_dou')) {
     /**
      * 用户金豆变化
      * @param unknown $uid 用户UID
      * @param unknown $dou 可以是负数,就是减积分
      * @param string $about 附注说明
      */
     function add_dou($uid,$dou,$about=''){
         if ($dou>0) {
             UserModel::where('uid',$uid)->setInc('dou',$dou);
         }else{
             UserModel::where('uid',$uid)->setDec('dou',abs($dou));
         }
         cache('user_'.$uid,null);
     }
 }


 
 if (!function_exists('get_dir_file')) {
     /**
      * 取某个目录下的所有指定类型的文件
      * @param string $path
      * @param string $_suffix 文件后缀,多个用逗号隔开,比如 'htm,txt'
      * @return string
      */
     function get_dir_file($path='',$_suffix=''){
         $suffix = explode(',', $_suffix);
         $dir = opendir($path);
         while (false!=($file=readdir($dir))){
             if(is_file($path.'/'.$file)){
                 $detail = explode('.', $file);
                 if(in_array(end($detail), $suffix)){
                     $array[] = $path.'/'.$file;
                 }
             }elseif($file!='.'&&$file!='..'){
                 $_array = get_dir_file($path.'/'.$file,$_suffix);
                 if(is_array($_array)){
                     $array = $array ? array_merge($array,$_array) : $_array ;
                 }
             }            
         }
         return $array;
     }
 }
 
 if (!function_exists('del_html')) {
     /**
      * 清除html代码
      * @param string $content
      * @param string $only_delhide 是否只做清除隐藏代码,比如获取内容图片的时候就用到
      * @return mixed|unknown
      */
     function del_html($content='',$only_delhide=false){         
         $content = preg_replace("/\[face(\d+)\]/is",'',$content);  //过滤掉QQ表情 [reply] 请在这括号范围内输入要隐藏的内容 [/reply]
         $content = preg_replace("/\[reply\](.*?)\[\/reply]/is",'',$content);   //过滤掉回复可见的内容
         $content = preg_replace("/\[group=([\d,]+)\](.*?)\[\/group]/is",'',$content);  //过滤掉指定用户可见
         $content = preg_replace("/\[password=([^\]]+)\](.*?)\[\/password]/is",'',$content); //过滤掉密码才能看的内容
         $content = preg_replace("/\[paymoney=([\d]+)\](.*?)\[\/paymoney]/is",'',$content); //过滤掉积分购买的内容
         $content = preg_replace("/\[pay ([^\]]+)\](.*?)\[\/pay]/is",'',$content);  //付费或圈内可看
         $content = preg_replace("/\[qun\](.*?)\[\/qun]/is",'',$content);   //过滤掉仅圈内成员可见的内容
         $content = preg_replace("/\[iframe_mv\](.*?)\[\/iframe_mv]/is",'',$content);   //过滤掉站外视频
         $content = preg_replace("/\[topic ([^\]]+)\](.*?)\[\/topic]/is",'\\2',$content);   //站内引用主题         
         if($only_delhide){
             return $content;
         }
         $content = str_replace("\r",'',$content);
         $content = str_replace("\n",'',$content);
         $content = str_replace('　',' ',$content);  //全角空格换成半角空格
         $content = str_replace('  ','',$content);      //两个以上的全角空格清掉,一个的话,保留,即最后只有单数的一个,双数话,可能0个
         $content = preg_replace('/<([^<]*)>/is','',$content);	       //把HTML代码过滤掉
         $content = str_replace('&nbsp;','',$content);	       //把空格代码过滤掉
         
         return $content;
     }
 }
 
 if (!function_exists('in_wap')) {
     /**
      * 检查是否为wap访问,也包含微信端
      * @return boolean
      */
     function in_wap(){
         $regex_match="/(WindowsWechat|iPad|nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
         $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
         $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
         $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
         $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
         $regex_match.=")/i";
         return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
     }
 }
 
 if (!function_exists('in_weixin')) {
     /**
      * 是否在微信客户端浏览器中打开 , 注意 如果在小程序中打开,也返回true
      * @return boolean
      */
     function in_weixin(){
         if( strstr($_SERVER['HTTP_USER_AGENT'],"MicroMessenger") || strstr($_SERVER['HTTP_USER_AGENT'],"WindowsWechat")){
             return true;
         }else{
             return false;
         }
     }
 }
 
 if (!function_exists('in_wxapp')) {
     /**
      * 是否在小程序中打开 , 注意 苹果系统不存在miniProgram 无法判断
      * @return boolean
      */
     function in_wxapp(){
         if( strstr($_SERVER['HTTP_USER_AGENT'],"miniProgram") ){
             return true;
         }else{
             return false;
         }
     }
 }
 
 

 
 if (!function_exists('get_sons')) {
     /**
      * 树型数组取下级子数组
      * @param unknown $array
      * @return unknown[]|unknown
      */
     function get_sons($array){
         if(empty($array)){
             return ;
         }
         $listdb = [];
         $pid = 0;
         foreach ($array AS $key=>$rs){
             if($rs['pid']){
                 $listdb[$pid]['sons'][$rs['id']] = $rs;
             }else{
                 $listdb[$rs['id']] = $rs;
                 $pid = $rs['id'];
             }             
         }
         return $listdb;
     }
 }

 
 if (!function_exists('get_url')) {
     /**
      * 获取各种网址,用得很频繁
      * @param unknown $type
      * @param array $array
      * @return string|unknown
      */
     function get_url($type,$array=[]){
         switch ($type){
             case 'msg':    //即时聊天
                 $url = iurl('index/msg/index').'#/public/static/libs/bui/pages/chat/chat?uid='.$array;
                 break;
             case 'reg':    //通用注册
                 $url = iurl('index/reg/index',$array);
                break;
             case 'login':  //通用登录
                 $url = iurl('index/login/index',$array).'?fromurl='.urlencode(request()->url(true));
                break;
             case 'wx_login':   //微信登录
                 $url = purl('weixin/login/index',$array).'?fromurl='.urlencode(request()->url(true));
                 break;
             case 'qq_login':   //QQ登录
                 $url = purl('login/qq/index',$array,'index').'?fromurl='.urlencode(request()->url(true));
                 break;
             case 'quit':   //退出
                 $url = iurl('index/login/quit',$array);
             break;
             case 'from':   //来源地址
                 $url = $_SERVER["HTTP_REFERER"];
                 break;
             case 'location':   //当前网址
                 $url = request()->url(true);
                 break;
             case 'home':   //访问主页
                 $url = request()->domain().'/';
                 break;
             case 'member':  //会员中心
                 $url = murl('member/index/index');//request()->domain().'/member.php';
                 break;
             case 'user':   //用户的主页
                 $url = murl('member/user/index',is_numeric($array)?['uid'=>$array]:$array);
                 break;
              default:  //把网址加上http域名
                  $url = preg_match('/:\/\//', $type)?$type:request()->domain().$type;
         }
         return $url;
     }
 }
 
 
 if (!function_exists('M')) {
     /**
      * 查找具体某个频道模块的相关信息,比如频道ID 频道目录名关键字
      * @param string $type key或keyword取目录名关键字,name值取名称,id值取模型的ID
      * @return mixed|array|boolean|NULL|unknown
      */
     function M($type='',$dirname=''){
         $dirname || $dirname = config('system_dirname');
         if ($type=='key'||$type=='keyword') {
             return $dirname;
         }elseif($type=='name'){
             $array = modules_config();
			 foreach($array AS $rs){
				 if($rs['keywords']==$dirname){
					 return $rs['name'];
				 }
			 }
         }elseif($type=='id'){
             $array = modules_config();
             foreach($array AS $rs){
                 if($rs['keywords']==$dirname){
                     return $rs['id'];
                 }
             }
         }else{
             $array = modules_config();
             foreach($array AS $rs){
                 if($rs['keywords']==$dirname){
                     return $rs;
                 }
             }
         }
     }
 }
 
 if(!function_exists('model_config')){
     /**
      * 获取频道的模型配置参数
      * @param number $mid 模型ID,可为空,为空的话,就是取所有模型数据
      * @param string $sys_type 特别指定哪个目录的频道
      * @return array|unknown|mixed|\think\cache\Driver|boolean
      */
     function model_config($mid=null,$sys_type=''){
         if(empty($sys_type)){
             $sys_type=config('system_dirname');
         }
         if(empty($sys_type)){
             return [];
         }
         static $model_array = [];
         $array = $model_array[$sys_type];
         if(empty($array)){
             $array = cache('model_config_'.$sys_type);
             if (empty($array)) {
                 //$array = model($sys_type.'/module')->getList();
                 $array = get_model_class($sys_type,'module')->getList();
                 cache('model_config_'.$sys_type,$array);
             }
             $model_array[$sys_type] = $array;
         }
         if (empty($mid)) {
             return $array;
         }elseif($mid=='default_id'){
             return current(model_config())['id'];
         }else{
             return $array[$mid];
         }         
     }
 }

if (!function_exists('get_field')) {
    /**
     * 取得具体某个频道的模型的字段配置信息
     * @param number $mid 模型ID
     * @param string $dirname 频道目录名,可为空
     */
    function get_field($mid=0,$dirname=''){
        $dirname || $dirname = config('system_dirname');
        static $field_array = [];
        $list_f = $field_array[$dirname];
        if(empty($list_f)){
            $list_f = cache($dirname.'__field');
            if (empty($list_f)) {
                $array = get_model_class($dirname,'field')->getFields([]);
                foreach($array AS $rs){
                    $list_f[$rs['mid']][$rs['name']] = $rs;
                }
                cache($dirname.'__field',$list_f);
            }
            $field_array[$dirname] = $list_f;
        }        
        return $list_f[$mid];        
    }
}

if (!function_exists('send_mail')) {
    /**
     * 发送邮件 
     * @param unknown $email 对方邮箱
     * @param unknown $title 邮件标题
     * @param unknown $content 邮件内容
     * @return boolean|string 发送成功会返回true 发布失败会返回对应的错误代码
     */
    function send_mail($email='',$title='',$content=''){
        if(is_numeric($email)){
            $array = get_user($email);
            $email = $array['email'];
            if (empty($email)) {
                return '用户邮箱不存在';
            }
        }
        static $obj = null;
        if ($obj===null) {
            $obj = new \app\common\util\Email;
        }        
        return $obj->send($email,$title,$content);
    }
}

if (!function_exists('send_sms')) {
    /**
     * 发送短信,主要用于验证码
     * @param string $phone 手机号码
     * @param string $msg 验证码内容
     * @return boolean|string 发送成功会返回true 发布失败会返回对应的错误代码
     */
    function send_sms($phone='',$msg=''){
        if($phone<99999999){
            $array = get_user($phone);
            $phone = $array['mobphone'];
            if (empty($phone)) {
                return '用户手机号不存在';
            }
        }
        static $obj = null;
        if ($obj===null) {
            $obj = new \app\common\util\Sms;
        }
        return $obj->send($phone,$msg);
    }
}



if (!function_exists('get_model_class')) {
    /**
     * 取得对应的模型
     * @param unknown $dirname 目录名
     * @param unknown $type 类名
     * @return unknown
     */
    function get_model_class($dirname,$type){
        $dispatch = request()->dispatch();
        if($dispatch['module'][1]=='plugin' && $dispatch['module'][2]=='execute'){
            $path = 'plugins';
        }else{
            $path = 'app';
        }
        $classname = "$path\\$dirname\\model\\".ucfirst($type);
        if(class_exists($classname)==false){
            $_path =  $path=='app'?'plugins':'app';
            $classname = "$_path\\$dirname\\model\\".ucfirst($type);
        }
        if(class_exists($classname)==false){
            return false;
        }
        $obj = new $classname;
        return $obj;
    }    
}

if (!function_exists('login_user')) {
    /**
     * 用户登录后的个人信息
     * @param string $key
     * @return void|mixed|\think\cache\Driver|boolean|number|array|\think\db\false|PDOStatement|string|\think\Model
     */
    function login_user($key=''){
        static $array = [];
        if(empty($array)){
            $array = UserModel::login_info();
        }        
        if($key!=''){
            return $array[$key];
        }else{
            return $array;
        }
    }
}


if (!function_exists('url_clean_domain')) {
    /**
     * 把本站的HTTP地址过滤掉
     * @param string $code
     * @return unknown
     */
    function url_clean_domain($code=''){
        static $domain = null;
        if($domain === null){
            $domain = request()->domain() ;
        }
        return str_replace($domain.PUBLIC_URL, '', $code);
    }
}

if (!function_exists('get_qrcode')) {
    /**
     * 获取某个网址的二维码
     * @param string $url 要生成的真实二维码网址
     * @param string $logo 小LOGO地址
     *  @param string $is_wxapp 是否为小程序码
     * @return string
     */
    function get_qrcode($url='',$logo='',$is_wxapp=false){
        static $domain = '';
        if($domain === ''){
            $domain = request()->domain();
        }
        if (!is_numeric($url)) {
            if ($url) {
                $url = preg_match('/:\/\//', $url) ? $url : $domain.$url;
            }else{
                $url = request()->url(true);
            }
        }        
        return iurl($is_wxapp?'index/qrcode/wxapp':'index/qrcode/index') . '?url=' . urlencode($url).($logo?'&logo='.urlencode($logo):'');
    }
}

if (!function_exists('weixin_share')) {
    /**
     * 微信JS接口
     * @param string $key
     * @return void|string|number|NULL
     */
    function weixin_share($type=''){
        if (!defined('LOAD_SHARE')) {
            define('LOAD_SHARE', TRUE);     //方便插件调用判断是否已加载过
        }
        if(config('webdb.weixin_type')<2 || config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')==''){
            return ;
        }
        if(!in_weixin()){
            return ;
        }
        static $array = [];
        if(empty($array)){            
            $jssdk = new \app\common\util\Weixin_share(config('webdb.weixin_appid'),config('webdb.weixin_appsecret'));
            $array = $jssdk->GetSignPackage();
        }
        if($type){
            return $array[$type];
        }else{
            return $array;
        }
    }
}

if (!function_exists('weixin_login')) {
    /**
     * 强制微信登录
     * @param string $url 登录成功后,返回的地址
     * @param string $jump 默认是立即跳转,可以设置为false 只获取跳转地址,
     */
    function weixin_login($url='',$jump=true){
        $url = $url=='' ? request()->url(true) : $url;
        if( in_weixin() && config('webdb.weixin_type')==3 ){    //在微信端并且是认证服务号的情况下
            $url = purl('weixin/login/index',[],'index') . '?fromurl=' . urlencode($url);
        }else{            
            $url = iurl('index/login/index').'?fromurl='.urlencode($url);
        }
        if ($jump===true) {
            header("location:$url");
            exit;
        }else{
            return $url;
        }
    }
}

if (!function_exists('makepath')) {
    /**
     * 自动创建多级目录
     * @param unknown $path
     * @return string|mixed
     */
    function makepath($path){
        //这个\没考虑
        $path=str_replace("\\","/",$path);
        $ROOT_PATH=str_replace("\\","/",ROOT_PATH);
        $detail=explode("/",$path);
        foreach($detail AS $key=>$value){
            if($value==''&&$key!=0){
                //continue;
            }
            $newpath.="$value/";
            if((preg_match("/^\//",$newpath)||preg_match("/:/",$newpath))&&!strstr($newpath,$ROOT_PATH)){continue;}
            if( !is_dir($newpath) ){
                if(substr($newpath,-1)=='\\'||substr($newpath,-1)=='/')
                {
                    $_newpath=substr($newpath,0,-1);
                }
                else
                {
                    $_newpath=$newpath;
                }
                if(!is_dir($_newpath)&&!mkdir($_newpath)&&preg_match("/^\//",ROOT_PATH)){
                    return false;
                }
                chmod($newpath,0777);
            }
        }
        return $path;
    }
}


if (!function_exists('get_pinyin')) {
    /**
     * 取得汉字的拼音
     * @param string $word 汉字
     * @param string $type all:全拼音|first:首字母|one:仅第一字符首字母
     * @return string|number
     */
    function get_pinyin($word='',$type='all'){
        //$obj = new \pinyin\Py;
        //return $obj->change2pinyin($word,$type);
        return \pinyin\Pinyin::get($word,$type);
    }
}

if (!function_exists('get_md5_num')) {
    /**
     * 生成随机字串,主要用在手机或邮箱获取注册码的时候,防止用户中途又换了其它邮箱或手机号
     * @param unknown $str
     * @param number $num
     * @return string
     */
    function get_md5_num($str,$num=6){
        $str .= config('webdb.mymd5');
        return substr(preg_replace('/(1|l|o|0|q|z)/i','',md5($str)),0,$num);
    }
}



if (!function_exists('check_bom')) {
    /**
     * 检查文件是否包含UTF8+BOM看不到的三个字符.
     * @param string $filename
     * @param string $onlycheck 为false时返回去除BOM后的内容,为TRUE的话,仅仅做检查文件是否包含BOM
     * @return boolean|string
     */
    function check_bom($filename='',$onlycheck=false){
        $contents = is_file($filename) ? file_get_contents($filename) : $filename;
        $charset[1] = substr($contents, 0, 1);
        $charset[2] = substr($contents, 1, 1);
        $charset[3] = substr($contents, 2, 1);
        if(ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191){
            if($onlycheck==true){
                return true;
            }else{
                $contents = substr($contents, 3);
            }
        }
        if($onlycheck==false){
            return $contents;
        }
    }
}





if(!function_exists('get_area')){
    /**
     * 获取地区数据,如果第三项不为0的话,则获取其子栏目的数据,这个时候,前两项设置无效
     * @param number $id 某个地区的ID
     * @param string $field 可以取值为name 名称 或 pid 父ID 
     * @param number $pid 指定父ID
     */
    function get_area($id=0,$field='name',$pid=null){
        if($id==0 && $pid===null){
            return ;
        }
        $area_array = cache('area_cache');
        if(empty($area_array)){
            $array= \plugins\area\model\Area::get_all();
            $fup = [];
            foreach ($array AS $rs){
                $fup[$rs['pid']][$rs['id']] = $rs['name'];
            }
            $area_array = [
                    'list'=>$array,
                    'fup'=>$fup
            ];
            cache('area_cache',$area_array);
        }
        if($pid!==null){
            return is_numeric($pid) ? $area_array['fup'][$pid] : reset($area_array['fup']);
        }else{
            return $area_array['list'][$id][$field];
        }
    }
    
}



if(!function_exists('get_status')){
    /**
     * 自定义通用状态助手
     * @access public
     * @param int $state 状态
     * @param array  $array 自定义数组
     * @return string
     */
    function get_status($state,$array=['禁用','正常']) {
        if(is_string($array)){
            $array = explode(',',$array);
        }
        return $array[$state];
    }
}

if (!function_exists('logs')) {
    /**
     * 开发调试日志
     * @param unknown $code
     * @param string $type
     * @param string $filename
     */
    function logs($code,$type=true,$filename='log.txt'){
        is_array($code) && $code = var_export($code,true);
        $code .= $type==true ? "\r\n" : '';
        write_file(ROOT_PATH.$filename,$code ,$type==true?'a':'rb+');
    }
}

if(!function_exists('fork_set')){
    /**
     * 设置分支进程，linux才支持
     */
    function fork_set(){
        if(!function_exists('pcntl_fork')){
            define('FORK',-1);
        }else{
            $pid = pcntl_fork();
            define('FORK',$pid);
        }        
    }
}

if(!function_exists('fork_main')){
    /**
     * 主进程 不执行耗时任务
     * @return boolean
     */
    function fork_main(){
        if(!defined('FORK')|| FORK==-1 || FORK>0){
            return true;
        }
    }
}

if(!function_exists('fork_son')){
    /**
     * 子进程 执行耗时任务
     * @return boolean
     */
    function fork_son(){
        if(!defined('FORK') || FORK==-1 || FORK==0){
            return true;
        }
    }
}

if(!function_exists('cache2')){
    /**
     * 增强版的cache函数,用法跟cache函数类似
     * 但有几点要注意的是
     * 1、cache2(['a','b','c'],'x') 这种格式代表批量插入一个Redis 列表(List)记录 key就是 x ;这个列可以有几万甚至几十万条记录
     * 2、$value的值为 lpop 或 rpop 的时候,第一项就必须是键名,配合上面的列表使用,代表获取列表记录的其中一个值,lpop正序获取,rpop倒序获取,获取后,那一项记录也会自动踢除 
     * 3、另外$key为db的时候,就直接返回Redis的类 ,此时可以直接使用原生 Redis类
     * 4、cache2('ab*',null) 代表把所有ab开头的key那些项目删除,cache2('ab*') 代表列出所有包含ab开头的key,只列出key,不列出值
     * 5、cache2()全部参数为空的话,就可以直接操作Redis的方法,比如 cache2()->get('aa'); cache2()->set('aa',88);
     * 这三个关键点要特别注意，也就是增强功能的体现
     * 更详细的教程 http://help.php168.com/1477144
     * @param string $key
     * @param string $value
     * @param number $time
     */
    function cache2($key='',$value='',$time=0){
        if (config('cache.prefix')!='' && $key!=='') {
            if (is_array($key)) {
                if($value!=='' && $value!==null && isset($key[0])){    //插入一批数据
                    $value = config('cache.prefix').'___'.$value;
                }else{
                    foreach ($key AS $k=>$v){
                        $k = config('cache.prefix').'___'.$k;
                        $key[$k] = $v;
                    }
                }
            }else{
                $key = config('cache.prefix').'___'.$key;
            }
        }
        if (is_array($key)) {
            Cache2::set($key,$value,$time);
        }elseif ($value===null) {
            Cache2::set($key,null);
        }elseif($value===''){
            if ($key==='') {
                return Cache2::db();
            }else{
                return Cache2::get($key);
            }            
        }elseif($value==='lpop'||$value==='rpop'){
            return Cache2::get($key,$value);
        }else{
            Cache2::set($key,$value,$time);
        }
    }
}

if(!function_exists('showerr')){
    function showerr($msg = '', $url = null, $data = '', $wait = 60, array $header = []){
        $obj = new Base;
        $obj->showerr($msg , $url , $data , $wait , $header);
    }
}



if (!function_exists('run_label')) {
    //下面这些标签用到的函数将弃用. 改用 fun 函数
    function comment_api($type='',$aid=0,$sysid=0,$cfg=[]){return fun('label@comment_api',$type,$aid,$sysid,$cfg);}
    function reply_api($type='',$aid=0,$cfg=[]){return fun('label@reply_api',$type,$aid,$cfg);}
    function run_label($tag_name,$cfg){fun('label@run_label',$tag_name,$cfg);}
    function label_ajax_url($tag_name='',$dirname){fun('label@label_ajax_url',$tag_name,$dirname);}
    function run_form_label($tag_name,$cfg){fun('label@run_form_label',$tag_name,$cfg);}
    function run_listpage_label($tag_name,$cfg){return fun('label@run_listpage_label',$tag_name,$cfg);}
    function run_showpage_label($tag_name,$info,$cfg){return fun('label@run_showpage_label',$tag_name,$info,$cfg);}
    function label_listpage_ajax_url($tag_name=''){fun('label@label_listpage_ajax_url',$tag_name);}
    function run_comment_label($tag_name,$info,$cfg){fun('label@run_comment_label',$tag_name,$info,$cfg);}
    function reply_label($tag_name,$info,$cfg){fun('label@reply_label',$tag_name,$info,$cfg);}
}

//下面这些用到的函数将弃用. 改用 fun 函数
if (!function_exists('get_app_upgrade_edition')) {
    function get_app_upgrade_edition(){return fun('upgrade@local_edition');}
}
if(!function_exists('make_area_url')){
    function make_filter_url($type='zone_id'){return fun('field@make_filter_url',$type);}
}
if(!function_exists('format_field')){    
    function format_field($info=[],$field='',$pagetype='list',$sysname=''){return fun('field@format',$info,$field,$pagetype,$sysname);}
}
if(!function_exists('get_filter_fields')){
    function get_filter_fields($mid=0){return fun('field@list_filter',$mid);}
}
if(!function_exists('set_date')){
    function set_date($time,$format='Y-m-d H:i:s'){return $time ? date($format,$time) : '';}
}
if(!function_exists('refreshto')){
    function refreshto($url,$msg,$time=1){header("location:$url");exit;}
}
if(!function_exists('jump')){
    function jump($msg,$url,$time=1){header("location:$url");exit;}
}
if(!function_exists('showmsg')){
    function showmsg($msg){die($msg);}
}
if (!function_exists('get_file_path')) {
    function get_file_path($id=0){return fun('zbuilder@get_file_path',$id);}
}
if (!function_exists('get_thumb')) {
    function get_thumb($id=0){return fun('zbuilder@get_thumb',$id);}
}


if (!function_exists('get_web_menu')) {
    function get_web_menu($type=''){return fun('page@get_web_menu',$type);}
}
if (!function_exists('getNavigation')) {
    function getNavigation($link_name='',$link_url='',$fid=0){return fun('page@getNavigation',$link_name,$link_url,$fid);}
}

if (!function_exists('label_format_where')) {
    function label_format_where($code=''){return fun('label@where',$code);}
}
