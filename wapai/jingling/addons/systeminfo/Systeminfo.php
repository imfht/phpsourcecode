<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
 
namespace addons\systeminfo;
use app\common\controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

    class Systeminfo extends Addon{

        public $info = array(
            'name'=>'systeminfo',
            'title'=>'系统环境信息',
            'description'=>'用于显示一些服务器的信息',
            'status'=>1,
            'author'=>'thinkphp',
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
                return $this->fetch('widget');
            }
        }
    }