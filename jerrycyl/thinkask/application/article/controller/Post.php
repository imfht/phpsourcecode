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
namespace app\article\controller;
use app\common\controller\Base;
class Post extends Base
{
public function _initialize(){
    parent::_initialize();
	 if(session('thinkask_uid')){
	            
	            // $userinfo = model('users')->getUserByUid(session('thinkask_uid'));
	            // $this->assign('userinfo',$userinfo);
		     	//分类
		     	// $this->assign('category',model('Category')->getall());
			      //分组
			    // $this->assign('group',model('Group')->getall());
	        }else{
	            $this->error('请先登陆');
	     }
       
    }
 public function edit()
    {
    	$this->assign('category',$this->getbase->getall('category'));
        $id=is_array($this->request->only(['id']))?current($this->request->only(['id'])):0;
        if($id>0){
            $this->assign(model('Base')->getone('article',['where'=>"question_id={$id}",'cache'=>false]));
            // show(model('Base')->getone('question',['where'=>"question_id={$id}",'cache'=>false]));
        }else{
        	$this->assign('uid',session('thinkask_uid'));
        }
       return $this->fetch('article/post/edit');  
        
    }

}
