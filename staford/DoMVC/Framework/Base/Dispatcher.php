<?php

/**
 * 通过全局变量GET的值来进行任务分派
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Dispatcher
{
    public static function dispatch(&$config = array())
    {
        //存在配置项，则调用对应的处理操作
        (isset($config['template']) && self::__template($config['template']));
        (isset($config['database']) && self::__database($config['database']));
        (isset($config['debug']) && self::__debug($config['debug']));
        (isset($config['session']) && self::__session($config['session']));
        (isset($config['timezone']) && self::__timezone($config['timezone']));
        (isset($config['message']) && self::__message($config['message']));
        (isset($config['cache']) && self::__cache($config['cache']));

        $c = ucfirst(strtolower($_GET['c'])); //先全部转小写，再首字母转大写
        $a = strtolower($_GET['a']); //操作名只能小写

        $c_class_file = CTR_DIR . $c . '.CTR.php';

        if (!is_file($c_class_file)) {
            throw new ControllerException('Controller file [' . $c . '] does not exist', 2);
        }

        require $c_class_file;
        $ctr = $c . 'CTR';

        if (!class_exists($ctr, false)) {
            throw new ControllerException('Unable to perform an undefined controller class [' .
                $ctr . ']', 2);
        }

        $controller = new $ctr;
        if (!method_exists($controller, $a)) {
            throw new ControllerException('Unable to perform an undefined action [' . $a .
                ']', 2);
        }
        //如果控制器中存在init()方法，则优先运行此方法
        if (method_exists($controller, 'init')) {
            call_user_func(array($controller, 'init'));
        }
        call_user_func(array($controller, $a));
    }

    private static function __message($msg_conf)
    {
        if (!is_file(TPL_DIR . $msg_conf)) {
            return false;
        }
        Controller::setMsgFile($msg_conf);
    }

    private static function __timezone($timezone)
    {
        if ($timezone) {
            date_default_timezone_set($timezone);
        } else {
            date_default_timezone_set('PRC');
        }
    }

    private static function __session($state)
    {
        if ($state) {
            session_start();
        }
    }

    private static function __debug($state)
    {
        if ($state) {
            ini_set('display_errors', 'on');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 'off');
            error_reporting(0);
        }
    }

    private static function __template($tpl_conf)
    {
        Template::setCfg($tpl_conf);
    }

    private static function __database($db_conf)
    {
        if (isset($db_conf['state']) && strtolower($db_conf['state']) === 'on') {
            $db_array = array(
                'type' => 'mysql',
                'host' => '127.0.0.1',
                'user' => 'root',
                'pass' => '',
                'name' => 'test',
                'charset' => 'utf8',
                'pconnect' => true);
            $db_conf = array_merge($db_array, $db_conf);
            Model::init($db_conf['type'], $db_conf['host'], $db_conf['user'], $db_conf['pass'],
                $db_conf['name'], $db_conf['charset'], $db_conf['pconnect']);
        }
    }

    private static function __cache($cfg)
    {
        Cache::setCacheDir($cfg);
    }
}

?>