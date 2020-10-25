<?php
namespace Addons\ChinaCity;
use Common\Controller\Addon;

/**
* 中国省市区三级联动插件
*/

class ChinaCityAddon extends Addon{

    public $info = array(
        'name'=>'ChinaCity',
        'title'=>'中国省市区三级联动',
        'description'=>'每个系统都需要的一个中国省市区三级联动插件。',
        'status'=>1,
        'author'=>'tensent',
        'version'=>'2.0'
    );

    public function install(){
        /* 先判断插件需要的钩子是否存在 */
        $this->getisHook('J_China_City', $this->info['name'], $this->info['description']);

        //读取插件sql文件
        $sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/install.sql');
        $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
        $counts = count($sqlFormat);
        
        for ($i = 0; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            D()->execute($sql);
        }
        return true;
    }

    public function uninstall(){
        //读取插件sql文件
        $sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/uninstall.sql');
        $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
        $counts = count($sqlFormat);
         
        for ($i = 0; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            D()->execute($sql);
        }
        return true;
    }

    //实现的J_China_City钩子方法
    public function J_China_City($param){
        $this->assign('param', $param);
        $this->display('chinacity');
    }
}