<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\controller;
use think\Request;
use think\Config;
use think\Loader;
abstract class Plus extends Base
{
    // 当前插件操作
    protected $addon = null;
    protected $controller = null;
    protected $action = null;
    // 当前template
    protected $template;
    // 模板配置信息
    protected $config = [
        'type' => 'Think',
        'view_path' => 'sasf',
        'view_suffix' => 'html',
        'strip_space' => true,
        'view_depr' => DS,
        'tpl_begin' => '{',
        'tpl_end' => '}',
        'taglib_begin' => '{',
        'taglib_end' => '}',
    ];

    /**
     * 架构函数
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        // 生成request对象
        $this->request = is_null($request) ? Request::instance() : $request;
        // 初始化配置信息
        $this->config = Config::get('template') ?: $this->config;
        // 处理路由参数
        // 格式化路由的插件位置
        $this->addon = strtolower(tostr($this->request->only(['plusname'])));
        $this->controller = strtolower(tostr($this->request->only(['controller'])));
        $this->action = strtolower(tostr($this->request->only(['action'])))?strtolower(tostr($this->request->only(['action']))):"index";
        // 是否自动转换控制器和操作名
        $convert = \think\Config::get('url_convert');

        // 生成view_path
        $view_path = $this->config['view_path'] ?: 'view';
        // 重置配置
        $this->request->module('');
        $this->view = $this->view->config('view_base', ADDON_PATH . $this->addon . DS . $view_path . DS);
    }

    /**
     * 加载模板输出
     * @access protected
     * @param string $template 模板文件名
     * @param array $vars 模板输出变量
     * @param array $replace 模板替换
     * @param array $config 模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $controller = Loader::parseName($this->controller);
        if ('think' == strtolower($this->config['type']) && $controller && 0 !== strpos($template, '/')) {
            $depr = $this->config['view_depr'];
            $template = str_replace(['/', ':'], $depr, $template);
            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                $template = str_replace('.', DS, $controller) . $depr . $this->action;
            } elseif (false === strpos($template, $depr)) {
                $template = str_replace('.', DS, $controller) . $depr . $template;
            }
        }
        $template = $template;
        // 实例化视图类
        $view = new \think\View([
            'type'          => 'think',
            'view_path'     => ROOT_PATH."plus/".$this->addon."/view/",
            'view_suffix'   => 'html',
            'view_depr'     => '/',
        ]);
        // show($template);
         // 渲染模板输出 并赋值模板变量
        return $view->fetch($template);
        // 
        // return parent::fetch($template, $vars, $replace, $this->config);
    }


    // final protected function theme($theme){
    //     $this->view->theme($theme);
    //     return $this;
    // }
    

    // final protected function assign($name,$value='') {
    //     $this->view->assign($name,$value);
    //     return $this;
    // }


    // //用于显示模板的方法
    // final protected function fetch($templateFile = 'widget'){
    //     if(!is_file($templateFile)){
        	
    //     	$config=$this->getConfig();
    //     	$theme=$config['theme'];
        	
    //     	$depr = "/";
        	
    //     	$theme=empty($theme)?"":$theme.$depr;
        	
    //         $templateFile = sp_add_template_file_suffix("./".$this->tmpl_root.$templateFile);
    //         if(!file_exists_case($templateFile)){
    //             throw new \Exception("模板不存在:$templateFile");
    //         }
    //     }
    //     return $this->view->fetch($templateFile);
    // }

    // final public function getName(){
    //     $class = get_class($this);
    //     return substr($class,strrpos($class, '\\')+1, -6);
    // }

    // final public function checkInfo(){
    //     $info_check_keys = array('name','title','description','status','author','version');
    //     foreach ($info_check_keys as $value) {
    //         if(!array_key_exists($value, $this->info))
    //             return false;
    //     }
    //     return true;
    // }

    // /**
    //  * 获取插件的配置数组
    //  */
    // public function getConfig($name=''){
    	
    //     static $_config = array();
    //     if(empty($name)){
    //         $name = $this->getName();
    //     }
    //     if(isset($_config[$name])){
    //     	return $_config[$name];
    //     }
        
    //     $config=M('Plugins')->where(array("name"=>$name))->getField("config");

    //     if(!empty($config) && $config!="null"){
    //         $config   =   json_decode($config, true);
    //     }else{
    //         $config=array();
    //         $temp_arr = include $this->config_file;
    //         if(!empty($temp_arr)){
    //             foreach ($temp_arr as $key => $value) {
    //                 if($value['type'] == 'group'){
    //                     foreach ($value['options'] as $gkey => $gvalue) {
    //                         foreach ($gvalue['options'] as $ikey => $ivalue) {
    //                             $config[$ikey] = $ivalue['value'];
    //                         }
    //                     }
    //                 }else{
    //                     $config[$key] = $temp_arr[$key]['value'];
    //                 }
    //             }
    //         }
            
    //     }
    //     $_config[$name]     =   $config;
    //     return $config;
    // }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
