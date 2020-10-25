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
namespace app\wx\controller;
use app\common\controller\Base;
class index extends Base
{
    private $accounts_id;
	public function _initialize()
    {
         $data = $this->request->param();
         $accounts_id = $data['accounts_id'];
         if(!$accounts_id) die('没有指定accounts_id');
         $this->accounts_id = $accounts_id;
        
    }
     /**
     * [validate 验证微信信息]
     * @return [type] [description]
     */
    public function validatewx(){
        $options = $this->f_accounts_id_t_info($this->accounts_id);
        $weObj = new \Wechat($options);
         $weObj->valid();
        // return $this->fetch();

    }

    /**
     * [auth_login 授权登陆]
     * @return [type] [description]
     */
    public function auth_login(){

    }
   
    /**
     * [f_accounts_id_t_info 根据accounts_id查出相关的信息]
     * @return [type] [description]
     */
    private function f_accounts_id_t_info($accounts_id){
        return model('base')->getone('weixin_accounts',['where'=>['id'=>$accounts_id]]);
    }




}
