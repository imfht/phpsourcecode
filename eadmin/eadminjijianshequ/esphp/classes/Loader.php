<?php

namespace esclass;

class Loader
{
    //存放命名空间映射
    protected static $maps = [];
    protected static $instance = [];

    //自己写自动加载函数
    public static function autoload($className)
    {

        //完整类名：命名空间名+类名
        //得到命名空间名，根据命名空间名得到其目录路径
        $pos = strpos($className, '\\');//获取出现位置


        //得到空间名
        $namespace = substr($className, 0, $pos);


        //得到类名
        $realClass = substr($className, $pos + 1);

        $realClass = str_replace('\\', DIRECTORY_SEPARATOR, $realClass);
        //找到文件并包含进来
        self::mapLoad($namespace, $realClass);
    }

    //根据命名空间名得到目录路径
    protected static function mapLoad($namespace, $realClass)
    {


        if (array_key_exists($namespace, self::$maps)) {
            $namespace = self::$maps[$namespace];
        }

        //处理路径
        $namespace = rtrim(str_replace('\\', DIRECTORY_SEPARATOR, $namespace), '\\');


        //拼接文件全路径
        $filePath = $namespace . $realClass . '.php';

        //引入文件
        if (file_exists($filePath)) {
            include $filePath;
        } else {

            return false;
            //die($namespace.$realClass.'类不存在');
        }
    }

    //提供命名空间和路径，保存至映射数组中
    public static function addMaps($namespace, $path)
    {

        //array_key_exists() 函数检查某个数组中是否存在指定的键名，如果键名存在则返回 true，如果键名不存在则返回 false。
        if (array_key_exists($namespace, self::$maps)) {
            die('此命名空间以映射过');
        }

        //将命名空间和路径以键值对形式存放到数组中
        self::$maps[$namespace] = $path;

    }

    // 注册自动加载机制
    public static function register()
    {


        spl_autoload_register('\\esclass\\Loader::autoload', true, true);


        self::addMaps('app', APP_PATH);

        self::addMaps('esclass', __CORE__ . 'classes/');

        self::addMaps('extend', ROOT_PATH . 'extend/');


        self::loadfunction();
        self::basedefined();

        self::InitHook();
    }

    public static function loadfunction()
    {


        $files = glob(__ROOT__ . 'esphp/functions/*.php');


        foreach ($files as $file) {

            include_once $file;

        }


        include_once APP_PATH . 'common.func.php';


    }

