<?php
/**
 * 常用类及函数
 * @author wuwenbin <wenbin.wu@foxmail.com>
 * @license MIT
 * @version 1.0.8
 */

if (!class_exists("App")) {
    /**
     * 应用管理
     */
    class App
    {
        const LEVEL_DEV = "dev";
        const LEVEL_TEST = "test";
        const LEVEL_PROD = "prod";

        /**
         * @var string $name 应用名称
         */
        protected static $name = "default";

        /**
         * @var string $rootPath 应用根目录
         */
        protected static $rootPath = "";

        /**
         * @var string $level 执行级别
         */
        protected static $level = "";

        /**
         * 执行应用
         *
         * @param string $name 应用名称
         * @param string $rootPath 应用根目录
         * @param string $level 执行级别
         */
        public static function run($name, $rootPath, $level = "")
        {
            self::$name = $name;
            self::$rootPath = $rootPath;
            switch ($level) {
                case self::LEVEL_PROD:
                    self::$level = self::LEVEL_PROD;
                    error_reporting(0);
                    ini_set("display_errors", 0);
                    break;
                case self::LEVEL_TEST:
                    self::$level = self::LEVEL_TEST;
                    error_reporting(E_ALL);
                    ini_set("display_errors", 1);
                    break;
                default:
                    self::$level = self::LEVEL_DEV;
                    error_reporting(E_ALL);
                    ini_set("display_errors", 1);
            }

            defined("START_TIME") || define("START_TIME", microtime());
            date_default_timezone_set("Asia/Shanghai");
            Router::adjustRequest();
            Config::setMulti(self::getConfigs());
            Mysql::setMulti(self::getMysqlConfigs());
            Template::setPath(self::getTemplatePath());
            Template::setCompilePath(self::getTemplateCompilePath());
            Template::forceCompile(self::$level == self::LEVEL_PROD ? false : true);
            Log::setPath(self::getLogPath());
            Cache::setPath(self::getCachePath());
            $baseUrl = Config::get("baseUrl");
            if (!is_null($baseUrl)) {
                Router::setBaseUrl($baseUrl);
            }
            $rules = Config::get("rewriteRules");
            if (is_array($rules)) {
                Router::setRules($rules);
            }
            Router::dispatch(self::getControllerPath(), G("do", ""));
        }

        /**
         * 获取应用名称
         *
         * @return string
         */
        public static function getName()
        {
            return self::$name;
        }

        /**
         * 获取应用根目录
         *
         * @return string
         */
        public static function getRootPath()
        {
            return self::$rootPath;
        }

        /**
         * 获取执行级别
         *
         * @return string
         */
        public static function getLevel()
        {
            return self::$level;
        }

        /**
         * 获取配置信息
         *
         * @return array
         */
        public static function getConfigs()
        {
            $configFiles = array();
            $configFiles[] = self::$rootPath . "/config/base.php";
            $configFiles[] = self::$rootPath . "/config/base_" . self::$level . ".php";
            $configFiles[] = self::$rootPath . "/config/" . self::$name . ".php";
            $configFiles[] = self::$rootPath . "/config/" . self::$name . "_" . self::$level . ".php";

            $configs = array();
            foreach ($configFiles as $configFile) {
                if (is_file($configFile)) {
                    arrayMergeDeep($configs, include ($configFile));
                }
            }

            return $configs;
        }

        /**
         * 获取控制器目录
         *
         * @return string
         */
        public static function getControllerPath()
        {
            $controllerPath = Config::get("controllerPath");
            if (is_null($controllerPath)) {
                return self::$rootPath . "/controller/" . self::$name;
            } else {
                return $controllerPath;
            }
        }

        /**
         * 获取数据库配置
         *
         * @return array
         */
        public static function getMysqlConfigs()
        {
            $db = Config::get("db");
            return is_array($db) ? $db : array();
        }

        /**
         * 获取模板目录
         *
         * @return string
         */
        public static function getTemplatePath()
        {
            $templatePath = Config::get("templatePath");
            if (is_null($templatePath)) {
                return self::$rootPath . "/template/" . self::$name;
            } else {
                return $templatePath;
            }
        }

        /**
         * 获取模板编译目录
         *
         * @return string
         */
        public static function getTemplateCompilePath()
        {
            $templateCompilePath = Config::get("templateCompilePath");
            if (is_null($templateCompilePath)) {
                return self::$rootPath . "/data/tpl_compile/" . self::$name;
            } else {
                return $templateCompilePath;
            }
        }

        /**
         * 获取文件日志目录
         *
         * @return string
         */
        public static function getLogPath()
        {
            $logPath = Config::get("logPath");
            if (is_null($logPath)) {
                return self::$rootPath . "/data/log";
            } else {
                return $logPath;
            }
        }

        /**
         * 获取文件缓存目录
         *
         * @return string
         */
        public static function getCachePath()
        {
            $cachePath = Config::get("cachePath");
            if (is_null($cachePath)) {
                return self::$rootPath . "/data/cache";
            } else {
                return $cachePath;
            }
        }
    }
}

