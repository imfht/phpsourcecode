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
namespace app\common\controller;
use app\common\controller\Base;
class AdminBase extends Base
{
  public function _initialize() {
    parent::_initialize();
     $rbac = \app\common\auth\Auth::Auth('Rbac')->auth();
    if($admininfo = \app\common\auth\Auth::Auth('ucenter')->getAdminInfo()){
    	$this->assign('userinfo',$admininfo);
    }
    if(is_array($rbac)&&!$rbac['status']){
        $this->error($rbac['msg'],$rbac['url']);
    }
 
   
  }

}
