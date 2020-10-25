<?php
namespace HY;
/*
    1.6.6
*/

class HY
{
    public static $_CLASS = array();
    public static $logs = array();
    public static function start()
    {
        define('HY_V','1.6.6');
        $GLOBALS['START_TIME'] = microtime(TRUE);

        date_default_timezone_set('PRC');

        //声明编码 UTF8
        header("Content-Type: text/html; charset=UTF-8");

        //内存记录
        if(function_exists('memory_get_usage')) 
            $GLOBALS['START_MEMORY'] = memory_get_usage();

        //系统信息
        define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
        define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
        define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

        $_SERVER['time'] = $_SERVER['REQUEST_TIME'];
        $_SERVER['ip'] = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'0.0.0.0';

        define('NOW_TIME',$_SERVER['REQUEST_TIME']);
        define('CLIENT_IP',$_SERVER['ip']);
        isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] = 'CGI';
        define('IS_GET',$_SERVER['REQUEST_METHOD'] =='GET' ? true : false);
        define('IS_POST',$_SERVER['REQUEST_METHOD'] =='POST' ? true : false);
        define('IS_AJAX',
            ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
            !empty($_POST['ajax']) ||
            !empty($_GET['ajax'])) ? true : false);


        //
        defined('PATH')         or define('PATH',          dirname($_SERVER['SCRIPT_FILENAME']).'/');//网站根目录
        defined('ACTION_PATH')  or define('ACTION_PATH',   PATH.'Action/'); //Action目录
        defined('VIEW_PATH')    or define('VIEW_PATH',     PATH.'View/'); //VIEW
        defined('CONF_PATH')    or define('CONF_PATH',     PATH.'Conf/'); //CONF
        defined('TMP_PATH')     or define('TMP_PATH',      PATH.'Tmp/'); //Tmp
        defined('TMPHTML_PATH') or define('TMPHTML_PATH',  PATH.'TmpHtml/'); //TmpHtml
        defined('MYLIB_PATH')   or define('MYLIB_PATH',    PATH.'Lib/'); //Lib
        defined('MODEL_PATH')   or define('MODEL_PATH',    PATH.'Model/'); //Model
        defined('PLUGIN_PATH')  or define('PLUGIN_PATH',    PATH.'Plugin/'); //插件目录

        defined('HY_PATH')      or define('HY_PATH',       __DIR__.'/'); //框架目录

        defined('LIB_PATH')     or define('LIB_PATH',      realpath(HY_PATH.'Lib').'/'); // 系统核心类库目录
        defined('DEBUG')        or define('DEBUG',         false); //是否调试
        defined('PLUGIN_ON')    or define('PLUGIN_ON',     false); //插件机制开启
        defined('PLUGIN_ON_FILE')    or define('PLUGIN_ON_FILE',     false); //插件机制开启
        defined('PLUGIN_MORE_LANG_ON')    or define('PLUGIN_MORE_LANG_ON',     false); //插件机制开启

        is_dir(ACTION_PATH)    or mkdir(ACTION_PATH);
        is_dir(VIEW_PATH)      or mkdir(VIEW_PATH);
        is_dir(CONF_PATH)      or mkdir(CONF_PATH);
        is_dir(TMP_PATH)       or mkdir(TMP_PATH);
        is_dir(TMPHTML_PATH)   or mkdir(TMPHTML_PATH);
        is_dir(MYLIB_PATH)     or mkdir(MYLIB_PATH);
        is_dir(MODEL_PATH)     or mkdir(MODEL_PATH);
        is_dir(PLUGIN_PATH)    or mkdir(PLUGIN_PATH);
        
        if(!is_file(CONF_PATH   . "config.php")){
            if(!is_writable(CONF_PATH))
                die('/Conf 目录没有写入权限. 请修改文件目录权限');
            file_put_contents(CONF_PATH   . "config.php","<?php \r\nreturn array(\r\n);");
        }
        if(!is_file(ACTION_PATH . "Index.php" )){
            if(!is_writable(ACTION_PATH))
                die('/Conf 目录没有写入权限. 请修改文件目录权限');
            file_put_contents(ACTION_PATH . "Index.php" ,"<?php \r\nnamespace Action;\r\nuse HY\Action;\r\nclass Index extends Action {\r\npublic function Index(){\r\necho 'HY框架';\r\n}\r\n}");
        }
        
         

        header('X-Powered-By:HYPHP');
        if(isset($argv) && count($argv) == 3)
            $GLOBALS['argv']=$argv;
        spl_autoload_register('HY\\HY::autoload');
        if (DEBUG) {
            error_reporting(E_ALL | E_STRICT);
            //error_reporting(E_ALL & ~(E_NOTICE | E_STRICT));
            @ini_set('display_errors', 'ON');
        } else {
            error_reporting(0);
            @ini_set('display_errors', 'OFF');
        }
        set_error_handler('HY\\HY::hy_error');
        set_exception_handler('HY\\HY::hy_exception');
        $config = include HY_PATH . 'conf.php';

        $config = array_merge($config,include CONF_PATH . 'config.php');

        include LIB_PATH . 'function.php';
        

        C($config);


        define('EXT',C("url_suffix"));
        define('EXP',C("url_explode"));
        
        define('IS_MOBILE',hy_is_mobile());
        define('IS_SHOUJI',IS_MOBILE);
        define('IS_WAP',IS_MOBILE);

        $url='';
        if(isset($_GET['s']))
            $url = ltrim(strtolower($_GET['s']), C("url_explode"));
        
        $class = '';
        $Action = 'Index';
        $_Action = 'Index';
        $_Fun = 'Index';

        $_GET['HY_URL']=array('Index','Index');

        if (empty($url)) {
            if(isset($GLOBALS['argv'])){
                if(isset($GLOBALS['argv'][1]) && isset($GLOBALS['argv'][2]))
                    $class = '\\Action\\'.ucfirst($GLOBALS['argv'][1]);
                    $Action = $_Action = ucfirst($GLOBALS['argv'][1]);
                    $_Fun = $_Fun = ucfirst($GLOBALS['argv'][2]);
            }else{
                $class = '\\Action\\Index';
            }
        } else {
            $info = str_replace(C("url_suffix"), '', $url);
            $info = $_GET['HY_URL'] = explode(C('url_explode'), $info);

            $Action = isset($info[0]) ? $info[0] : 'Index';
            $Fun = isset($info[1]) ? $info[1] : 'Index';

            $Action=trim($Action,'/');

            $Fun=trim($Fun,'/');

            $Action = $Action == '' ? 'Index' : $Action;
            $Fun = $Fun == '' ? 'Index' : $Fun;
            for ($i = 2; $i < count($info); $i++) {
                $_GET[$info[$i++]] = isset($info[$i]) ? $info[$i] : '';
            }
            if(isset($config['HY_URL']['action'])){
                $z = array_search($Action,$config['HY_URL']['action']);
                if($z){
                    $Action = $z;
                    if(isset($config['HY_URL']['method'][$z])){
                        $b = array_search($Fun,$config['HY_URL']['method'][$z]);
                        if($b)
                            $Fun=$b;
                    }

                }
            }
            $_Action = $Action = ucfirst($Action);
            $_Fun = $Fun = ucfirst($Fun);
            $class = "\\Action\\{$_Action}";
        }
        define('ACTION_NAME', $_Action);
        define('METHOD_NAME', $_Fun);
        
        if (!file_exists(ACTION_PATH . "{$Action}.php")) {
            if (!file_exists(ACTION_PATH . 'No.php')) {
                E("{$Action}控制器不存在!");
            } else {
                $class = '\\Action\\No';
            }
        }
        if (file_exists(MYLIB_PATH . 'function.php')) {
            include MYLIB_PATH . 'function.php';
        }

        $module = new $class();
        if (!method_exists($module, $_Fun) || !preg_match('/^[A-Za-z](\/|\w)*$/',$_Fun)) {
            if (!method_exists($module, '_no')) {

                E("你的{$class}没有存在{$_Fun}操作方法");
            }
            $_Fun = '_no';
        }
        

        $method = new \ReflectionMethod($module, $_Fun);
        if ($method->isPublic() && !$method->isStatic()) {
            $class = new \ReflectionClass($module);
            $method->invoke($module);
        }

        $GLOBALS['END_TIME'] = microtime(TRUE);
        if (C('DEBUG_PAGE')) {
            $DEBUG_SQL = self::$logs;
            if (empty($url)) {
                $url = '/';
            } else {
                $url = '/' . $url;
            }
            $DEBUG_CLASS = self::$_CLASS;
            require HY_PATH . 'View/Debug.php';
        }

    }
    public static function hy_exception( $e){

        if(!isset($GLOBALS['Exception_save_log']))
            $GLOBALS['Exception_save_log'] = true;
        $file = $e->gettrace();
        $getFile = $e->getFile();
        $getLine = $e->getLine();
        
        if(isset($file[0]['args'][2])){
            $getFile = $file[0]['args'][2];
            if(isset($file[0]['args'][3])){
                if(!is_array($file[0]['args'][3]))
                    $getLine = $file[0]['args'][3];
            }
            
        }

        
        
        

        $s = '';
        $log = New \HY\Lib\Logs;

        if(is_array($getFile)) $getFile = '';
        $text = $e->getMessage() .'  #发生错误的文件位于: '. $getFile .' #行数: ' .$getLine . ' #发生时间: '.date("Y-m-d H:i:s").' ##发生URL: ' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . "\r\n";

        $translate = include LIB_PATH . 'translate.php';
        foreach ($translate as $key => $value) {
            $text = str_replace($key,$value,$text);
        }

        if($GLOBALS['Exception_save_log']){

            $log->log($text);
            //die($text);
        }


        if (DEBUG) {
            if(IS_AJAX){

                header('HTTP/1.1 200 OK'); 
                header('Content-Type:application/json; charset=utf-8');
                die(json_encode(array('error'=>false,'info'=>$text,'data'=>$text)));
            }
            else{
                $s = Lib\exception::to_html($e);
                echo $s;
                exit;
            }
        } else {

            header('HTTP/1.1 404 Not Found'); 
            header('status: 404 Not Found');
            $s = $e->getMessage();
            include C("error_404");
            
        }
        
    }
    public static function hy_error($Error_Type, $Error_str,$Error_file, $Error_line,$errcontext){
        


        if(isset($_SERVER['ob_start']) && DEBUG){
            unset($_SERVER['ob_start']);
            ob_end_clean();
        }
        

        
        //var_dump($s);
        
        if (DEBUG) {
            return self::hy_exception( new \ErrorException( $Error_str, 0, $Error_Type, $Error_file, $Error_line ) );
        } else {
            $Error_China = array(
                E_ERROR => '错误', 
                E_WARNING => '警告', 
                E_PARSE => '解析错误', 
                E_NOTICE => '注意', 
                E_CORE_ERROR => '核心错误', 
                E_CORE_WARNING => '核心警告', 
                E_COMPILE_ERROR => '编译错误', 
                E_COMPILE_WARNING => '编译警告', 
                E_USER_ERROR => '用户错误', 
                E_USER_WARNING => '用户警告', 
                E_USER_NOTICE => 'User Notice', 
                E_STRICT => 'Runtime Notice'
            );
            $s = "错误类型({$Error_Mun}) : {$Error_str}";
            $Error_Mun = isset($Error_China[$Error_Type]) ? $Error_China[$Error_Type] : '未知';
            $translate = include LIB_PATH . 'translate.php';
            foreach ($translate as $key => $value) {
                $s = str_replace($key,$value,$s);
            }
            $log = New \HY\Lib\Logs;
            $log->log($s.' #错误来自于:'.$Error_file.' #行数:'.$Error_line."\r\n\r\n");
        }


        return 0;
    }
    public static function autoload($class){

        if (isset(self::$_CLASS[$class])) {//加载过 
            //echo $class."\r\n";
            return;
        }
        $className = ltrim($class, '\\');  
        $fileName  = '';  
        $namespace = '';  
        if ($lastNsPos = strrpos($className, '\\')) {  
            $namespace = substr($className, 0, $lastNsPos);  
            $className = substr($className, $lastNsPos + 1);  
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;  
        }
        $fileName .= $className .'.php';
        

        
        if (!is_file(PATH . $fileName)) { //自动加载路劲不存在 启用映射搜索
            $vendor_bool = false;
            foreach (C('vendor') as $v) {
                $vendor_path = ltrim($v,'\\/') . DIRECTORY_SEPARATOR . $fileName;
                //echo PATH . $vendor_path."\r\n";
                if(is_file(PATH . $vendor_path)){
                    $fileName = $vendor_path;
                    $vendor_bool=true;
                    break;
                }
            }
            if(!$vendor_bool){
                //E('类库不存在 : ' . $class . ' 加载路径:'.$fileName);
                return false;
            }
                
        }
        $fileName = PATH . $fileName;

        //$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';  
        //echo $fileName  ."<br>\r\n";
        //echo PATH .'  '. $fileName.' | ' .$class ."<br>\r\n";
      	$info = explode('\\', $class);
        $agrs =count($info);
        if ($info[0] == 'Model') {
            $file = $fileName;
            Lib\hook::$include_file[]=$file;
            if (PLUGIN_ON) {
                //插件机制
                $file1 = TMP_PATH . $info[1] . '_' . MD5('Model/' . $info[1]) . C("tmp_file_suffix");
                if (!is_file($file1) || DEBUG) {
                    // 临时Action不存在
                    if (!is_file($file)) {
                        throw new \Exception('控制器 ' . $class . ' 不存在!');
                    }
                    $code = file_get_contents($file);
                    $code = Lib\hook::re($code,$file);
                    if(PLUGIN_MORE_LANG_ON){
                        static $more_lang_lib = null;
                        if($more_lang_lib == null){
                            $more_lang_lib = new Lib\more_lang_lib;
                        }
                        $code = $more_lang_lib->decode($code);
                    }
                    Lib\hook::put(Lib\hook::encode($code), $file1);
                }
                $fileName = $file1;
            }
        } elseif ($info[0] == 'Action') {
            $file = $fileName;
            Lib\hook::$include_file[]=$file;
            if (PLUGIN_ON) {

                //插件机制
                $file1 = TMP_PATH . $info[1] . '_' . MD5('Action/' . $info[1]) . C("tmp_file_suffix");
                if (!is_file($file1) || DEBUG) {
                    // 临时Action不存在
                    if (!is_file($file)) {
                        throw new \Exception('控制器 ' . $class . ' 不存在!');
                    }
                    $code = file_get_contents($file);
                    $code = Lib\hook::re($code,$file);
                    if(PLUGIN_MORE_LANG_ON){

                        static $more_lang_lib = null;
                        if($more_lang_lib == null){
                            $more_lang_lib = new Lib\more_lang_lib;
                        }
                        $code = $more_lang_lib->decode($code);

                    }
                    Lib\hook::put(Lib\hook::encode($code), $file1);
                }

                $fileName = $file1;


            }
        }

        if (empty($fileName)) {
            return false;
        }
       
        include_once $fileName;
        self::$_CLASS[$class] = true;
        return $fileName;
    }
    public static function SQL_LOG($log){
        array_push(self::$logs, $log);
    }
}


HY::start();