    public static function basedefined()
    {
        define('RESULT_SUCCESS', 'success');
        define('RESULT_ERROR', 'error');
        define('RESULT_REDIRECT', 'redirect');
        define('RESULT_MESSAGE', 'message');
        define('RESULT_URL', 'url');
        define('RESULT_DATA', 'data');
        define('API_CODE_NAME', 'code');
        define('API_MSG_NAME', 'msg');

        define('DATA_STATUS_NAME', 'status');
        define('DATA_COMMON_STATUS', 'status');
        define('DATA_NORMAL', 1);
        define('DATA_DISABLE', 0);
        define('DATA_DELETE', -1);
        define('DATA_SUCCESS', 1);
        define('DATA_ERROR', 0);

        define('TIME_CT_NAME', 'create_time');
        define('TIME_UT_NAME', 'update_time');
        define('TIME_NOW', time());

        define('SYS_APP_NAMESPACE', config('', 'app_namespace'));
        define('SYS_HOOK_DIR_NAME', 'hook');
        define('SYS_ADDON_DIR_NAME', 'addon');
        define('SYS_COMMON_DIR_NAME', 'common');

        define('SYS_DRIVER_DIR_NAME', 'driver');
        define('SYS_STATIC_DIR_NAME', 'static');

        define('SYS_VERSION', webconfig('SYS_VERSION'));
        define('SYS_ADMINISTRATOR_ID', 1);
        define('SYS_DSS', '/');
        define('SYS_DS_PROS', '/');
        define('SYS_DS_CONS', '\\');
        define('SYS_ENCRYPT_KEY', '}a!vI9wX>l2V|gfZp{8`;jzR~6Y1_p-e,#"MN=e:');

        define('LAYER_LOGIC_NAME', 'logic');
        define('LAYER_MODEL_NAME', 'model');
        define('LAYER_SERVICE_NAME', 'service');
        define('LAYER_CONTROLLER_NAME', 'controller');


        define('WEB_URL', detect_site_url());
        define('WEB_PATH_UPLOAD', WEB_URL . SYS_DSS . 'uploads' . SYS_DSS);
        define('WEB_PATH_PICTURE', WEB_PATH_UPLOAD . 'picture' . SYS_DSS);
        define('WEB_PATH_FILE', WEB_PATH_UPLOAD . 'file' . SYS_DSS);

        define('PATH_ADDON', ROOT_PATH . SYS_ADDON_DIR_NAME . '/');
        define('PATH_PUBLIC', ROOT_PATH . 'public' . DS);
        define('PATH_UPLOAD', ROOT_PATH . 'uploads' . DS);
        define('PATH_PICTURE', PATH_UPLOAD . 'picture' . DS);
        define('PATH_FILE', PATH_UPLOAD . 'file' . DS);
        define('PATH_SERVICE', ROOT_PATH . DS . SYS_APP_NAMESPACE . DS . SYS_COMMON_DIR_NAME . DS . LAYER_SERVICE_NAME . DS);

        // 注册插件根命名空间
        self::addMaps(SYS_ADDON_DIR_NAME, PATH_ADDON);

        $api_key = webconfig('api_key');
        $jwt_key = webconfig('jwt_key');

        empty($api_key) && $api_key = 'ESPHP';
        empty($jwt_key) && $jwt_key = 'ESPHP';

        define('API_KEY', $api_key);
        define('JWT_KEY', $jwt_key);


        $database_config = config('database');

        $list_rows = webconfig('list_rows');

        define('DB_PREFIX', $database_config['prefix']);

        empty($list_rows) ? define('DB_LIST_ROWS', 10) : define('DB_LIST_ROWS', $list_rows);

    }

    /**
     * 初始化插件静态资源
     */
    private static function initAddonStatic()
    {

        $regex = '/[^\s]+\.(jpg|gif|png|bmp|js|css)/i';

        $url = htmlspecialchars(addslashes(Request::instance()->url()));

        if (strpos($url, SYS_ADDON_DIR_NAME) !== false && preg_match($regex, $url)) :

            $url = PATH_ADDON . str_replace(SYS_DSS, DS, substr($url, strlen(SYS_DSS . SYS_ADDON_DIR_NAME . SYS_DSS)));

            !is_file($url) && exit('plugin resources do not exist.');

            $ext = pathinfo($url, PATHINFO_EXTENSION);

            $header = 'Content-Type:';

            in_array($ext, ['jpg', 'gif', 'png', 'bmp']) && $header .= "image/jpeg;text/html;";

            switch ($ext) {
                case 'css':
                    $header .= "text/css;";
                    break;
                case 'js' :
                    $header .= "application/x-javascript;";
                    break;
            }

            $header .= "charset=utf-8";

            header($header);

            exit(file_get_contents($url));

        endif;
    }

