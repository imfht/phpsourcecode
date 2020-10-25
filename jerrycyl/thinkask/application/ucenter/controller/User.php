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


class User extends Base
{

    public function login()
    {
    	if(parent::auth('ucenter')->getUid()>0){
    		$url = $this->request->only(['gourl'])?$this->request->only(['gourl']):"";
    		$url = empty($url)?"/":encode($url);
    		$this->redirect($url);
    	}
      return $this->fetch('ucenter/logo');

    }
    public function reg(){
        if(getset('register_type')=="close"){
            $this->error2('网站已关闭注册,请联系管理员开通注册功能');
        }

    	return $this->fetch('ucenter/reg');
    }
    public function forget(){
       return $this->fetch('ucenter/forget'); 
    }
    /**
     * [agreement 协议]
     * @return [type] [description]
     */
    public function agreement(){
        $this->success2(getset('register_agreement'));
         // echo "<div style='width:500px;margin:0 auto;'>".."</div>";
    }
}
