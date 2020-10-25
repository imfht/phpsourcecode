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
namespace app\admin\controller;
use app\common\controller\AdminBase;
class Addon extends AdminBase{
 public function lists(){
    	$plus = finddirfromdir("./../plus");
    	$plu = [];
    	foreach ($plus as $k => $v) {
    		$config = include(PLUS_PATH.$v.'/config.php');
    		$plu[$k]['name'] = $config['system']['name'];
    		$plu[$k]['install'] = file_exists("./../plus/".$v."/install.lock")?"Y":"N";
    		$plu[$k]['view'] = $config['system']['logo']?$config['system']['logo']:"/static/images/icon/plus.png";

    	}
    	$this->assign('plu',$plu);
    return $this->fetch('admin/addon/list');
  }
  public static function test(){
    echo "string";
  }


}