    /**
     * 行为入口
     */
    private static function InitHook()
    {


        $hook_list = database::getInstance()->table('hook')->column('name,addon_list');

        foreach ($hook_list as $k => $v) {

            if (!empty($v)):

                $where[DATA_COMMON_STATUS] = DATA_NORMAL;
                $name_list                 = explode(',', $v);
                $where['name']             = $name_list;

                $data = database::getInstance()->table('addon')->where($where)->column('id,name');


                !empty($data) && Hook::add($k, array_map('get_addon_class', array_intersect($name_list, $data)));

            endif;
        }

    }

    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     *
     * @param string  $name    字符串
     * @param integer $type    转换类型
     * @param bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }

    /**
     * 解析URL的pathinfo参数和变量
     *
     * @access private
     * @param string $url URL地址
     * @return array
     */
    private static function parseUrlPath($url, $config = [])
    {
        $url = trim($url, '/');
        $var = [];
        //分两种，参数url和带“/”的


        if (strpos($url, '?') !== false && strpos($url, '.' . $config['view_suffix']) === false) {
            $info = parse_url($url);

            $info['query'] = str_replace('?', '&', $info['query']);

            parse_str($info['query'], $newpath);

        } else {
            $url = str_replace('.' . $config['view_suffix'], '', $url);
            $url = str_replace(['?', '=', '&'], '/', $url);


            // [模块/控制器/操作]
            $path = explode('/', $url);

            $controller = !empty($path) ? array_shift($path) : null;
            // 解析操作
            $action = !empty($path) ? array_shift($path) : null;


            preg_replace_callback('/(\w+)\|([^\|]+)/', function ($match) use (&$var) {
                $var[$match[1]] = strip_tags($match[2]);
            }, implode('|', $path));

            $newpath = array_merge(['c' => $controller, 'a' => $action], $var);

        }


        $var  = array_slice($newpath, 2);
        $path = array_slice($newpath, 0, 2);

        return [$path, $var];
    }

    /**
     * 调用反射执行类的方法 支持参数绑定
     *
     * @access public
     * @param string|array $method 方法
     * @param array        $vars   变量
     * @return mixed
     */
    public static function invokeMethod($method, $vars = [])
    {


        if (is_array($method)) {
            $class = is_object($method[0]) ? $method[0] : self::invokeClass($method[0]);

            $reflect = new \ReflectionMethod($class, $method[1]);

        } else {
            // 静态方法
            $reflect = new \ReflectionMethod($method);
        }
        $args = self::bindParams($reflect, $vars);

        return $reflect->invokeArgs(isset($class) ? $class : null, $args);
    }

    /**
     * 调用反射执行类的实例化 支持依赖注入
     *
     * @access public
     * @param string $class 类名
     * @param array  $vars  变量
     * @return mixed
     */
    public static function invokeClass($class, $vars = [])
    {

        $reflect     = new \ReflectionClass($class);
        $constructor = $reflect->getConstructor();


        if ($constructor) {
            $args = self::bindParams($constructor, $vars);

        } else {
            $args = [];
        }

        return $reflect->newInstanceArgs($args);
    }

    /**
     * 绑定参数
     *
     * @access private
     * @param \ReflectionMethod|\ReflectionFunction $reflect 反射类
     * @param array                                 $vars    变量
     * @return array
     */
    private static function bindParams($reflect, $vars = [])
    {

        if (empty($vars)) {

            $vars = Request::instance()->param();


        }
        $args = [];
        if ($reflect->getNumberOfParameters() > 0) {

            // 判断数组类型 数字数组时按顺序绑定参数
            reset($vars);
            $type   = key($vars) === 0 ? 1 : 0;
            $params = $reflect->getParameters();

            foreach ($params as $param) {
                $args[] = self::getParamValue($param, $vars, $type);
            }
        }

        return $args;
    }

    /**
     * 获取参数值
     *
     * @access private
     * @param \ReflectionParameter $param
     * @param array                $vars 变量
     * @param string               $type
     * @return array
     */
    private static function getParamValue($param, &$vars, $type)
    {
        $name = $param->getName();

        $class = $param->getClass();

        if ($class) {
            $className = $class->getName();
            $bind      = Request::instance()->$name;
            if ($bind instanceof $className) {
                $result = $bind;
            } else {
                if (method_exists($className, 'invoke')) {
                    $method = new \ReflectionMethod($className, 'invoke');
                    if ($method->isPublic() && $method->isStatic()) {
                        return $className::invoke(Request::instance());
                    }
                }
                $result = method_exists($className, 'instance') ? $className::instance() : new $className;
            }
        } elseif (1 == $type && !empty($vars)) {
            $result = array_shift($vars);
        } elseif (0 == $type && isset($vars[$name])) {
            $result = $vars[$name];
        } elseif ($param->isDefaultValueAvailable()) {
            $result = $param->getDefaultValue();
        } else {
            throw new \InvalidArgumentException('method param miss:' . $name);
        }
        return $result;
    }

    public static function run(Request $request = null)
    {

        is_null($request) && $request = Request::instance();

        if (defined('BIND_MODULE')) {

            $module = BIND_MODULE;
        } else {
            $name = pathinfo($request->baseFile(), PATHINFO_FILENAME);//获取模块名称

            $module = $name;
        }


        $path = $request->url();


        $config = config();

        $module_config = load_config(APP_PATH . DS . $module . '/config.php');

        $config = array_merge($config, $module_config);

        $OPEN_ROUTER = webconfig('OPEN_ROUTER');

        if (!$_SERVER['QUERY_STRING'] && !$_SERVER['PATH_INFO']) {
            if ($module == 'admin') {
                $path = [
                    'c' => config('config.default_controller'),
                    'a' => 'adminindex',
                ];
            } else {
                $path = [
                    'c' => config('config.default_controller'),
                    'a' => config('config.default_action'),
                ];
            }
            $var = [];

        } else {


            if (strpos($path, '?') !== false && strpos($path, '.' . $config['view_suffix']) === false) {

                if ($_SERVER['QUERY_STRING']) {
                    list($path, $var) = self::parseUrlpath('?' . $_SERVER['QUERY_STRING'], $config);
                }

            } else {
                if ($_SERVER['QUERY_STRING']) {
                    if (strpos('s=/', $_SERVER['QUERY_STRING']) === false) {
                        list($path, $var) = self::parseUrlpath($_SERVER['PATH_INFO'] . '&' . $_SERVER['QUERY_STRING'], $config);
                    } else {


                        list($path, $var) = self::parseUrlpath(str_replace('s=/', '/', $_SERVER['QUERY_STRING']), $config);
                    }

                } else {
                    list($path, $var) = self::parseUrlpath($_SERVER['PATH_INFO'], $config);
                }

            }
        }


        $request->route($var);
        if (empty($path['c'])) {
            if ($module == 'admin') {
                $path['c'] = config('config.default_controller');


            } else {
                $path['c'] = config('config.default_controller');
            }
        }
        if (empty($path['a'])) {
            if ($module == 'admin') {
                $path['a'] = 'adminindex';

            } else {
                $path['a'] = config('config.default_action');
            }
        }

        //得到了路径地址和参数列表数组
        $route = [$module, $path['c'], $path['a']];

        // 记录当前调度信息
        $request->dispatch(['type' => 'module', 'module' => $route]);
        $request->controller($path['c']);
        $request->action($path['a']);
        $request->module($module);

        $class = 'app\\' . $module . '\\controller\\' . ucfirst($path['c']);//命名空间类名

        if (class_exists($class)) {

            $instance = self::invokeClass($class);


        }

        $action = $path['a'];


        if (is_callable([$instance, $action])) {
            // 执行操作方法

            $call = [$instance, $action];


        } elseif (is_callable([$instance, '_empty'])) {
            // 空操作
            $call = [$instance, '_empty'];
            $vars = [$action];
        } else {
            // 操作不存在
            throw new EsException('操作方法不存在');
        }

        return self::invokeMethod($call, $var);

    }

    /**
     * 实例化验证类 格式：[模块名/]验证器名
     *
     * @param string $name         资源地址
     * @param string $layer        验证层名称
     * @param bool   $appendSuffix 是否添加类名后缀
     * @param string $common       公共模块名
     * @return Object|false
     * @throws ClassNotFoundException
     */
    public static function validate($name = '', $layer = 'validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: config('config.default_validate');
        if (empty($name)) {
            return new Validate;
        }
        $name = ucfirst($name);
        $guid = $name . $layer;
        if (isset(self::$instance[$guid])) {
            return self::$instance[$guid];
        }
        if (false !== strpos($name, '\\')) {
            $class  = $name;
            $module = Request::instance()->module();
        } else {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name);
            } else {
                $module = Request::instance()->module();
            }
            $class = '\\app\\' . $module . '\\' . $layer . '\\' . $name;
        }

        if (class_exists($class)) {
            $validate = new $class;
        } else {

            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);

            if (class_exists($class)) {
                $validate = new $class;
            } else {
                throw new \Exception('class not exists:' . $class);
            }
        }
        self::$instance[$guid] = $validate;
        return $validate;
    }
}