if (!class_exists("Router")) {
    /**
     * 路由器
     */
    class Router
    {
        const ERROR_CONTROLLER_FILE_NOT_FOUND = "控制器文件不存在";
        const ERROR_CONTROLLER_NOT_FOUND = "控制器类不存在";
        const ERROR_ACTION_NOT_FOUND = "控制器方法不存在";

        /**
         * @var array $rules URL构造规则
         */
        protected static $rules = array();

        /**
         * @var string $baseUrl 用于URL构造的根路径
         */
        protected static $baseUrl = "";

        /**
         * @var callable $dispatchErrorCallback 路由分发出错处理方法
         */
        protected static $dispatchErrorCallback = null;

        /**
         * 路由分发
         *
         * @param string $controllerPath 控制器目录
         * @param string $do 执行操作
         *
         */
        public static function dispatch($controllerPath, $do)
        {
            $do = trim($do);
            if (strlen($do) == 0) {
                $controller = "Index";
                $action = "Index";
            } elseif (strpos($do, "_") === false) {
                $controller = "Index";
                $action = $do;
            } else {
                list($controller, $action) = explode("_", $do);
            }

            $classFile = "$controllerPath/$controller.php";
            $class = "Controller$controller";
            $method = "action$action";
            if (!is_file($classFile)) {
                self::dispatchError($controller, $action, self::ERROR_CONTROLLER_FILE_NOT_FOUND);
            }
            include_once $classFile;
            if (!class_exists($class)) {
                self::dispatchError($controller, $action, self::ERROR_CONTROLLER_NOT_FOUND);
            }
            if (!in_array($method, get_class_methods($class))) {
                self::dispatchError($controller, $action, self::ERROR_ACTION_NOT_FOUND);
            }

            call_user_func(array(new $class(), $method));
        }

        /**
         * 设置路由分发错误处理方法
         *
         * @param callable $callback 处理方法
         */
        public static function setDispatchErrorCallback($callback)
        {
            self::$dispatchErrorCallback = $callback;
        }

        /**
         * 路由分发错误处理
         *
         * @param string $controller 控制器
         * @param string $action 操作
         * @param string $error 错误信息
         *
         */
        protected static function dispatchError($controller, $action, $error)
        {
            if (is_callable(self::$dispatchErrorCallback)) {
                call_user_func(self::$dispatchErrorCallback, $controller, $action, $error);
                exit;
            } else {
                exitUtf8("[$controller/$action] $error\n");
            }
        }

        /**
         * 构造控制器访问URL
         *
         * @param string $do 执行操作
         * @param array $params 参数列表
         * @param mixed $baseUrl 根URL
         * @param int $id 规则ID
         *
         * @return string
         */
        public static function buildUrl($do, array $params = array(), $baseUrl = null, $id = 0)
        {
            if (isset(self::$rules[$do])) {
                $rules = self::$rules[$do];
                if (!is_array($rules)) {
                    $rules = array($rules);
                }
                if (!isset($rules[$id])) {
                    $id = 0;
                }
                $rule = isset($rules[$id]) ? $rules[$id] : "";

                preg_match_all("/\([^\)]+\)|[^\(\)]+/", $rule, $matches);
                $parts = $matches[0];
                foreach ($parts as $i => $part) {
                    $must = ($part[0] == "(") ? false : true;
                    preg_match_all("/\[(\w+)\]|[^\[\]]+/", $must ? $part : substr($part, 1, -1), $matches);
                    $subParts = $matches[0];
                    $finished = false;
                    foreach ($subParts as $j => $subPart) {
                        if ($subPart[0] != "[") {
                            continue;
                        }
                        $key = substr($subPart, 1, -1);
                        if (isset($params[$key])) {
                            $subPart = $params[$key];
                            unset($params[$key]);
                        } elseif (!$must) {
                            $part = "";
                            $finished = true;
                            break;
                        }
                        $subParts[$j] = $subPart;
                    }
                    if (!$finished) {
                        $part = implode("", $subParts);
                    }

                    $parts[$i] = $part;
                }

                $queries = $params;
                $url = implode("", $parts);
            } else {
                $queries = array_merge(array("do" => $do), $params);
                $url = is_null($baseUrl) ? self::$baseUrl : $baseUrl;
            }

            if (empty($queries)) {
                return $url;
            }

            $queryStr = "";
            foreach ($queries as $k => $v) {
                $queryStr .= "$k=$v&";
            }
            $queryStr = substr($queryStr, 0, -1);
            $url .= (strpos($url, "?") === false) ? "?" : "&";
            $url .= $queryStr;

            return $url;
        }

        /**
         * 设置URL构造规则
         *
         * @param array $rules 规则
         */
        public static function setRules(array $rules)
        {
            self::$rules = $rules;
        }

        /**
         * 设置根URL
         *
         * @param string $url 根URL
         */
        public static function setBaseUrl($url)
        {
            self::$baseUrl = $url;
        }

        /**
         * 校正请求数据
         */
        public static function adjustRequest()
        {
            if (version_compare(PHP_VERSION, '5.4.0') < 0 && get_magic_quotes_gpc()) {
                $_GET = stripslashesDeep($_GET);
                $_POST = stripslashesDeep($_POST);
                $_COOKIE = stripslashesDeep($_COOKIE);
            }
            $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);
        }
    }
}

if (!class_exists("Config")) {
    /**
     * 全局配置
     */
    class Config
    {
        /**
         * @var array $data 配置信息
         */
        protected static $data = array();

        /**
         * 设置配置
         *
         * @param string $key 配置名称
         * @param mixed $value 配置内容
         */
        public static function set($key, $value)
        {
            $key = strval($key);
            self::$data[$key] = $value;
        }

        /**
         * 批量设置配置
         *
         * @param array $data 配置列表
         */
        public static function setMulti(array $data)
        {
            foreach ($data as $key => $value) {
                self::set($key, $value);
            }
        }

        /**
         * 获取配置内容
         *
         * @param string $key 配置名称
         * @param string $key2 配置名称2
         * @param string $key3 配置名称3
         *
         * @return mixed
         */
        public static function get($key, $key2 = null, $key3 = null)
        {
            if (!isset(self::$data[$key])) {
                return null;
            }

            if (is_null($key2)) {
                return self::$data[$key];
            }

            if (!is_array(self::$data[$key]) || !isset(self::$data[$key][$key2])) {
                return null;
            }

            if (is_null($key3)) {
                return self::$data[$key][$key2];
            }

            if (!is_array(self::$data[$key][$key2]) || !isset(self::$data[$key][$key2][$key3])) {
                return null;
            }

            return self::$data[$key][$key2][$key3];
        }
    }
}

