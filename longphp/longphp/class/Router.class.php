<?php
/**
 * @require : none
 * @author : yu@wenlong.org
 * @date : 2015-11-14 14:11:10
 * @description : 路由类 
 */
class Router{
    public $class = '';
    public $method = 'index';
    public $dir = 'index/';

    function run(){
        $this->load();
    }

    private function load(){
        global $argv;

        if (PHP_SAPI === 'cli') {
            $request_uri = $argv[1];
        }else {
            $request_uri = $_SERVER['REQUEST_URI'];
        }
        $uri_arr = explode('?', $request_uri);

        if(count($uri_arr) == 2){
            $get_params = explode('&', $uri_arr[1]);
            foreach($get_params as $get_v){
                $get_vs = explode('=', $get_v);
                
                if(count($get_vs) != 2){
                    continue;
                }
                
                $_GET[$get_vs[0]] = $get_vs[1];
            }
        }

        $uri = $uri_arr[0];
        $dir_arr = strtr(dirname(dirname(__FILE__)), array('\\' => '/'));
        $dir_name = explode('/', $dir_arr);
        $pop = array_pop($dir_name);
        if(preg_match('/^(\/'.$pop.')/', $uri)){
            $uri = strtr($uri, array('/'.$pop => ''));
        }

        $uri_arr = array();
        $dir = $file_name = '';
        $is_file = $is_dir = false;
        if($uri != '/'){
            $uri_arr = explode('/', $uri);
            array_shift($uri_arr);

            do{
                foreach($uri_arr as $k => $v){
                    $file_name = ucwords(strtolower($v)).'.controller.php';
                    $dir .= $v.'/';

                    if($is_dir == false && is_dir(DIR_CONTROLLER.$dir)){
                        $this->_set_dir($dir);
                        for($i = 0; $i <= $k; $i++){
                            unset($uri_arr[$i]);
                        }
                        $is_dir = true;
                        continue;
                    }

                    if(!is_file(DIR_CONTROLLER.$this->dir.$file_name) && ENVIRONMENT == 'development'){
                        exit('文件：'.DIR_CONTROLLER.$this->dir.$file_name.' 找不到；<br>或者<br>目录：'.DIR_CONTROLLER.$dir.' 不存在');
                    }

                    $this->_set_class($v);
                    unset($uri_arr[$k]);
                    $is_file = true;
                    break;
                }
            }while(0);
        }else {
            $is_dir = $is_file = true;
            $file_name = 'Index.controller.php';
            $this->_set_class('index');
            $uri_arr[0] = $this->method;
        }

        if($uri != '/' && $is_dir == false && $is_file == false && ENVIRONMENT == 'development'){
            exit('uri：'.$uri.' 找不到对应的控制器');
        }

        if(!empty($uri_arr)){
            $uri_arr = array_values($uri_arr);
        }else {
            $uri_arr[0] = $this->method;
        }

        autoload($this->dir.$this->class);
        $classname = ucwords(strtolower($this->class));

        $action = 'Action_'.$classname;
        $class = new ReflectionClass($action);

        if(!empty($uri_arr) && method_exists($action, $uri_arr[0])){
            $this->_set_method(array_shift($uri_arr));
        }else if(ENVIRONMENT == 'development'){
            exit('文件：'.$this->dir.$file_name.' 控制器：'.$action.' 方法：'.$uri_arr[0].' 不存在');
        }

        $instance = $class->newInstanceArgs();
        $method = $class->getmethod('run');

        $data = array(
            $class,
            $instance,
            $this->method,
            $uri_arr
        );
        $method->invokeArgs($instance, $data);
    }

    private function _set_class($class){
        $this->class = $class;
    }

    private function _set_method($method){
        $this->method = $method;
    }

    private function _set_dir($dir){
        $this->dir = $dir;
    }
}
