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
namespace app\common\behavior;
use app\common\controller\Base;
use app\common\Auth;
class InitAuth extends Base
{
    public function run(&$params)
    {
    $this->assign('module',$this->request->module());
    $this->assign('controller',$this->request->controller());
    $this->assign('action',$this->request->action());

    // $rbac = \app\common\auth\Auth::Auth('Rbac')->auth();
    if($userinfo = \app\common\auth\Auth::Auth('ucenter')->getUser()){
    	$this->assign('userinfo',$userinfo);
    }
    // if(is_array($rbac)&&!$rbac['status']){
        // $this->error($rbac['msg'],$rbac['url']);
    // }
   }


}