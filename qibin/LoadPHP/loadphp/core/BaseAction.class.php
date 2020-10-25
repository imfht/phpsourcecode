<?php
// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * 框架基类
 +------------------------------------------------------------------------------
 */
class BaseAction extends Smarty {
    function __construct() {
        parent::__construct();                                          //调用父类被覆盖的构造方法
        $this->template_dir = VIEW_PATH;                                //模板目录
        $this->compile_dir = RUNTIME_PATH."compile/".$_GET['c'];        //里的文件是自动生成的，合成的文件
        $this->caching = ISCACHE;                                       //设置缓存开启
        $this->cache_dir = RUNTIME_PATH."cache/".$_GET['c'];            //设置缓存的目录
        $this->cache_lifetime = CACHE_LIFE;                             //设置缓存的时间 
        $this->left_delimiter = LEFT_DELIMITER;                         //模板文件中使用的“左”分隔符号
        $this->right_delimiter = RIGHT_DELIMITER;                       //模板文件中使用的“右”分隔符号
    }
    
    // +重写父类的assign方法
    function assign($tpl_var, $value = NULL, $nocache = false) {
        $args = func_get_args();
        parent::assign($args[0],$args[1]);
    }
    
    // +设置缓存为true时，这里自动调用，项目中不必添加缓存项
    protected function loadCache() {
        $cache = $this->proArray($_GET,$_POST);
        return md5("loadphp_".$cache);
    }
    
    // +处理$_GET和$_POST将数组中的键/值组合成字符串
    private function proArray($get,$post) {
        $arrays = array_merge($get,$post);
        
        $keys = array_keys($arrays);
        $values = array_values($arrays);
        
        return implode('',$keys).implode('',$values);
    } 
    
    // +重写父类的display方法
    function display($arg='',$mark='',$location='', $parent = NULL) {
        if(!empty($mark)) {
            $this->assign("mark",$mark);
        }
        
        if(!empty($location)) {
            $this->assign("location",$location);
        }

        $this->assign("url",URL);                                //url链接等
        $this->assign("cururl",CURURL);                          //当前控制器URL
        $this->assign("public",$GLOBALS['public_path']);         //APP级别js等包含时的路径
        $this->assign("pub",$GLOBALS['pub_path']);               //框架目录级别js等包含时的路径
        $this->assign("loadpath",LOAD_PATH);                     //框架所在目录
        $this->assign("tplpath",TPL_PATH);                       //模板套名
        
        if(empty($arg)) {
            if($this->caching) parent::display(TPL_PATH.'/'.$_GET['c'].'/'.$_GET['a'].".".TPL_TYPE,$this->loadCache());     //判断缓存是否开启
            else parent::display(TPL_PATH.'/'.$_GET['c'].'/'.$_GET['a'].".".TPL_TYPE);
        }else if("notice"==strtolower($arg)) {
            if($this->caching) parent::display($arg,$this->loadCache());
            else parent::display($arg);
        }else if(strpos($arg,'@')) {
            $dirAndFile = explode('@',$arg);
            if($this->caching) parent::display(TPL_PATH.'/'.$dirAndFile[1].'/'.$dirAndFile[0].'.'.TPL_TYPE,$this->loadCache());
            else parent::display(TPL_PATH.'/'.$dirAndFile[1].'/'.$dirAndFile[0].'.'.TPL_TYPE); 
        }else{
            if($this->caching) parent::display(TPL_PATH.'/'.$_GET['c'].'/'.$arg.'.'.TPL_TYPE,$this->loadCache());
            else parent::display(TPL_PATH.'/'.$_GET['c'].'/'.$arg.'.'.TPL_TYPE);
        }
    }
    
    // +重写父类的clearCache方法
    function clearCache($template_name, $cache_id = NULL, $compile_id = NULL, $exp_time = NULL, $type = NULL) {
        parent::clearAllCache();
    }
    
    // +重写父类的clearAllCache方法
    function clearAllCache($exp_time = NULL, $type = NULL) {
        $this->clearFiles(RUNTIME_PATH.'cache');
    }
    
    // +递归删除所有缓存
    private function clearFiles($dir) {
        $dir = trim($dir,'/').'/';
        $loadHandle = opendir($dir);
        while($loadResorce = readdir($loadHandle)) {
            if($loadResorce!='.' && $loadResorce!='..') {
                if(is_dir($dir.$loadResorce)) {
                    $this->clearFiles($dir.$loadResorce);
                }else {
                    @unlink($dir.$loadResorce);
                }
            }
        }
    }
}
?>