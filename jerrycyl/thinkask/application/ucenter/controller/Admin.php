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
namespace app\ucenter\controller;
use app\common\controller\Base;


class Admin extends Base
{

    public function login()
    {
    	// if(parent::auth('ucenter')->getUid()>0){
    		// $url = $this->request->only(['gourl'])?$this->request->only(['gourl']):"";
    		// $url = empty($url)?"/":encode($url);
    		// $this->redirect($url);
    	// }
      return $this->fetch('ucenter/admin/login');

    }
   
}
