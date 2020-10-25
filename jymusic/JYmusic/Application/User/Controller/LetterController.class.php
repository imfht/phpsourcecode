<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class LetterController extends UserController {
    //私信首页
    public function index($uid=0){ 
    	C('TOKEN_ON',false); //令牌验证 
    	$uid = intval($uid);
    	$m = M('Message');
    	$map['type']  = array('eq','letter');
    	$map['to_uid']  = UID;
    	if ($uid){	//获取当前对话    	
	    	$map['post_uid']  = $uid;
	    	$list = $this->lists('Message',$map,'id desc');
	    	foreach ($list as $k => $v) {
				//获取回复
				$map['id'] = $v['id'];
				$m-> where($map)->setField('is_read',1);
				$reply = $m->field('content,post_time')->where(array('reply_id'=>$v['id']))->order('id desc')->select();
				if (is_array($reply)){
					$list[$k]['reply']= $reply;
				} 
			}
			// 记录当前列表页的cookie
        	Cookie('__forward__',$_SERVER['REQUEST_URI']);
		}else{	   
	    	$list = $this->lists('Message',$map,'id desc');
		}
		
		$this->assign('list', $list);	
    	$this->meat_title = '我的私信 - '.C('WEB_SITE_TITLE');   	 	    	
    	$this->display();
    }    
    
    //添加私信
    public function post(){
    	C('TOKEN_ON',false);//令牌验证    	
		if( IS_AJAX){	
			if(IS_POST){
				//防刷
				if (I('post.toid') == UID ) $this->error('接收用户不正确！');								
				$Message = D('Message');				
				if($Message->checkpost()){//防刷下版完善		            	                      	            
		            if($data = $Message->create()){  		            	
		            	$data['type'] = 'letter';
		            	$data['title'] =$data['post_uname'].'给您发送了一条私信！'; 		            	
		               	if($Message->add($data)){
		                	$return['info']   =  '私信发送成功！';
		                	$return['status'] =   1;
		                	$return['url'] =   Cookie('__forward__');
		                    $this->ajaxReturn($return);
		                } else {
		                    $this->error('操作失败');
		                }
		            } else {
		                $this->error($Message->getError());
		            }
		        } else {
		            $this->error('操作过于频繁，休息会再来！');
		        }
		    }elseif(IS_GET){
		    	$this->show(':Ajaxget/postLetter');
		    }
	    }else{
	    	$this->display();
	    }
    }
    
   public function read($id=0) {
    	$id = intval($id);
    	if($id){
    		$data = M("Message")->where('id='.$id)->delete();
    		if($data){
    			$this->success('操作成功！');
    		}else{
    			$this->error('操作失败！');	
    		}
    	}else{
    		
    		$this->error('参数错误！');	
    	}    	   
   }
        
    public function remove($id=0) {
    	$id = intval($id);
    	if($id){
    		$data = M("Message")->where('id='.$id)->delete();
    		if($data){
    			$this->success('操作成功！');
    		}else{
    			$this->error('操作失败！');	
    		}
    	}else{
    		
    		$this->error('参数错误！');	
    	}    	   
   }
    
    public function removeall() {
    	$id = UID;
    	if($id){
    		$map['to_uid'] = $id;
    		$map['type'] = 'letter';
    		$data = M("Message")->where($map)->delete();
    		if($data){
    			$this->success('操作成功',U('Letter/index'));
    		}else{
    			$this->error('操作失败！');	
    		}
    	}else{
    		
    		$this->error('参数错误！');	
    	}    	   
   }
       
}