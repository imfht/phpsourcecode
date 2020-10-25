<?php
//加载函数和类的助手函数
/**
* [loadf 加载函数]
* @return [type] [description]
*/
function loadf() {
    $arguments = func_get_args();//获取传给函数的参数（数组）
    $name = array_shift($arguments);//弹出第一个参数，即函数名
    if ($name == '') {
        die('function name is empty!');
    } else {
        $call_exist = stripos($name, 'call:');//如果有call:字样，就直接返回函数名
        if ($call_exist === 0) {
           $callf = explode(':', $name);
           $name = $callf[1];
        }

        $function = "workerbase\\func\\".$name;
        if (!function_exists($function)) {
            $func =  dirname(__FILE__).DIRECTORY_SEPARATOR.'func'.DIRECTORY_SEPARATOR. $name . '.php';

            if (!is_file($func)) {
                die(' function ' . $name . ' Not Found!');
            }
            require_once $func;
        }

        if ($call_exist === 0) {
            return $function;
        } else {
            return  call_user_func_array ($function , $arguments);//调用函数，并传递参数
        }
    }
}
/*
加载类
*/
function loadc() {
    $arguments = func_get_args();//获取传给函数的参数（数组）
    $name = array_shift($arguments);//弹出第一个参数，即类名
    if ($name == '') {
        die('class name is empty!');
    }

    $name = strtolower($name);
    static $workerbase_modules = array();
    if (isset($workerbase_modules[$name])) {
        return $workerbase_modules[$name];
    }

    $class_name = "workerbase\\classs\\" . ucfirst($name);
    if (!class_exists($class_name)) {
        $class =  dirname(__FILE__).DIRECTORY_SEPARATOR.'classs'.DIRECTORY_SEPARATOR. $name . '.php';

        if (!is_file($class)) {
            die(' class ' . $name . ' Not Found!');
        }
        require_once $class;
    }

    $class_name = new \ReflectionClass($class_name);//反射类
    $workerbase_modules[$name] = $class_name->newInstanceArgs($arguments);//传入参数
    return $workerbase_modules[$name];
}

/**
 * [cliRun php命令执行]
 * @param  string $path      命令目录
 * @param  string $namespace 命令目录对应的命名空间
 * @param  string $suffix   后缀
 * @param string $class_name  [可选]类名（设置将不接收命令行参数）
 * @param string $func_name   [可选]方法名（设置将不接收命令行参数）
 * @param array $arguments  [可选]方法参数数组
 * @return array            []
 */
function cliRun($path, $namespace="\\", $suffix = '', $class_name = null, $func_name = null, $arguments = []) {
    global $argc,$argv;
    $return_arr = array(
        'code'=> -1,
        'msg'=> 'false',
        'data'=>''
    );
    if (is_null($class_name) || is_null($func_name)) {
        $arguments_count = $argc;//参数数量
        if ($arguments_count < 3) {
            $return_arr['msg'] = 'params false';
            return $return_arr;
        }

        $arguments = $argv;//获取用户输入的参数（数组）
        array_shift($arguments);//弹出第一个参数，为当前文件的相对路径
        $class_name = array_shift($arguments);//弹出第二个参数，即类名
        $func_name = array_shift($arguments);//弹出第三个参数，即函数名
    }

    if (empty($class_name)) {
        $return_arr['msg'] = 'class_name is empty!';
        return $return_arr;
    }
    if (empty($func_name)) {
        $return_arr['msg'] = 'func_name is empty!';
        return $return_arr;
    }

    //检测命名空间
    if ($namespace != "\\") {
        $namespace = trim($namespace,"\\");
        if ($namespace != '') {
            $namespace = "\\".$namespace."\\";
        } else {
            $namespace = "\\";
        }
    }

    $class_name_tmp = $class_name . $suffix;
    //带命名空间的类名
    $class_name = $namespace . $class_name_tmp;

    if (!class_exists($class_name)) {
        //加载类文件
        $class_file =  $path . $class_name_tmp .'.php';
        if (!is_file($class_file)) {
            $return_arr['msg'] = ' class file ' . $class_file . ' Not Found!';
            return $return_arr;
        }
        require_once $class_file;
    }

    //检查类和方法是否正确
    if (!class_exists($class_name)) {
        $return_arr['msg'] = $class_name . ' does not exist! ';
        return $return_arr;
    }
    $reflection_class = new \ReflectionClass($class_name);
    if (!$reflection_class->IsInstantiable()) { //是否可实例化
        $return_arr['msg'] = $class_name . ' is not instantiable! ';
        return $return_arr;
    }
    if (!$reflection_class->hasMethod($func_name)) { //方法是否存在
        $return_arr['msg'] = $class_name . '::' . $func_name . ' does not exist! ';
        return $return_arr;
    }

    unset($reflection_class);
    $instance = new $class_name;
    $result = call_user_func_array ([$instance, $func_name], $arguments); //调用函数，并传递参数
    unset($instance);

    $return_arr['code'] = 200;
    $return_arr['msg'] = 'success';
    $return_arr['data'] = $result?$result:'';
    return $return_arr;
}
