<?php
/**
 * php 模板引擎
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-1-19
 */
namespace framework\core;
use framework\libraries\Dir;
use Templi;

/**
 * Class View
 * @package framework\core
 * @property string $viewDir
 * @property string $compileDir
 *
 */
class View extends Object
{
    //模板变量
    private $viewVar = array();

    private $_viewDir ='';
    private $_compileDir ='';

    function __construct()
    {
        $this->setViewDir(Templi::getApp()->appPath.'views/');
        $this->setCompileDir(Templi::getApp()->appPath.'cache/compile/');
    }
    /**
     * 设置模板路径
     * @param string $template
     */
    public function setViewDir($template){
        $this->_viewDir = rtrim($template,'/\\').'/';
    }

    /**
     * @return string
     */
    public function getViewDir()
    {
        return $this->_viewDir;
    }
    /**
     * 设置编译缓存路径
     * @param string $compile
     */
    public function setCompileDir($compile){
        $this->_compileDir = rtrim($compile,'/\\').'/';
    }

    /**
     * @return string
     */
    public function getCompileDir()
    {
        return $this->_compileDir;
    }
    /**
     * 分配变量 变量批量分配
     * @param array $data 变量名
     */
    public function setOutput($data){
        if (!is_array($data)) return;
        $this->viewVar = array_merge($this->viewVar,$data);
    }

    /**
     * 分配变量 单个变量分配
     * @param string $name 变量名
     * @param mixed|string $value 变量值
     */
    public function assign($name, $value=''){
        $this->viewVar[$name] = $value;
    }

    /**
     * 页面显示
     * @param string $template_file_name 模板文件名(不包括扩展名)
     */
    public function display($template_file_name=null){
        ob_start();
        $template = $this->_compile($template_file_name);
        extract($this->viewVar, EXTR_OVERWRITE);
        require($template);
        ob_end_flush();
    }

    /**
     * 获取渲染编译模板后的内容
     * @param string $template_file_name
     * @return string
     */
    public function render($template_file_name){
        ob_start();
        $template = $this->_compile($template_file_name);
        extract($this->viewVar, EXTR_OVERWRITE);
        require($template);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    /**
     * 载入 模板缓存文件
     * @param string $template_file_name
     * @return string
     */
    public function loadView($template_file_name=null){
        return $this->_compile($template_file_name);
    }

    /**
     * 模板编译 并缓存
     * @param string|null $template_file_name
     * @return string
     * @throws Abnormal
     */
    private function _compile($template_file_name=null){
         //视图文件名
        $file_info = pathinfo($template_file_name);
        $template_file = $file_info['extension'] ? $template_file_name : $template_file_name.'.html';
        //视图文件路径
        $template_file_path = $this->viewDir;
        $compile_file_name = $file_info['extension']?($file_info['dirname'].'/'.$file_info['filename']):$template_file_name;
        //编译文件
        $compile_file = $this->compileDir.$compile_file_name.'.tpl.php';

        if(!file_exists($template_file_path.$template_file)){
            if(APP_DEBUG)
                throw new Abnormal($template_file_path.$template_file.'模板文件不存在', 500);
            else
                Common::show404();
        }
        if(!file_exists($compile_file) || filemtime($compile_file) < filemtime($template_file_path.$template_file)){
            $content   = file_get_contents($template_file_path.$template_file, filesize($template_file_path.$template_file));
            $content = $this->_replaceTag($content);
            Dir::dirCreate(dirname($compile_file));
            file_put_contents($compile_file,$content);
            return $compile_file;
        }else{
            return $compile_file;
        }
    }
    /**
     * 标签替换
     */
    private function _replaceTag($str){
        if(empty($str)) return;
        $find_tag  = array(
            0=>'/\{loop\s+?(\$\S+?)\s+?(\$\S+?)\}/i',     // foreach 循环
            1=>'/\{loop\s+?(\$\S+?)\s+?(\$\S+?)\s+?(\$\S+?)\}/i',   //foreach 循环
            2=>'/\{\/loop\}/i',   //foreach 循环结束标签
            3=>'/\{if\s+?(.+?)\}/i',  //if 标签
            4=>'/\{else\}/i',  //else 标签
            5=>'/\{elseif\s+?(.+?)\}/i', //elseif标签
            6=>'/\{\/if\}/i', //if 结束标签
            7=>'/\{php\s+?(.+?)\}/i',   //php源代码标签
            8=>'/\{include\s+?(.+?)\}/i', //include
            9=>'/\{\s*?([a-zA-Z_\x7f-\xff][a-zA-Z_0-9:\x7f-\xff]*?\([^{}]*?\))\s*?\}/i',//函数
            10=>'/\{\s*?(\$[a-zA-Z_\x7f-\xff][a-zA-Z_0-9\x7f-\xff]*?([^{}]+?))\s*?\}/i',//数组变量
            11=>'/\{\s*?(\$[a-zA-Z_\x7f-\xff][a-zA-Z_0-9\x7f-\xff]*?)\s*?\}/i',//变量
            12=>'/\{\s*?([A-Z_]+?)\s*?\}/', //常量
            13=>'/\{template\s+?file=[\"\']?([\w\/]+)[\"\']?\s+?module=[\"\']?(\w+)[\"\']?\}/',//载入模板文件
            14=>'/\{template\s+?file=[\"\']?([\w\/]+)[\"\']?\}/',//载入模板文件
        );
        $replace_tag = array(
            0=>'<?php $n=1; if(is_array(\\1))foreach(\\1 as \\2):?>', //foreach 循环
            1=>'<?php $n=1; if(is_array(\\1))foreach(\\1 as \\2=>\\3):?>', //foreach循环
            2=>'<?php $n++;endforeach;unset($n);?>',//foreach 循环结束标签
            3=>'<?php if(\\1):?>',//if 
            4=>'<?php else:?>',// else
            5=>'<?php elseif(\\1):?>',//elseif
            6=>'<?php endif?>',//if 结束标签
            7=>'<?php \\1;?>',  //php 源代码标签
            8=>'<?php include \\1;?>',//include
            9=>'<?php echo \\1;?>',//函数显示
            10=>'<?php echo \\1;?>',//数组变量
            11=>'<?php echo \\1;?>',//变量
            12=>'<?php echo \\1;?>', //常量
            13=>'<?php include framework\\Templi::getAPP()->load->view("\\1","\\2");?>',
            14=>'<?php include framework\\Templi::getAPP()->load->view("\\1");?>',
        );
        
        $str = preg_replace($find_tag,$replace_tag,$str);
        return $str;
    }
}