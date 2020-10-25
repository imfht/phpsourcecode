<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

class Libs{
    public function run(){
        global $router;
        $this->router_dir = $router->dir;
        $this->router_class = $router->class;
        $this->router_method = $router->method;

        $this->before();
        session_start();
        $method = func_get_arg(0)->getmethod(func_get_arg(2));
        $method->invokeArgs(func_get_arg(1), func_get_arg(3));
        $this->after();
    }
	
    public function before(){
        $this->config = include DIR_CONF.'config.conf.php';

        if(ENVIRONMENT != 'production' && file_exists(DIR_CONF.ENVIRONMENT.'/'.'config.conf.php')){
            $this->config = include_once DIR_CONF.ENVIRONMENT.'/'.'config.conf.php';
        }

        if(!empty($this->config['php.ini'])){
            foreach($this->config['php.ini'] as $pk => $pv){
                ini_set($pk, $pv);
            }
        }    
    }
	
    public function after(){
        if((isset($this->is_smarty) && $this->is_smarty !== false) || !isset($this->is_smarty)){
            require_once DIR_CONF.'smarty.conf.php';
            $this->smarty = $smarty;
        }

        if(!empty($this->tpl)){
            header('Content-type: text/html; charset=utf-8');
            $this->tpl_include($this->tpl);
        }else if(!empty($this->_json_data)){
            header('Content-type: application/json;');
            echo json_encode($this->_json_data);
        }

        if(!empty($this->db_arr)){
            foreach((array)$this->db_arr as $v){
                $v = trim($v);
                $this->$v->close();
            }
        }
    }
	
    public function tpl_include($tpl){
        global $file;
        $t_arr = explode('_', $tpl);
        
        $tplname = '';
        foreach($t_arr as $v){
            $tplname .= htmlspecialchars(ucwords(strtolower($v)), ENT_QUOTES, 'UTF-8').'_';
        }
        $tplname = substr($tplname, 0, -1);
        $tplname = explode('/', $tplname);
        $tpl_arr_count = count($tplname);
        $tplname[$tpl_arr_count - 1] = ucwords(strtolower($tplname[$tpl_arr_count - 1]));
        $tplname = implode('/', $tplname);
        if(file_exists(DIR_TPL.$file.$tplname.'.tpl.html')){
            foreach($this as $k => $v){
                $$k = $v;
                if(!empty($this->smarty)){
                    $this->smarty->assign($k, $$k);
                }
            }
            if(!empty($this->smarty)){
                $this->smarty->display($file.$tplname.'.tpl.html');
            }else {
                require DIR_TPL.$file.$tplname.'.tpl.html';
            }
        }else {
            if(ENVIRONMENT == 'development'){
                exit('模版文件: '.DIR_TPL.$file.$tplname.'.tpl.html 不存在');
            }else {
                header('HTTP/1.1 404 Not Found');
                header("status: 404 Not Found");
            }
        }
    }

    public function load_fun($fun_name){
        $arr = explode('/', $fun_name);
        $file = '';
        $count = count($arr) - 1;
        for($i = 0; $i < $count; $i++){
            $file .= $arr[$i].'/';
        }
        $filename = ucfirst($arr[$count]);
        $file .= $filename.'.fun.php';

        if(file_exists(DIR_FUN.$file)){
            require_once DIR_FUN.$file;
        }else {
            if(ENVIRONMENT == 'development'){
                exit('函数文件：'.$file.' 不存在');
            }else {
                header('HTTP/1.1 404 Not Found');
                header("status: 404 Not Found");
            }
        }
    }

    public function load_class($class_name, $is_new = true){
        $arr = explode('/', $class_name);
        $file = '';
        $count = count($arr) - 1;
        for($i = 0; $i < $count; $i++){
            $file .= $arr[$i].'/';
        }
        $filename = ucfirst($arr[$count]);
        $file .= $filename.'.class.php';

        if(file_exists(DIR_CLASS.$file)){
            require_once DIR_CLASS.$file;
            if($is_new == true){
                $this->$class_name = new $filename;
            }
        }else {
            if(ENVIRONMENT == 'development'){
                exit('类文件：'.$file.' 不存在');
            }else {
                header('HTTP/1.1 404 Not Found');
                header("status: 404 Not Found");
            }
        }
    }
}
