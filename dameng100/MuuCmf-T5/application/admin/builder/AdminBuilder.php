<?php
namespace app\admin\builder;

use think\Controller;
use think\Request;
use think\Db;
use app\admin\model\AuthGroup;
use app\admin\Controller\Admin;

/**
 * AdminBuilder：快速建立管理页面。
 * Class AdminBuilder
 * @package Admin\Builder
 */
abstract class AdminBuilder extends Admin
{

    public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        //获取模版的名称
        $template = APP_PATH . 'admin' .DS. 'view' .DS. 'builder'. DS . $templateFile . '.html';
        //显示页面
        //echo $this->fetch($template);
        //dump($templateFile);
        //dump($this->fetch('admin@builder/'.$templateFile));
        echo $this->fetch('admin@builder'.DS.$templateFile);
    }

    protected function compileHtmlAttr($attr) {
        $result = array();
        
        if(is_array($attr)){
            foreach($attr as $key=>$value) {
                $value = htmlspecialchars($value);
                $result[] = "$key=\"$value\"";
            }
            $result = implode(' ', $result);
        }
        
        return $result;
    }
}

