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
class Ajax extends Base
{
	public function _initialize()
    {
        // $this->base = 
    }
    //添加授权列表
    public function auth(){
    	if($this->request->isAJax()){
            $param = $this->request->param();
    		if(!$param['token']||!$param['encodingaeskey']||!$param['appid']||!$param['appsecret']) $this->error('token,encodingaeskey,appid,appsecret这几项不能为空，会影响授权!');
            if(!$param['name']) $this->error('名字不能为空，会影响标识！');
            $returnurl = $param['returnurl']?$param['returnurl']:url('wx/admin/auth');
    		if($param['id']){
                //修改
                $id = $param['id'];
                unset($param['id']);
                $param['edittime'] = time();
                model('base')->getedit("weixin_accounts",['where'=>["id"=>$id]],$param);
                $this->success('修改成功',$returnurl);

            }else{
                $param['time'] = time();
                if(model('base')->getadd('weixin_accounts',$param)){
                    
                    $this->success('添加成功',$returnurl);
                }else{
                    $this->error('服务器忙，请稍后再试');
                }
            }

    	}
    }
    /**
     * [savemenu description]
     * @return [type] [description]
     */
    public function savemenu(){
       if($this->request->isAJax()){
        $param = $this->request->param();
        $id = $param['id'];
        $accounts_id = $param['accounts_id'];
        unset($param['id']);
        unset($param['accounts_id']);
       model('base')->getedit('weixin_menu',['where'=>['id'=>$id,'accounts_id'=>$accounts_id]],$param);
       $this->success('修改成功');


       }
    }




}