if (!class_exists("Mysql")) {
    /**
     * MySQL数据库操作
     */
    class Mysql
    {
        const ERROR_CONFIG_NOT_FOUND = "无配置信息";
        const ERROR_CONNECT_FAILED = "数据库连接失败";

        /**
         * @var array $configs 配置信息
         */
        protected static $configs = array();

        /**
         * @var array $instances 数据库连接资源
         */
        protected static $instances = array();

        /**
         * @var string $error 错误信息
         */
        protected static $error = "";

        /**
         * @var string $sql 查询SQL语句
         */
        protected static $sql = "";

        /**
         * @var string $startTime 查询执行开始时间
         */
        protected static $startTime = "";

        /**
         * @var string $endTime 查询执行结束时间
         */
        protected static $endTime = "";

        /**
         * @var callable $queryCallback 查询成功回调方法
         */
        protected static $queryCallback = "";

        /**
         * 设置数据库配置
         *
         * @param array $config 配置信息
         * @param string $id 数据库ID
         */
        public static function set(array $config, $id = "default")
        {
            $_config = array(
                "host" => "localhost",
                "port" => 3306,
                "user" => "",
                "pass" => "",
                "name" => "",
                "charset" => "utf8",
            );

            foreach ($_config as $k => $v) {
                if (isset($config[$k])) {
                    $_config[$k] = $config[$k];
                }
            }
            self::$configs[strval($id)] = $_config;
        }

        /**
         * 批量设置数据库配置
         *
         * @param array $configs 配置列表
         */
        public static function setMulti(array $configs)
        {
            foreach ($configs as $id => $config) {
                self::set($config, $id);
            }
        }

        /**
         * 关闭数据库
         *
         * @param string $id 数据库ID
         */
        public static function close($id)
        {
            if (!isset(self::$instances[$id])) {
                return;
            }

            mysqli_close(self::$instances[$id]);
            unset(self::$instances[$id]);
        }

        /**
         * 关闭所有数据库
         */
        public static function closeAll()
        {
            foreach (array_keys(self::$instances) as $id) {
                self::close($id);
            }
        }

        /**
         * 执行SQL语句
         *
         * @param string $sql SQL语句
         * @param string $id 数据库ID
         *
         * @return mixed
         */
        public static function query($sql, $id = "default")
        {
            self::$error = "";
            self::$startTime = "";
            self::$endTime = "";
            self::$sql = $sql;

            if (!isset(self::$instances[$id])) {
                if (!isset(self::$configs[$id])) {
                    self::$error = self::ERROR_CONFIG_NOT_FOUND;
                    return false;
                }

                $host = self::$configs[$id]["host"];
                $port = self::$configs[$id]["port"];
                $user = self::$configs[$id]["user"];
                $pass = self::$configs[$id]["pass"];
                $name = self::$configs[$id]["name"];
                $db = @mysqli_connect($host, $user, $pass, $name, $port);
                if (mysqli_connect_errno()) {
                    self::$error = self::ERROR_CONNECT_FAILED;
                    return false;
                }

                $charset = self::$configs[$id]["charset"];
                @mysqli_query($db, "set names $charset");
            } else {
                $db = self::$instances[$id];
            }

            self::$startTime = microtime();
            $result = @mysqli_query($db, $sql);
            self::$endTime = microtime();

            if (!$result) {
                self::$error = @mysqli_error($db) . "(" . @mysqli_errno($db) . ")";
                return false;
            }

            if (self::$queryCallback && is_callable(self::$queryCallback)) {
                call_user_func(self::$queryCallback, $id);
            }

            if (preg_match("/^insert/i", $sql)) {
                return @mysqli_insert_id($db);
            }

            if (preg_match("/^(update|delete)/i", $sql)) {
                return @mysqli_affected_rows($db);
            }

            if (preg_match("/^select/i", $sql)) {
                $rows = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }
                mysqli_free_result($result);
                return $rows;
            }

            return $result;
        }

        /**
         * 设置查询成功回调方法
         *
         * @param callable $callback 回调方法
         */
        public static function setQueryCallback($callback)
        {
            self::$queryCallback = $callback;
        }

        /**
         * 获取错误信息
         *
         * @return string
         */
        public static function getError()
        {
            return self::$error;
        }

        /**
         * 获取执行时间
         *
         * @return float
         */
        public static function getExecTime()
        {
            if (self::$startTime == "" || self::$endTime == "") {
                return 0;
            }

            $startTime = explode(" ", self::$startTime);
            $endTime = explode(" ", self::$endTime);
            return round($endTime[0] + $endTime[1] - $startTime[0] - $startTime[1], 4);
        }

        /**
         * 获取执行SQL语句
         *
         * @return string
         */
        public static function getSql()
        {
            return self::$sql;
        }
    }
}

if (!class_exists("Template")) {
    /**
     * 模板引擎
     *
     * 修改自initphp(http://initphp.com/)
     */
    class Template
    {
        const ERROR_FILE_NOE_FOUND = "模板文件不存在";
        const ERROR_COMPILE_FAILED = "模板编译失败";

        /**
         * @var string $path 模板目录
         */
        protected static $path = "";

        /**
         * @var string $compilePath 模板编译目录
         */
        protected static $compilePath = "";

        /**
         * @var bool $forceCompile 是否强制编译
         */
        protected static $forceCompile = true;

        /**
         * @var array $tag 模板定界符
         */
        protected static $tag = array("<!--{", "}-->");

        /**
         * @var string $error 错误信息
         */
        protected static $error = "";

        /**
         * @var string $file 模板文件
         */
        protected static $file = "";

        /**
         * @var string $compileFile 模板编译文件
         */
        protected static $compileFile = "";

        /**
         * @var array $data 模板中可直接使用的数据
         */
        protected static $data = array();

        /**
         * @var bool $return 是否返回输出内容
         */
        protected static $return = false;

        /**
         * 输出模板
         *
         * @param string $tpl 模板名称
         * @param array $data 模板中可直接使用的数据
         * @param bool $return 是否返回输出内容
         *
         * @return bool
         */
        public static function display($tpl, array $data = array(), $return = false)
        {
            defined("__TPL__") || define("__TPL__", true);
            self::$file = self::getFile($tpl);
            self::$compileFile = self::getCompileFile($tpl);
            self::$data = $data;
            self::$return = $return;

            if (!is_file(self::$file)) {
                self::$error = self::ERROR_FILE_NOE_FOUND;
                return false;
            }

            if (
                self::$forceCompile ||
                !is_file(self::$compileFile) ||
                filemtime(self::$compileFile) < filemtime(self::$file)
            ) {
                self::compile(self::$file, self::$compileFile);
            }

            if (!is_file(self::$compileFile)) {
                self::$error = self::ERROR_COMPILE_FAILED;
                return false;
            }

            foreach (self::$data as $__K__ => $__V__) {
                $$__K__ = $__V__;
            }

            if (self::$return) {
                ob_start();
                include self::$compileFile;
                return ob_get_clean();
            } else {
                include self::$compileFile;
            }

            return true;
        }

        /**
         * 编译模板
         *
         * @param string $file 模板文件
         * @param string $compileFile 模板编译文件
         */
        protected static function compile($file, $compileFile)
        {
            $tag_l = self::$tag[0];
            $tag_r = self::$tag[1];
            $php_s = "<?php";
            $php_e = "?" . ">";

            $template = strval(@file_get_contents($file));
            preg_match_all("/(" . $tag_l . "layout:)(.+)(" . $tag_r . ")/U", $template, $matches);
            foreach ($matches[2] as $v) {
                self::compile(self::getFile($v), self::getCompileFile($v));
            }

            foreach ($matches[0] as $k => $v) {
                $replace = "{$php_s} include('" . self::getCompileFile($matches[2][$k]) . "'); {$php_e}";
                $template = str_replace($v, $replace, $template);
            }

            // if
            $template = preg_replace("/{$tag_l}if([^{]+?){$tag_r}/", "{$php_s} if \\1 { {$php_e}", $template);
            $template = preg_replace("/{$tag_l}else{$tag_r}/", "{$php_s} } else { {$php_e}", $template);
            $template = preg_replace("/{$tag_l}elseif([^{]+?){$tag_r}/", "{$php_s} } elseif \\1 { {$php_e}", $template);
            $template = preg_replace("/{$tag_l}\/if{$tag_r}/", "{$php_s} } {$php_e}", $template);
            // foreach
            $template = preg_replace("/{$tag_l}foreach([^{]+?){$tag_r}/", "{$php_s} foreach \\1 { {$php_e}", $template);
            $template = preg_replace("/{$tag_l}\/foreach{$tag_r}/", "{$php_s} } {$php_e}", $template);
            // for
            $template = preg_replace("/{$tag_l}for([^{]+?){$tag_r}/", "{$php_s} for \\1 { {$php_e}", $template);
            $template = preg_replace("/{$tag_l}\/for{$tag_r}/", "{$php_s} } {$php_e}", $template);
            // var
            $template = preg_replace("/{$tag_l}(\\\$[^{]+?){$tag_r}/", "{$php_s} echo \\1; {$php_e}", $template);
            // const
            $template = preg_replace("/{$tag_l}(\w+){$tag_r}/", "{$php_s} echo \\1; {$php_e}", $template);
            // function
            $template = preg_replace("/{$tag_l}:(\w+[^{]+?){$tag_r}/", "{$php_s} echo \\1; {$php_e}", $template);
            // php
            $template = preg_replace(array("/{$tag_l}/", "/{$tag_r}/"), array("{$php_s} ", " {$php_e}"), $template);
            // head
            $template = "{$php_s} if(!defined('__TPL__')) {exit('Access Denied!');} {$php_e}" . $template;

            $compileFileDir = dirname($compileFile);
            if (!is_dir($compileFileDir)) {
                mkdir($compileFileDir, 0777, true);
            }

            file_put_contents($compileFile, $template);
        }

        /**
         * 设置模板目录
         *
         * @param string $path 模板目录
         */
        public static function setPath($path)
        {
            self::$path = rtrim($path, "/\\") . "/";
        }

        /**
         * 设置模板编译目录
         *
         * @param string $compilePath 模板编译目录
         */
        public static function setCompilePath($compilePath)
        {
            self::$compilePath = rtrim($compilePath, "/\\") . "/";
        }

        /**
         * 设置是否强制编译
         *
         * @param bool $force 是否强制编译
         */
        public static function forceCompile($force)
        {
            self::$forceCompile = $force ? true : false;
        }

        /**
         * 获取模板文件
         *
         * @param string $tpl 模板名称
         *
         * @return string
         */
        public static function getFile($tpl)
        {
            return self::$path . "/" . $tpl . ".html";
        }

        /**
         * 获取模板编译文件
         *
         * @param string $tpl 模板名称
         *
         * @return string
         */
        public static function getCompileFile($tpl)
        {
            return self::$compilePath . "/" . $tpl . ".tpl.php";
        }

        /**
         * 获取错误信息
         *
         * @return string
         */
        public static function getError()
        {
            return $error;
        }
    }
}

