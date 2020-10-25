<?php
/**
 * 创建一个视图
 * @author wolf [Email: 116311316@qq.com]
 * @since 2011-10-01
 */

class View
{
    /**
     * Smarty 对象
     * @var Smarty
     */
    protected $_smarty;
    static $_instance;
    /**
     * 构造函数
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    private function __construct()
    {
        $config = require_once 'smarty.php'; //导入配置		 
        require_once 'smarty/Smarty.class.php';
        require_once 'smarty/plugins/resource.mysql.php';
        $this->_smarty = new Smarty();
        $this->_smarty->debugging = false;
        //注册数据库模板 可以使用以mysql为前缀的模板  这里需要引入Db库
        $rs = $this->_smarty->registerResource('mysql', new Smarty_Resource_Mysql());
       
        $this->_smarty->muteExpectedErrors(); //屏蔽smarty filemtime错误
        $this->_smarty->setTemplateDir(ROOT . $config['templates_dir']);
        $this->_smarty->setCacheDir(ROOT . $config['cache_dir']);
        $this->_smarty->setCompileDir(ROOT . $config['compile_dir']);
        $this->_smarty->caching = $config['caching'];
        $this->_smarty->cache_lifetime = $config['cache_lifetime'];
        $this->_smarty->left_delimiter = $config['left_delimiter'];
        $this->_smarty->right_delimiter = $config['right_delimiter'];
        $this->_smarty->compile_check = $config['compile_check'];
        $this->_smarty->force_compile = $config['force_compile']; //强迫编译
    

    }
    //禁止复制
    private function __clone()
    {
    
    }
    
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function isCached($template, $cache_id = null, $compile_id = null, $parent = null)
    {
        return $this->_smarty->isCached($template, $cache_id, $compile_id = null, $parent = null);
    }
    
    /**
     * 为模板设置一个变量
     * 允许设置一个具体的值给一个具体的变量，或者传递一个数组，用key => value键值对的形式批量赋值。（）
     * @see __set()
     * @param string|array $spec 变量名，或数组
     * @param mixed $value 可选的。如果前一个参数是变量，这个值将赋给变量
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }
        $this->_smarty->assign($spec, $value);
    }
    
    /**
     * 
     * 清楚编译缓存
     * @param unknown_type $resource_name
     * @param unknown_type $compile_id
     * @param unknown_type $exp_time
     */
    public function clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null)
    {
        return $this->_smarty->clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null);
    }
    
    public function clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null)
    {
        return $this->_smarty->clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null);
    }
    
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        return $this->_smarty->fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
    }
    
    /**
     * 
     * @return int $t
     */
    public function clearAllCache($exp_time = null, $type = null)
    {
        return $this->_smarty->clearAllCache($exp_time = null, $type = null);
    }
    
    /**
     * 处理一个模板，并返回其输出。.
     * @param string $name 要处理的模板.
     * @return string 输出的内容.
     */
    public function display($resource_name, $cache_id = null, $compile_id = null)
    {
        
        $this->_smarty->display($resource_name, $cache_id, $compile_id);
    }

}