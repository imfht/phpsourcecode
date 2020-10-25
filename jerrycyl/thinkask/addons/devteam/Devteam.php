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

namespace addons\devteam;
use app\common\controller\Addons;

/**
 * 开发团队信息插件
 * @author thinkphp
 */

class Devteam extends Addons{

    public $info = array(
        'name'=>'Devteam',
        'title'=>'开发团队信息',
        'description'=>'开发团队成员信息',
        'status'=>1,
        'author'=>'molong',
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
        echo "string";
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
        // if($config['display']){
            // $this->template('widget');
        // }
    }
}