if (!class_exists("Log")) {
    /**
     * 文件日志
     */
    class Log
    {
        const MODE_DAY = 1;
        const MODE_MONTH = 2;
        const MODE_YEAR = 3;

        /**
         * @var string $path 日志目录
         */
        protected static $path = "";

        /**
         * @var string $format 日志格式
         */
        protected static $format = "[{time}] {data}\n";

        /**
         * 保存日志
         *
         * @param string $name 日志名称
         * @param mixed $data 日志内容
         * @param int $mode 记录模式
         */
        public static function save($name, $data, $mode = null)
        {
            $file = self::getFile($name, $mode);
            $fileDir = dirname($file);
            if (!is_dir($fileDir)) {
                mkdir($fileDir, 0777, true);
            }

            $time = date("Y-m-d H:i:s", time());
            if (is_array($data)) {
                $data = print_r($data, true);
            } else {
                $data = strval($data);
            }

            $record = str_replace(array("{time}", "{data}"), array($time, $data), self::$format);
            if (!is_file($file)) {
                file_put_contents($file, "<?php exit; ?" . ">\n");
            }

            file_put_contents($file, $record, FILE_APPEND);
        }

        /**
         * 获取日志文件
         *
         * @param string $name 日志名称
         * @param int $mode 记录模式
         *
         * @return string
         */
        public static function getFile($name, $mode)
        {
            $file = self::$path . $name;
            switch ($mode) {
                case self::MODE_DAY:
                    $file .= date("_Y-m-d", time());
                    break;
                case self::MODE_MONTH:
                    $file .= date("_Y-m", time());
                    break;
                case self::MODE_YEAR:
                    $file .= date("_Y", time());
                    break;
                default:
            }
            $file .= ".log.php";
            return $file;
        }

        /**
         * 设置日志目录
         *
         * @param string $path 日志目录
         */
        public static function setPath($path)
        {
            self::$path = rtrim($path, "/\\") . "/";
        }
    }
}

if (!class_exists("Cache")) {
    /**
     * 文件缓存
     */
    class Cache
    {
        /**
         * @var string $path 缓存目录
         */
        protected static $path = "";

        /**
         * @var array $caches 缓存数据
         */
        protected static $caches = array();

        /**
         * 保存数据
         *
         * @param string $name 索引名称
         * @param mixed $value 数据
         */
        public static function set($name, $value)
        {
            $file = self::getFile($name);
            $fileDir = dirname($file);
            if (!is_dir($fileDir)) {
                mkdir($fileDir, 0777, true);
            }

            $data = "<?php exit; ?" . ">" . serialize($value);
            if (file_put_contents($file, $data)) {
                self::$caches[$name] = $value;
            }
        }

        /**
         * 读取数据
         *
         * @param string $name 索引名称
         *
         * @return mixed
         */
        public static function get($name)
        {
            if (isset(self::$caches[$name])) {
                return self::$caches[$name];
            }

            $file = self::getFile($name);
            if (!is_file($file)) {
                return null;
            }

            return unserialize(substr(file_get_contents($file), strlen("<?php exit; ?" . ">")));
        }

        /**
         * 删除数据
         * @param string $name 索引名称
         */
        public static function del($name)
        {
            $file = self::getFile($name);
            if (is_file($file)) {
                unlink($file);
            }
            if (isset(self::$caches[$name])) {
                unset(self::$caches[$name]);
            }
        }

        /**
         * 设置缓存目录
         *
         * @param string $path 缓存目录
         */
        public static function setPath($path)
        {
            self::$path = rtrim($path, "/\\") . "/";
        }

        /**
         * 获取缓存文件
         *
         * @param string $name 索引名称
         *
         * @return string
         */
        public static function getFile($name)
        {
            return self::$path . $name . ".cache.php";
        }

        /**
         * 获取缓存时间
         *
         * @param string $name 索引名称
         *
         * @return int
         */
        public static function getCachedTime($name)
        {
            $file = self::getFile($name);
            if (!is_file($file)) {
                return 0;
            }

            return time() - filemtime($file);
        }
    }
}

