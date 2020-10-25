<?php
namespace addons\demo;

use app\common\controller\Addons;
use think\Db;


    class Demo extends Addons{

        /**
         * 插件基础信息
         * @var [type]
         */
        public $info = [
            //插件目录（名称）
            'name'=>'demo',
            //插件名
            'title'=>'demo测试',
            //插件描述
            'description'=>'这就是个案例',
            //开发者
            'author'=>'muucmf',
            //版本号
            'version'=>'1.0.0'
        ];

        /**
         * [$admin description]
         * @var boolean
         */
        public $admin = true;

        public function install(){
    
            return true;
        }

        public function uninstall(){
            
            return true;
        }
        /**
         * 实现demo插件的默认方法
         * 任何钩子都可执行
         * @param  [type] $param [description]
         * @return [type]        [description]
         */
        public function run($param){

            $this->assign('param',$param);
            return $this->fetch('index/demo');
        }
        /**
         * 绑定实现插件的钩子
         * 绑定钩子，其它钩子无法执行
         * @return [type] [description]
         */
        public function demoTest(){
            return true;
        }
    }