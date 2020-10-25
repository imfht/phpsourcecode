<?php

class viewAction extends backendAction
{
    /**
     * 模板列表
     */
	
    public function _initialize() {
    	parent::_initialize();
       $menuid = $_GET['menuid'];
        $this->assign('menuid', $menuid);
    }
    public function tpl() {
        $config_file = CONF_PATH . 'home/config.php';
        $config = include $config_file;
        if ($dirname = $this->_get('dirname', 'trim')) {
            //写入配置文件
            $config['DEFAULT_THEME'] = $dirname;
            file_put_contents($config_file, "<?php \nreturn " . var_export($config, true) . ";", LOCK_EX);
            $obj_dir = new Dir;
            is_dir(CACHE_PATH.'home/') && $obj_dir->delDir(CACHE_PATH.'home/');
            @unlink(RUNTIME_FILE);
        }
        $tpl_dir = TMPL_PATH.'home/';
        $opdir = dir($tpl_dir);
        $template_list = array();
        while (false !== ($entry = $opdir->read())) {
            if ($entry{0} == '.') {
                continue;
            }
            if (!is_file($tpl_dir . $entry . '/info.php')) {
                continue;
            }
            $info = include_once($tpl_dir . $entry . '/info.php');
            $info['preview'] = TMPL_PATH . 'home/' . $entry . '/preview.gif';
            $info['dirname'] = $entry;
            $template_list[$entry] = $info;
        } 
        $this->assign('template_list',$template_list);
        //当前使用的模板
        
        
        
       
        $this->assign('def_tpl', $config['DEFAULT_THEME']);
        $this->display();
    }
    
    public function expression(){
    	$public_path=C('PUBLIC_PATH');
    	 $config_file = CONF_PATH . 'home/config.php';
        $config = include $config_file;
        if ($dirname = $this->_get('dirname', 'trim')) {
            //写入配置文件
            $config['DEFAULT_EMOT'] = $dirname;
            file_put_contents($config_file, "<?php \nreturn " . var_export($config, true) . ";", LOCK_EX);
            $obj_dir = new Dir;
            is_dir(CACHE_PATH.'home/') && $obj_dir->delDir(CACHE_PATH.'home/');
            @unlink(RUNTIME_FILE);
        }
        $emot_dir = './Public/images/emot/';
        
       
        $opdir = dir($emot_dir);
        $emot_list = array();
        while (false !== ($entry = $opdir->read())) {
            if ($entry{0} == '.') {
                continue;
            }
            if (!is_file($emot_dir . $entry . '/info.php')) {
                continue;
            }
            $info = include_once($emot_dir . $entry . '/info.php');
            $info['preview'] = $emot_dir . $entry . '/preview.gif';
            $info['dirname'] = $entry;
            $emot_list[$entry] = $info;
        }
        
       
        $this->assign('emot_list',$emot_list);
        //当前使用的模板
        $this->assign('def_emot', $config['DEFAULT_EMOT']);
        $this->display();
    }
}