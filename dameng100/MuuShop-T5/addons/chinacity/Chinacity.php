<?php
namespace addons\chinaCity;

use app\common\controller\Addons;
use think\Db;


    class Chinacity extends Addons{

        public $info = [
            //插件目录
            'name'=>'chinacity',
            //插件名
            'title'=>'中国省市区三级联动',
            //插件描述
            'description'=>'每个系统都需要的一个中国省市区三级联动插件。想天-駿濤修改，将镇级地区移除',
            //开发者
            'author'=>'muucmf',
            //版本号
            'version'=>'2.0'
        ];

        public function install(){

            /* 先判断插件需要的钩子是否存在 */
            $this->getisHook('J_China_City', $this->info['name'], $this->info['description']);

            //读取插件sql文件
            $sqldata = file_get_contents($this->addons_path.'install.sql');
            $sqlFormat = $this->sqlSplit($sqldata, config('database.prefix'));
            $counts = count($sqlFormat);
            
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                Db::execute($sql);
            }
            return true;
        }

        public function uninstall(){
            //读取插件sql文件
            $sqldata = file_get_contents($this->addons_path.'uninstall.sql');

            $sqlFormat = $this->sqlSplit($sqldata, config('database.prefix'), 'uninstall');
            $counts = count($sqlFormat);
            
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                Db::execute($sql);
            }
            return true;
        }

        //实现的J_China_City钩子方法
        public function Chinacity($param){

            $this->assign('param', $param);
            return $this->fetch('chinacity');
        }   
    }