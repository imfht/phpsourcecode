<?php

namespace Addons\ExecuteSql;
use Common\Controller\Addon;

/**
 * 执行sql语句插件
 * @author 翟小斐
 */

    class ExecuteSqlAddon extends Addon{

        public $info = array(
            'name'=>'ExecuteSql',
            'title'=>'执行sql语句',
            'description'=>'用于执行sql语句,处于安全考虑请在需要是安装该插件,使用完成后请卸载或删除',
            'status'=>1,
            'author'=>'翟小斐',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的AdminIndex钩子方法
        public function AdminIndex($param){
            $config = $this->getConfig();
            $this->assign('addons_config', $config);
            if($config['display']){
                $this->display('widget');
            }
        }

    }