if (!class_exists("Ftp")) {
    /**
     * FTP服务器操作
     */
    class Ftp
    {
        const ERROR_CONFIG_NOT_FOUND = "无配置信息";
        const ERROR_CONNECT_FAILED = "FTP服务器连接失败";
        const ERROR_LOGIN_FAILED = "FTP服务器登录失败";
        const ERROR_LOCAL_FILE_NOT_FOUND = "本地文件不存在";
        const ERROR_REMOTE_FILE_NOT_FOUND = "远程文件不存在";
        const ERROR_FILE_NAME_NOT_EMPTY = "文件名不能为空";
        const ERROR_CREATE_DIR_FAILED = "创建目录失败";
        const ERROR_UPLOAD_FAILED = "文件上传失败";
        const ERROR_DOWNLOAD_FAILED = "文件下载失败";

        /**
         * @var array $configs 配置信息
         */
        protected static $configs = array();

        /**
         * @var array $instances FTP服务器连接资源
         */
        protected static $instances = array();

        /**
         * @var string $error 错误信息
         */
        protected static $error = "";

        /**
         * 设置FTP服务器配置
         *
         * @param array $config 配置信息
         * @param string $id FTP服务器ID
         */
        public static function set(array $config, $id = "default")
        {
            $_config = array(
                "host" => "localhost",
                "port" => 21,
                "user" => "anonymous",
                "pass" => "",
                "timeout" => 10,
            );
            foreach ($_config as $k => $v) {
                if (isset($config[$k])) {
                    $_config[$k] = $config[$k];
                }
            }
            self::$configs[$id] = $_config;
        }

        /**
         * 批量设置FTP服务器配置
         *
         * @param array $configs 配置信息
         */
        public static function setMulti(array $configs)
        {
            foreach ($configs as $id => $config) {
                self::set($id, $config);
            }
        }

        /**
         * 关闭FTP服务器连接
         *
         * @param string $id FTP服务器ID
         */
        public static function close($id = "default")
        {
            if (isset(self::$instances[$id])) {
                @ftp_close(self::$instances[$id]);
                unset(self::$instances[$id]);
            }
        }

        /**
         * 关闭所有FTP服务器连接
         */
        public static function closeAll()
        {
            foreach (array_keys(self::$instances[$id]) as $id) {
                self::close($id);
            }
        }

        /**
         * 获取FTP服务器连接资源
         *
         * @param string $id FTP服务器ID
         *
         * @return resource
         */
        public static function getInstance($id = "default")
        {
            self::$error = "";

            if (isset(self::$instances[$id])) {
                return self::$instances[$id];
            }

            if (!isset(self::$configs[$id])) {
                self::$error = self::ERROR_CONFIG_NOT_FOUND;
                return false;
            }

            $host = self::$configs[$id]["host"];
            $port = self::$configs[$id]["port"];
            $timeout = self::$configs[$id]["timeout"];
            $ftp = ftp_connect($host, $port, $timeout);
            if (!$ftp) {
                self::$error = self::ERROR_CONNECT_FAILED;
                return false;
            }

            $user = self::$configs[$id]["user"];
            $pass = self::$configs[$id]["pass"];
            if (!ftp_login($ftp, $user, $pass)) {
                self::$error = self::ERROR_LOGIN_FAILED;
                return false;
            }

            self::$instances[$id] = $ftp;
            return $ftp;
        }

        /**
         * 上传文件
         *
         * @param string $localFile 本地文件
         * @param string $remoteFile FTP服务器文件
         * @param string $id FTP服务器ID
         *
         * @return bool
         */
        public static function put($localFile, $remoteFile, $id = "default")
        {
            self::$error = "";
            if (!is_file($localFile)) {
                self::$error = self::ERROR_LOCAL_FILE_NOT_FOUND;
                return false;
            }

            $remoteFile = trim($remoteFile);
            if (strlen($remoteFile) == 0) {
                self::$error = self::ERROR_FILE_NAME_NOT_EMPTY;
                return false;
            }

            $ftp = self::getInstance($id);
            if (!$ftp) {
                return false;
            }

            @ftp_chdir($ftp, "/");
            $dirs = explode("/", trim(dirname($remoteFile), "/"));
            foreach ($dirs as $dir) {
                $dir = trim($dir);
                if (strlen($dir) == 0) {
                    continue;
                }
                @ftp_mkdir($ftp, $dir);
                if (!@ftp_chdir($ftp, $dir)) {
                    self::$error = self::ERROR_CREATE_DIR_FAILED;
                    return false;
                }
            }
            @ftp_chdir($ftp, "/");
            if (!@ftp_put($ftp, $remoteFile, $localFile, FTP_BINARY)) {
                self::$error = self::ERROR_UPLOAD_FAILED;
                return false;
            }

            return true;
        }

        /**
         * 删除FTP服务器文件
         *
         * @param string $remoteFile FTP服务器文件
         * @param string $id FTP服务器ID
         *
         * @return bool
         */
        public static function del($remoteFile, $id = "default")
        {
            $ftp = self::getInstance($id);
            if (!$ftp) {
                return false;
            }

            @ftp_chdir($ftp, "/");
            return ftp_delete($ftp, $remoteFile);
        }

        /**
         * 下载文件
         *
         * @param string $remoteFile FTP服务器文件
         * @param string $localFile 本地文件
         * @param string $id FTP服务器ID
         *
         * @return bool
         */

        public static function get($remoteFile, $localFile, $id = "default")
        {
            self::$error = "";
            $localFile = trim($localFile);
            if (strlen($localFile) == 0) {
                return self::ERROR_FILE_NAME_NOT_EMPTY;
                return false;
            }

            $ftp = self::getInstance($id);
            if (!$ftp) {
                return false;
            }

            @ftp_chdir($ftp, "/");
            $localFileDir = dirname($localFile);
            if (!is_dir($localFileDir)) {
                mkdir($localFileDir, 0777, true);
            }

            return ftp_get($ftp, $localFile, $remoteFile, FTP_BINARY);

        }

        /**
         * 获取错误信息
         */
        public static function getError()
        {
            return self::$error;
        }
    }
}

if (!function_exists("http")) {
    /**
     * HTTP请求(CURL)
     *
     * @param string $url 请求URL
     * @param mixed $data POST数据
     * @param int $timeout 超时时间
     * @param array $options CURL参数
     * @param string $error 错误信息
     *
     * @return mixed
     */
    function http($url, $data = null, $timeout = 20, array $options = array(), &$error = null)
    {
        $curl = curl_init($url);
        if ($data !== null) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        foreach ($options as $k => $v) {
            curl_setopt($curl, $k, $v);
        }
        $res = curl_exec($curl);
        if ($res === false) {
            $error = curl_error($curl) . "(" . curl_errno($curl) . ")";
        }
        curl_close($curl);
        return $res;
    }
}

if (!function_exists("getExecTime")) {
    /**
     * 获取脚本执行时间
     *
     * @return float
     */
    function getExecTime()
    {
        if (!defined("START_TIME")) {
            return 0;
        }

        $startTime = explode(" ", START_TIME);
        $endTime = explode(" ", microtime());
        return round($endTime[0] + $endTime[1] - $startTime[0] - $startTime[1], 4);
    }
}

