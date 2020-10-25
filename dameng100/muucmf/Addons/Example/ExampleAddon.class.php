<?php

namespace Addons\Example;
use Common\Controller\Addon;

/**
 * 示列插件
 * @author 无名
 */

    class ExampleAddon extends Addon{

        public $info = array(
            'name'=>'Example',
            'title'=>'示列',
            'description'=>'这是一个临时描述',
            'status'=>1,
            'author'=>'无名',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }


    }