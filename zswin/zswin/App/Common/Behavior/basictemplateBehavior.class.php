<?php
namespace Common\Behavior;
use Think\Behavior;

defined('THINK_PATH') or exit();

class basictemplateBehavior extends Behavior {

   protected $basic='default';

    public function run(&$templateFile){
        // 自动定位模板文件
        
        if(!file_exists_case($templateFile)) {
        	 
        	
            $templateFile   = $this->parseTemplateFile($templateFile);
            
        }   
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param string $templateFile 文件名
     * @return string
     */
    private function parseTemplateFile($templateFile) {
        //var_dump($templateFile);
    if(MODULE_NAME=='Admin'){
        	return '';
        }
        
        if(''==$templateFile) {
              // 如果模板文件名为空 按照默认规则定位
            $templateFile = C('TEMPLATE_NAME');
            
           
           
            if(!file_exists_case($templateFile) && C('DEFAULT_THEME') && $this->basic) {
                //如果定义了主题，不存在则找项目缺省主题目录寻找
                $default_theme = C('DEFAULT_THEME');
                
                 
                
                $theme_path = C('VIEW_PATH') . $default_theme . '/';
                
                 
                $templateFile = $theme_path.CONTROLLER_NAME.'/'.ACTION_NAME.C('TMPL_TEMPLATE_SUFFIX');
               
               if(!file_exists_case($templateFile)) {
               	
               	$theme_path = C('VIEW_PATH') . $this->basic . '/';
               $templateFile = $theme_path.CONTROLLER_NAME.'/'.ACTION_NAME.C('TMPL_TEMPLATE_SUFFIX');
            }
           
            }
        } elseif(false === strpos($templateFile,C('TMPL_TEMPLATE_SUFFIX'))){
            // 解析规则为 模板主题:模块:操作 不支持 跨项目和跨分组调用
            $path   =  explode(':',$templateFile);
            $action = array_pop($path);
            $module = !empty($path)?array_pop($path):CONTROLLER_NAME;
            if(!empty($path)) {// 设置模板主题
                $path = C('VIEW_PATH').array_pop($path).'/';
            }else{
                $path = C('VIEW_PATH');
            }
            $depr = '/';
            $templateFile  =  $path.$module.$depr.$action.C('TMPL_TEMPLATE_SUFFIX');
            if(!file_exists_case($templateFile) && C('DEFAULT_THEME') && $this->basic) {
                //如果定义了主题，不存在则找项目缺省主题目录寻找
                $path = C('VIEW_PATH') . $this->basic . '/';
                $templateFile = $path.$module.$depr.$action.C('TMPL_TEMPLATE_SUFFIX');
            }
        }
        
        
        if(!file_exists_case($templateFile)) {
           echo '模板不存在';
        }
       // dump($templateFile);
        return $templateFile;
    }
 
}