if (!function_exists("logMysqlQuery")) {
    /**
     * 记录MySQL查询信息
     *
     * @param string $id 数据库ID
     */
    function logMysqlQuery($id = "default")
    {
        $sql = Mysql::getSql();
        $execTime = Mysql::getExecTime();
        $record = sprintf("%s %0.4f %s", $id, $execTime, $sql);
        Log::save("mysql_query", $record);
    }
}

if (!function_exists("buildInsertSql")) {
    /**
     * 构造插入语句
     *
     * @param string $table 表名
     * @param array $data 插入数据
     *
     * @return string
     */
    function buildInsertSql($table, array $data)
    {
        $table = "`" . str_replace(".", "`.`", $table) . "`";
        $sql = "insert into $table";
        $fields = "";
        $values = "";
        foreach ($data as $k => $v) {
            $fields .= "`$k`,";
            $values .= "'$v',";
        }
        $fields = rtrim($fields, ",");
        $values = rtrim($values, ",");
        $sql .= "($fields) values($values)";
        return $sql;
    }
}

if (!function_exists("buildReplaceSql")) {
    /**
     * 构造替换语句
     *
     * @param string $table 表名
     * @param array $data 插入数据
     *
     * @return string
     */
    function buildReplaceSql($table, array $data)
    {
        $table = "`" . str_replace(".", "`.`", $table) . "`";
        $sql = "replace into $table";
        $fields = "";
        $values = "";
        foreach ($data as $k => $v) {
            $fields .= "`$k`,";
            $values .= "'$v',";
        }
        $fields = rtrim($fields, ",");
        $values = rtrim($values, ",");
        $sql .= "($fields) values($values)";
        return $sql;
    }
}

if (!function_exists("buildUpdateSql")) {
    /**
     * 构造更新语句
     *
     * @param string $table 表名
     * @param array $data 更新数据
     * @param string $where 查询条件
     *
     * @return string
     */
    function buildUpdateSql($table, array $data, $where = "")
    {
        $table = "`" . str_replace(".", "`.`", $table) . "`";
        $sql = "update $table set ";
        foreach ($data as $k => $v) {
            if (is_array($v) && isset($v[0])) {
                $v = "$v";
            } elseif (is_numeric($v)) {
                $v = "$v";
            } else {
                $v = "'" . strval($v) . "'";
            }
            $sql .= "`$k` = $v,";
        }
        $sql = rtrim($sql, ",") . ($where ? " where $where" : "");
        return $sql;
    }
}

if (!function_exists("getCol")) {
    /**
     * 获取记录集列集合
     *
     * @param string $fields 字段名
     * @param array $data 记录集
     *
     * @return array
     */
    function getCol($fields, array $data)
    {
        $fields = explode(",", trim($fields));
        $cols = array();
        foreach ($fields as $v) {
            $cols[$v] = array();
        }

        foreach ($data as $k => $v) {
            foreach ($fields as $v1) {
                if (isset($v[$v1])) {
                    $cols[$v1][$k] = $v[$v1];
                }
            }
        }

        if (count($fields) > 1) {
            return $cols;
        } else {
            return $cols[$fields[0]];
        }
    }
}

if (!function_exists("isGet")) {
    /**
     * 判断是否为GET请求
     *
     * @return bool
     */
    function isGet()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "";
        return strtoupper($method) == "GET" ? true : false;
    }
}

if (!function_exists("isPost")) {
    /**
     * 判断是否为POST请求
     *
     * @return bool
     */
    function isPost()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "";
        return strtoupper($method) == "POST" ? true : false;
    }

}

if (!function_exists("isAjax")) {
    /**
     * 判断是否为AJAX请求
     *
     * @return bool
     */
    function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                return true;
            }

        }
        return false;
    }
}

if (!function_exists("isHttps")) {
    /**
     * 判断是否为HTTPS请求
     *
     * @return bool
     */
    function isHttps()
    {
        $https = isset($_SERVER["HTTPS"]) ? $_SERVER["HTTPS"] : "";
        if (strtolower($https) == "on") {
            return true;
        }

        $https = isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) ? $_SERVER["HTTP_X_FORWARDED_PROTO"] : "";
        if (strtolower($https) == "https") {
            return true;
        }

        return false;
    }
}

if (!function_exists("G")) {
    /**
     * 获取GET请求数据
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    function G($key, $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
}

if (!function_exists("P")) {
    /**
     * 获取POST请求数据
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    function P($key, $default = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
}

if (!function_exists("C")) {
    /**
     * 获取COOKIE数据
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    function C($key, $default = null)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }
}

if (!function_exists("R")) {
    /**
     * 获取REQUEST数据
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    function R($key, $default = null)
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }
}

if (!function_exists("F")) {
    /**
     * 获取上传文件数据
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    function F($key, $default = null)
    {
        return isset($_FILES[$key]) ? $_FILES[$key] : $default;
    }
}

if (!function_exists("getCurrentUrl")) {
    /**
     * 获取当前网址
     *
     * @return string
     */
    function getCurrentUrl()
    {
        return "http" . (isHttps() ? "s" : "") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }
}

if (!function_exists("getReferUrl")) {
    /**
     * 获取来路网址
     *
     * @return string
     */
    function getReferUrl()
    {
        return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
    }
}

if (!function_exists("getClientIp")) {
    /**
     * 获取客户端IP
     *
     * 修改自thinkphp(http://www.thinkphp.cn)
     *
     * @return string
     */
    function getClientIp()
    {
        static $ip = null;
        if (!is_null($ip)) {
            return $ip;
        }

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $arr = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
            $pos = array_search("unknown", $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        $ip = (false !== ip2long($ip)) ? $ip : '';
        return $ip;
    }
}

if (!function_exists("session")) {
    /**
     * SESSION操作
     *
     * @param string $key 名称
     * @param mixed $value 内容
     *
     * @return mixed
     */
    function session($key, $value = null)
    {
        isset($_SESSION) || @session_start();
        if (func_num_args() == 1) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        } elseif (is_null($value)) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        } else {
            $_SESSION[$key] = $value;
        }
    }
}

if (!function_exists("returnJson")) {
    /**
     * 返回json应答数据
     *
     * @param int $status 状态
     * @param string $msg 提示信息
     * @param mixed $data 其他数据
     */
    function returnJson($status, $msg = '', $data = null)
    {
        $res["status"] = intval($status);
        $res["msg"] = strval($msg);
        $res["data"] = $data;
        $output = json_encode($res);
        exit($output);
    }
}

if (!function_exists("alert")) {
    /**
     * JS信息提示并跳转
     *
     * @param string $msg 提示信息
     * @param string $url 跳转地址
     */
    function alert($msg, $url)
    {
        $msg = json_encode($msg);
        $url = json_encode($url);
        $output = "<script type=\"text/javascript\">alert({$msg}); location.href = {$url};</script>";
        exit($output);
    }
}

