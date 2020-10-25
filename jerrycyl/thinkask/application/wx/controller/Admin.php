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
class Admin extends Base
{
	public function _initialize()
    {
      $this->is_admin();  
    }
    //微信公众号列表
    public function auth(){
        $auth = model('base')->getall('weixin_accounts');
        $this->assign('auth',$auth);
        return $this->fetch(); 
    }
    //修改公众号列表
    public function edit(){
     $param = $this->request->param();
     $id = (int)$param['id'];
        if($id){
            $this->assign(model('base')->getone('weixin_accounts',['where'=>['id'=>$id]]));
        }

        return $this->fetch(); 
    }
    //功能管理
    public function fun(){
        $param = $this->request->param();
        $accounts_id =(int)$param['accounts_id'];
        
        if($accounts_id){
          $this->assign('accounts_info',$this->find_accounts_by_id($accounts_id));  
        }
        
        //所有公众号
        $gzh = model('base')->getall('weixin_accounts',['field'=>"id,name,"]);
        $this->assign('gzh',$gzh);

        return $this->fetch(); 
    }
    //当建立微信公众号的时候，就新建默认的微信公众号
    private function creatDefaultMenu($accounts_id){
        //一级菜单
        $arr = array(1,2,3);
       foreach ($arr as $key => $v) {
             //父级
            $data = [
                'parentid'=>0,
                'type'=>"",
                'msg'=>"",
                'url'=>'',
                'accounts_id'=>$accounts_id,
                'name'=>'菜单'.$v,
                ];
            $parentid = model('base')->getadd('weixin_menu',$data);
               //子级
            for ($i=0; $i < 5; $i++) { 
                $data = [
                'parentid'=>$parentid,
                'type'=>"url",
                'msg'=>"",
                'url'=>'',
                'accounts_id'=>$accounts_id,
                'name'=>'子菜单'.$i,
                ];
                model('base')->getadd('weixin_menu',$data);
            }
       }
    }
    //菜单管理
    public function menu(){
        $param = $this->request->param();

        $accounts_id = (int)$param['accounts_id'];
        // show($accounts_id);
        $id = (int)$param['id'];
        $math = $param['math'];
        if(!$accounts_id){
            $this->error('请先选择公众号');
        }else{
            
            
            $notice = $nowmenu['parentid']>0?"字数不超过8个汉字或16个字母":"字数不超过4个汉字或8个字母";
            $this->assign('notice',$notice);
            //如果当前公众号菜单存在就用当前的，不存在就用生成个默认的
            $wxmenu = model('base')->getall('weixin_menu',['where'=>['accounts_id'=>$accounts_id]]);
            if(!$wxmenu){
               $this->creatDefaultMenu($accounts_id);
               $wxmenu = model('base')->getall('weixin_menu',['where'=>['accounts_id'=>$accounts_id]]);
            }
            // show($id);
            // show($accounts_id);
            $nowmenu = model('base')->getone('weixin_menu',['where'=>['id'=>$id,'accounts_id'=>$accounts_id]]);
            // show($nowmenu);
            // show($wxmenu);
            $wxmenu = treeMenus($wxmenu,0);
           // show($wxmenu);
            $this->assign('nowmenu',$nowmenu);
            $this->assign('wxmenu',$wxmenu);
            $this->assign('math',$math);
            $this->assign('accounts_id',$accounts_id);
            $this->assign('id',$id);
        }

     return $this->fetch(); 
    }
    private function find_accounts_by_id($id){
    
        return model('base')->getone('weixin_accounts',['where'=>['id'=>$id]]);
    }
    // public function defaults(){
    //   return $this->fetch();   
    // }

  



}
