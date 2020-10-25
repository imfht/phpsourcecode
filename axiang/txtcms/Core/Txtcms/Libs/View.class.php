<?php
/**
 * TXTCMS 框架视图类
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class View {
	public $template;
	private $group;
	public $options;
	function __construct(){
		$this->template=new Smarty;
		$this->group=defined('GROUP_NAME')?GROUP_NAME.'/':'';
		$theme=$this->getTemplateTheme();
		$this->options=array();
		$this->options['compile_check']=config('TMPL_COMPILE_CHECK');
		$this->options['caching']=config('HTML_CACHE');
		$this->options['tpl_path']=config('TMPL_PATH') ? config('TMPL_PATH') : TMPL_PATH;
		$this->options['template_dir']=$this->options['tpl_path'].$this->group.$theme;
		$this->options['cache_dir']=CACHE_PATH.'Html/'.$this->group.$theme;
		$this->options['compile_dir']=TPLCACHE_PATH.$this->group.$theme;
		$this->options['cache_id']=md5($_SERVER['QUERY_STRING']); //缓存ID
		$this->options['cache_html_user_hashdir']=config('HTML_CACHE_USE_HASHDIR'); //是否开启缓存子目录
		$this->options['cache_html_hashdir_level']=config('HTML_CACHE_HASHDIR_LEVEL'); //缓存目录级别
		$this->initialize();
	}
	private function initialize(){
		$this->template->debugging=false;
		$this->template->compile_check=$this->options['compile_check'];
		$this->template->addPluginsDir(TEMPLATE_PATH.'plugins');
		$this->template->template_dir=$this->options['template_dir'];
		$this->template->compile_dir=$this->options['compile_dir'];	//编译目录
		$this->template->caching=false; //缓存开关
		$this->template->cache_dir=$this->options['cache_dir'];	//缓存目录
		$this->template->use_sub_dirs=false;
		$this->template->left_delimiter='{';	//左边界符
		$this->template->right_delimiter='}';	//右边界符
	}
	/**
     * 模板变量赋值
     * @access public
     * @param mixed $name
     * @param mixed $value
     */
    public function assign($name,$value=''){
		$this->template->assign($name,$value);
    }
	/**
     * 获取模板变量的值
     * @access public
     * @param mixed $name
     * @param mixed $value
     */
    public function get($name=''){
		return $this->template->getTemplateVars($name);
    }
	//获取当前操作模板
	public function getTemplate(){
		//获取当前主题名称
		if(ACTION_NAME==config('DEFAULT_ACTION')){
			$template=MODULE_NAME;
		}else{
			$template=MODULE_NAME.'_'.ACTION_NAME;
		}
		$group=$this->group;
		if($this->group==config('DEFAULT_GROUP').'/'){
			$group='';
		}
		define('THEME_PATH',$this->options['tpl_path'].$group.$this->getTemplateTheme());
		$template=THEME_PATH.$template.config('TMPL_TEMPLATE_SUFFIX');
		return $template;
	}
	/**
     * 模板输出
     * @access public
     * @param string $templateFile 模板文件名
     */
	public function display($templateFile=''){
		//模板参数重新赋值
		$this->initialize();
		$cacheid=$this->options['cache_id'];
		if(!is_file($templateFile)) {
			$templateFile=$this->getTemplate();
		}
		if($this->options['caching']){
			$_cache_file=$this->getHtmlPath($cacheid);
			$_cache_life_time=$this->options['cache_lifetime'];
			if(is_file($_cache_file) && (filemtime($_cache_file)+$_cache_life_time)>time()){
				echo file_get_contents($_cache_file);
			}else{
				$_cache_dirs=dirname($_cache_file);
				if(!is_dir($_cache_dirs)) mkdir($_cache_dirs,0766,true);
				ob_start();
				$this->template->display('file:'.$templateFile,$cacheid);
				$_cache_html=ob_get_contents();
				file_put_contents($_cache_file,$_cache_html);
				ob_end_flush();
			}
		}else{
			$this->template->display('file:'.$templateFile,$cacheid);
		}
	}
	public function __call($name,$args){
		$method=new ReflectionMethod($this->template,$name);
		$method->invokeArgs($this->template,$args);
	}
	/**
     * 获取当前的模板主题
     * @access private
     * @return string
     */
    private function getTemplateTheme() {
		$theme=config('DEFAULT_THEME');
        define('THEME_NAME',$theme); // 当前模板主题名称
        return $theme ? $theme.'/':'';
    }
	/**
     * 获取缓存文件路径
     * @access public
     * @return string
     */
	 public function getHtmlPath($cacheid){
		$_cache_dirs=$this->options['cache_html_user_hashdir'] ? getHashDir($cacheid,$this->options['cache_html_hashdir_level']) : '';
		$_cache_dirs=$this->options['cache_dir'].$_cache_dirs;
		$_cache_file=$_cache_dirs.'/'.$cacheid.config('HTML_CACHE_SUFFIX');
		return $_cache_file;
	 }
}