if (!function_exists("redirect")) {
    /**
     * URL重定向
     *
     * @param string $url 跳转地址
     */
    function redirect($url)
    {
        @header('Location:' . $url);
        exit;
    }
}

if (!function_exists("exitUtf8")) {
    /**
     * 扩展退出提示
     *
     * @param string $msg 提示信息
     */
    function exitUtf8($msg)
    {
        @header("Content-Type: text/html; charset=utf-8");
        exit(strval($msg));
    }
}

if (!function_exists("H")) {
    /**
     * HTML输出过滤
     *
     * @param string $str HTML字符串
     *
     * @return string
     */
    function H($str)
    {
        return htmlspecialchars($str);
    }
}

if (!function_exists("getStrLen")) {
    /**
     * 获取字符串长度
     *
     * @param string $str 字符串
     * @param int $zhLen 中文字符长度
     *
     * @return int
     */
    function getStrLen($str, $zhLen = 0)
    {
        if ($zhLen == 0) {
            return strlen($str);
        } else {
            preg_match_all("/./us", $str, $match);
            $count = 0;
            foreach ($match[0] as $v) {
                $count += (strlen($v) == 1) ? 1 : $zhLen;
            }
            return $count;
        }
    }
}

if (!function_exists("strCut")) {
    /**
     * 字符串截取
     *
     * @param string $str 字符串
     * @param int $len 截取长度
     * @param string $ext 多余内容替换字符串
     * @param int $zhLen 中文字符长度
     *
     * @return string
     */
    function strCut($str, $len, $ext = "", $zhLen = 0)
    {
        $count = 0;
        $output = "";
        preg_match_all("/./us", $str, $match);

        foreach ($match[0] as $v) {
            $vLen = strlen($v);
            $count += ($zhLen == 0) ? $vLen : $zhLen;
            $output .= $v;
            if ($count >= $len) {
                break;
            }
        }

        if (strlen($output) < strlen($str)) {
            $output .= $ext;
        }

        return $output;
    }
}

if (!function_exists("getRandStr")) {
    /**
     * 生成随机字符串
     *
     * @param int $len 随机字符串长度
     * @param mixed $chars 指定字符集
     *
     * @return string
     */
    function getRandStr($len, $chars = null)
    {
        $default = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        if (is_null($chars)) {
            $chars = $default;
        } elseif (is_int($chars)) {
            switch ($chars) {
                case 1:
                    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    break;
                case 2:
                    $chars = "1234567890";
                    break;
                default:
                    $chars = $default;
            }
        } else {
            $chars = strval($chars);
        }

        if (strlen($chars) == 0) {
            return "";
        }

        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $key = rand(0, 1000) % strlen($chars);
            $output .= $chars[$key];
        }
        return $output;
    }
}

if (!function_exists("stripslashesDeep")) {
    /**
     * 取消特殊字符转义
     *
     * @param mixed $data 字符串
     *
     * @return mixed
     */
    function stripslashesDeep($data)
    {
        if (is_array($data)) {
            return array_map(__FUNCTION__, $data);
        } else {
            return stripslashes($data);
        }
    }
}

if (!function_exists("addslashesDeep")) {
    /**
     * 特殊字符转义
     *
     * @param mixed $data 字符串
     *
     * @return mixed
     */
    function addslashesDeep($data)
    {
        if (is_array($data)) {
            return array_map(__FUNCTION__, $data);
        } else {
            return addslashes($data);
        }
    }
}

if (!function_exists("buildSignature")) {
    /**
     * 构造数据签名
     *
     * @param array $params 参数列表
     * @param string $key 加密参数
     *
     * @return string
     */
    function buildSignature(array $params, $key)
    {
        $tmp = array();
        foreach ($params as $k => $v) {
            $tmp[] = $k . $v;
        }

        sort($tmp);
        return strtoupper(md5($key . implode("", $tmp) . $key));
    }
}

if (!function_exists("uuid")) {
    /**
     * 构造唯一ID
     *
     * @param string $prefix 指定前缀参数
     *
     * @return string
     */
    function uuid($prefix = "")
    {
        $time = md5(microtime());
        $rand1 = md5(substr($time, rand(0, 10), rand(22, 32)));
        $rand2 = md5(substr($rand1, rand(0, 10), rand(22, 32)));
        $id = strtolower(md5($prefix . uniqid($prefix) . $time . $rand1 . $rand2));
        return $id;
    }
}

if (!function_exists("arrayMergeDeep")) {
    /**
     * 数组递归合并
     *
     * @param array $arr1 数组1
     * @param array $arr2 数组2
     */
    function arrayMergeDeep(array &$arr1, array $arr2)
    {
        foreach ($arr2 as $k => $v) {
            if (!isset($arr1[$k])) {
                $arr1[$k] = $v;
            } else {
                if (!is_array($arr1[$k]) || !is_array($v)) {
                    $arr1[$k] = $v;
                } else {
                    arrayMergeDeep($arr1[$k], $v);
                }
            }
        }
    }
}

if (!function_exists("isValidEmail")) {
    /**
     * 检查邮件格式是否合法
     *
     * @param string $email 邮件地址
     *
     * @return bool
     */
    function isValidEmail($email)
    {
        return (bool) preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email);
    }
}

if (!function_exists("isValidPhoneNumber")) {
    /**
     * 检查手机号码是否合法
     *
     * 仅支持大陆11手机号
     *
     * @param string $phoneNumber 手机号码
     *
     * @return bool
     */
    function isValidPhoneNumber($phoneNumber)
    {
        return (bool) preg_match("/^(13|14|15|17|18)\d{9}$/", $phoneNumber);
    }
}

if (!function_exists("getPageUrl")) {
    /**
     * 获取用于分页的URL
     *
     * @param string $pageParam 分页参数名
     *
     * @return string
     */
    function getPageUrl($pageParam = "page")
    {
        $url = getCurrentUrl();
        if (preg_match("/{$pageParam}=(\d+)/", $url)) {
            return preg_replace("/{$pageParam}=(\d+)/", "{$pageParam}=[p]", $url);
        } else {
            return $url . (strpos($url, "?") ? "&" : "?") . "{$pageParam}=[p]";
        }
    }
}

