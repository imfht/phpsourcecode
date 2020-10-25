<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class MsgcenterController extends UserController {
    //留言首页
    public function index(){ 
    	C('TOKEN_ON',false); //令牌验证 
    	$type = I('get.type');
    	$type = empty($type)? 'sys'  : $type;
    	if ($type == 'sys'){
    		$map['model_id']  = array('eq',1); //模型id 1-系统提示消息，2-消息，3-留言
    	}elseif($type == 'sms'){
    		$map['model_id']  = array('eq',2);
    	}elseif($type == 'message'){
    		$map['model_id']  = array('eq',3);    		
    	}
    	$map['take_user_id']  = UID;
    	$map['status'] = array('eq',1);
    	//$map['model_id']  = array('eq',3); //模型id 1-系统提示消息，2-消息，3-留言
    	$list = $this->lists('Message',$map,'id desc');
    	$m = M('Message');
		foreach ($list as $k => $v) {
			//获取回复
			$map['id'] = $v['id'];
			$m-> where($map)->setField('is_read',1);
			$reply = $m->field('content,post_time')->where(array('reply_msg_id'=>$v['id'],'status'=>array('neq',0)))->order('id desc')->select();
			if (is_array($reply)){
				$list[$k]['reply']= $reply;
			} 
		}
    	$this->meat_title = '消息中心 - '.C('WEB_SITE_TITLE');
    	$this->assign('list', $list);
    	$this->assign('type', $type);
		$this->display();
    }
    //提示消息
    public function info(){
    	$this->assign('meat_title', '消息');	
		$this->display();
    }
    
    //添加留言
    public function add(){
    	C('TOKEN_ON',false);//令牌验证
		if(IS_POST && IS_AJAX){
			if(UID){//验证登录
				if(true){//防刷下版完善
		            $Message = D('Message');	                      	            
		            if($data = $Message->create()){        		            	
		                if($Message->add()){
		                	$dat['info']   =  '操作成功';
		                	$dat['status'] =   1;
		                	$dat['content']   =  $data['content'];
		                    $this->ajaxReturn($dat);
		                } else {
		                    $this->error('操作失败');
		                }
		            } else {
		                $this->error($Message->getError());
		            }
		        } else {
		            $this->error('操作过于频繁，休息会再来操作！');
		        }
		    }else{
		    	$this->error('请登录后再操作！');
		    }
	    }else{
	    	$this->error('非法操作');
	    }
    }
    
    function getNewMsg() {
    	if(IS_POST && IS_AJAX){
    		$map['take_user_id']  = UID; //接收用户
    		$map['is_read']  = array('eq',0); //未读  
    		$map['status']  = array('eq',1); 
    		$list   = M('Message')->where($map)->field('title,content,model_id')->order('id desc')->select();	
    		$data['count'] = count($list);
    		//cookie('msgcount',$data['count']);
    		foreach ($list as $v) {
    			if( $v['title'] == null){$v['title'] = msubstr($v['model_id'],0,24);}
    		 	if($v['model_id'] == '1'){    		 		    		 		
    		 		$data['system'][] = $v['title'];    		 		
    		 	}elseif($v['model_id'] == '2'){
    		 		$data['sms'][] = $v['title']; 
    		 	
    		 	}elseif($v['model_id'] == '3'){
    		 		$data['message'][] = $v['title']; 
    		 	}
    		} 
    		//$data['msg'] = $msg;
    		$this->ajaxReturn($data);
    	}else{
	    	$this->error('非法操作');
	    }
    
    }
    
}