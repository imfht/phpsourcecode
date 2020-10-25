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
|   权限管理
+---------------------------------------------------------------------------
 */
namespace app\admin\controller;
use app\common\controller\AdminBase;
class Auth extends AdminBase
{
 /**
  * [index 用户管理]
  * @return [type] [description]
  */
  public function edit(){
    $topmenuname = $data['topmenuname']?$data['topmenuname']:"home_action";
    //菜单
    $defaultmenu = config('adminmenu');
    // show($defaultmenu);
    // die;
    $Models = finddirfromdir(APP_PATH."../application");
    $no_join = ['ajax','index','asset','common','post','ucenter','admin'];
    //模型
    if($topmenuname=="admin_content_model"){
        foreach ($Models as $k => $v) {
          $path = APP_PATH."../application/".$v."/menu.php";
          if(!in_array($v, $no_join)&&file_exists($path)){
            $menu= include($path);
            $defaultmenu['adminmenu']['child'][]=$menu['adminmenu'];
            
          }
      }
    }
    $this->assign('defaultmenu',$defaultmenu);
    // show($defaultmenu);

    return $this->fetch('admin/auth/edit');
  }

  
  

}
