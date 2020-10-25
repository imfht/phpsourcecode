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
namespace app\Question\controller;
use app\common\controller\Base;
class Post extends Base
{
public function _initialize(){
    parent::_initialize();
	 if(parent::getUid()){
	            
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
            $this->assign(model('Base')->getone('question',['where'=>"question_id={$id}",'cache'=>false]));
           
        }else{
        	$this->assign('uid',session('thinkask_uid'));
        }
       return $this->fetch('question/post/edit');  
        
    }
 /**
     * [editanswer 编辑回答]
     * @Author   Jerry
     * @DateTime 2017-05-01
     * @Example  eg:
     * @return   [type]     [description]
     */
    public function editanswer(){
        $answer_id = (int) decode(input('answer_id'));
        //如果不是本人，或者超给管理员。抛出错误
        $answer_info = $this->getbase->getone('answer',['where'=>['answer_id'=>$answer_id]]);
        if($answer_info['uid']==parent::getUid()||in_array(parent::getUid(), config('super_manager'))){
            $this->assign($answer_info);
        }else{
            $this->error('您只能修改自已的内容!');
        }
      return $this->fetch('question/post/editanswer'); 
    }

}