if (!function_exists("getPageHtml")) {
    /**
     * 分页函数
     *
     * @param string $url 分页URL
     * @param int $page 当前页数
     * @param int $each 每页显示记录数
     * @param int $count 总记录数
     *
     * @return string
     */
    function getPageHtml($url, $page, $each, $count)
    {
        $page = max(intval($page), 1);
        $each = max(intval($each), 1);
        $count = intval($count);
        $totalPage = ceil($count / $each);
        $len = 6; // 宽度

        if ($count < 1) {
            return "<span>共0条记录</span>";
        }

        $html = "<strong>$page</strong>";
        $l = $r = 1;
        while (1) {
            if ($page - $l > 0) {
                $_page = $page - $l;
                $_url = str_replace("[p]", $_page, $url);
                $html = '<a href="' . $_url . '">' . $_page . '</a>' . $html;
                $l++;
                $len--;
            }

            if ($len == 0) {
                break;
            }

            if ($page + $r < $totalPage + 1) {
                $_page = $page + $r;
                $_url = str_replace("[p]", $_page, $url);
                $html .= '<a href="' . $_url . '">' . $_page . '</a>';
                $r++;
                $len--;
            }

            if ($len == 0) {
                break;
            }

            if ($page - $l <= 0 && $page + $r > $totalPage) {
                break;
            }
        }

        if ($page - 1 > 0) {
            $_page = $page - 1;
            $_url = str_replace("[p]", $_page, $url);
            $html = '<a href="' . $_url . '">上页</a>' . $html;
        } else {
            $html = '<a href="javascript:;">上页</a>' . $html;
        }

        if ($page + 1 < $totalPage + 1) {
            $_page = $page + 1;
            $_url = str_replace("[p]", $_page, $url);
            $html .= '<a href="' . $_url . '">下页</a>';
        } else {
            $html .= '<a href="javascript:;">下页</a>';
        }

        if ($page > 1) {
            $_page = 1;
            $_url = str_replace("[p]", $_page, $url);
            $html = '<a href="' . $_url . '">首页</a>' . $html;
        } else {
            $html = '<a href="javascript:;">首页</a>' . $html;
        }

        if ($page < $totalPage) {
            $_page = $totalPage;
            $_url = str_replace("[p]", $_page, $url);
            $html .= '<a href="' . $_url . '">末页</a>';
        } else {
            $html .= '<a href="javascript:;">末页</a>';
        }

        $html = "<span>共{$totalPage}页/{$count}条记录</span>" . $html;
        return $html;
    }
}

if (!function_exists("cleanDir")) {
    /**
     * 清空目录
     *
     * @param string $rootPath 根目录
     * @param bool $includeSelf 是否删除根目录
     *
     * @return array
     */
    function cleanDir($rootPath, $includeSelf = false)
    {
        $list = array();
        $paths = array(rtrim($rootPath, "/\\"));
        $dirs = array();

        while (!empty($paths)) {
            $path = array_shift($paths);
            $dir = @opendir($path);
            if (!$dir) {
                continue;
            }

            while ($file = @readdir($dir)) {
                if ($file == "." || $file == "..") {
                    continue;
                }

                $file = "$path/$file";

                if (is_dir($file)) {
                    $paths[] = $file;
                    $dirs[] = $file;
                    continue;
                }

                if (@unlink($file)) {
                    $list[] = $file;
                }
            }

            for ($i = count($dirs) - 1; $i >= 0; $i--) {
                if (@rmdir($dirs[$i])) {
                    $list[] = $dirs[$i];
                }
            }
        }

        if ($includeSelf) {
            if (@rmdir($rootPath)) {
                $list[] = $rootPath;
            }
        }

        return $list;
    }
}

if (!function_exists("exportCsv")) {
    /**
     * 导出数据到csv文件提供下载
     *
     * @param string $filename 下载文件名
     * @param array $data 数据
     */
    function exportCsv($filename, array $data)
    {
        @header("Cache-Control: public");
        @header("Pragma: public");
        @header("Content-Type: application/vnd.ms-excel");
        @header("Content-Disposition: attachment; filename={$filename}.csv");
        @header("Content-Type: application/octet-stream");
        @header("Content-Type: application/download");
        @header("Content-Type: application/force-download");

        $handle = fopen("php://output", "w");
        foreach ($data as $v) {
            if (is_array($v)) {
                fputcsv($handle, $v);
            }
        }
        exit;
    }
}

if (!function_exists("buildVerifyImage")) {
    /**
     * 构造数字验证码
     *
     * 修改自thinkphp(http://www.thinkphp.cn)
     *
     * @param int $width 宽度
     * @param int $height 高度
     *
     * @return string
     */
    function buildVerifyImage($width = 48, $height = 22)
    {
        $verifyCode = "";
        $chars = "1234567890";
        $length = 4;
        $im = imagecreate($width, $height);
        $r = array(225, 255, 255, 223);
        $g = array(225, 236, 237, 255);
        $b = array(225, 236, 166, 125);
        $key = mt_rand(0, 3);
        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);
        $borderColor = imagecolorallocate($im, 100, 100, 100);
        $stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);

        for ($i = 0; $i < 10; $i++) {
            imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $stringColor);
        }
        for ($i = 0; $i < 25; $i++) {
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $stringColor);
        }
        for ($i = 0; $i < $length; $i++) {
            $char = substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            imagestring($im, 5, $i * 10 + 5, mt_rand(1, 8), $char, $stringColor);
            $verifyCode .= $char;
        }

        @header("Content-Type: image/png");
        imagepng($im);
        imagedestroy($im);

        return $verifyCode;
    }
}

if (!function_exists("authcode")) {
    /**
     * 字符串加密/解密
     *
     * 修改自discuz(http://www.discuz.net)
     *
     * @param string $string 字符串
     * @param string $operation 操作
     * @param string $key 加密参数
     * @param int $expiry 有效期
     *
     * @return string
     */
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckeyLength = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckeyLength ? ($operation == 'DECODE' ? substr($string, 0, $ckeyLength) : substr(md5(microtime()), -$ckeyLength)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $keyLength = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckeyLength)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $stringLength = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLength]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
}

if (!function_exists("getXmlNode")) {
    /**
     * 查找XML节点
     *
     * @param mixed $xml XML对象或文件
     * @param string $path 查找路径
     * @param bool $getAll 是否返回所有节点对象
     *
     * @return mixed
     */
    function getXmlNode($xml, $path, $getAll = false)
    {
        $list = array();
        $tempList = array();

        if ($xml instanceof DOMNode) {
            $list[] = $xml;
        } elseif (is_array($xml)) {
            foreach ($xml as $v) {
                if ($xml instanceof DOMNode) {
                    $list[] = $xml;
                }
            }
        } elseif (is_string($xml)) {
            $doc = new DOMDocument("1.0", "utf-8");
            $res = $doc->loadXML(trim($xml));
            if ($res) {
                $list[] = $doc;
            }
        }

        $nodeNames = explode("/", $path);
        foreach ($nodeNames as $nodeName) {
            $nodeName = trim($nodeName);
            if (strlen($nodeName) < 1) {
                continue;
            }

            foreach ($list as $v) {
                foreach ($v->childNodes as $_v) {
                    if ($_v->nodeName == $nodeName) {
                        $tempList[] = $_v;
                    }
                }
            }
            $list = $tempList;
            $tempList = array();
        }

        if (empty($list)) {
            return null;
        }

        if ($getAll) {
            return $list;
        } else {
            return $list[0];
        }
    }
}
