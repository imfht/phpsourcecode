<?php
//版权所有(C) 2014 www.ilinei.com

namespace ilinei;

/**
 * 运行上下文
 * @author sigmazel
 * @since v1.0.2
 */
class runtime{
    /**
     * 获取调度
     * @return string[] 模块、控制器、方法、页面、操作
     */
    public function dispatch(){
        global $_var;
        
        $dispatches = array('module' => 'admin', 'control' => '', 'method' => '', 'page' => '', 'operations' => null);
        
        //支持全局4个参数
        $_var['m'] && $dispatches['module'] = $_var['m'];
        $_var['c'] && $dispatches['control'] = $_var['c'];
        $_var['e'] && $dispatches['method'] = $_var['e'];
        $_var['p'] && $dispatches['page'] = $_var['p'];
        
        /**
         * 也可以使用r作为参数，格式如下：
         * r=admin/setting/seo 即 模块：admin；控制器：setting；方法：seo
         * 如果方法不想参与权限检查，则以_打头即可
         */
        if($_var['gp_r']){
            $_var['gp_r'] = str_replace(array('//', '../'), array('/', ''), $_var['gp_r']);
            $_var['gp_r'] = substr($_var['gp_r'], 0, 1) == '/' ? substr($_var['gp_r'], 1): $_var['gp_r'];
            $_var['gp_r'] = explode('/', $_var['gp_r']);
            
            $paths = array();
            foreach ($_var['gp_r'] as $key => $val){
                if($val && is_ansi($val)) $paths[] = $val;
            }
            
            $paths[0] && $dispatches['module'] = $paths[0];
            $paths[1] && $dispatches['control'] = $paths[1];
            $paths[2] && $dispatches['method'] = $paths[2];
        }

        return $dispatches;
    }
    
    /**
     * 执行上下文
     * @throws \Exception
     */
	public function execute(){
	    global $config, $dispatches;
	    
	    !$dispatches && $dispatches = $this->dispatch();
        !$dispatches['control'] && $dispatches['control'] = 'index';
        !$dispatches['method'] && $dispatches['method'] = 'index';

        if(substr($dispatches['control'], 0, 1) == '_'){
            $dispatches['method'] = $dispatches['control'];
            $dispatches['control'] = 'index';
        }elseif(strpos($dispatches['control'], '/') !== false){
            $arr = explode('/', $dispatches['control']);

            $dispatches['control'] = $arr[0];
            $dispatches['method'] = $arr[1];
        }

        if($config['filter'] && class_exists($config['filter'])){
            $filter = new $config['filter']();
            $filter->execute($dispatches);
        }
        
        //拼接控制器类名称
		$class = $dispatches['module'].'\\control\\'.$dispatches['control'];
		
		if(!class_exists($class)) throw new \Exception($GLOBALS['lang']['class.action'].$class);
		if(!method_exists($class, $dispatches['method'])) throw new \Exception($GLOBALS['lang']['class.method'].$class.'->'.$dispatches['method']);
		
		$method = $dispatches['method'];
		$control = new $class();
		$control->$method();
	}
	
}
?>