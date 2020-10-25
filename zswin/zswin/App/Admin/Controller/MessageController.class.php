<?php
namespace Admin\Controller;
class MessageController extends CommonController {
	
public function insert(){
	
	$data['sendtype']=I('sendtype',0);
	$data['at']=I('at','');
	$data['title']=I('title','');
	$data['content']=I('content','');
	
	
	
	
	if($data['content']==''){
		
		$this->mtReturn(300, '内容为空!');
	}
	if($data['sendtype']==0&&$data['at']==''){
	
		$this->mtReturn(300, '没有@任何人!');
	}
	
	if($data['sendtype']==0){
		//通知被@到的人
		
		$uids = get_at_uids($data['at']);
		$uids = array_unique($uids);
		
		
		$count=0;
		
		foreach ($uids as $uid) {
			
			$title = $data['title'];
			$message = $data['content'];
			if($uid>0){
				sendMessage($uid, 1, $title, $message,  0);
				$count=$count+1;
			}
			
			//
		}
		
		if($count==0){
			$this->mtReturn(300, '@的人不存在！');
		}elseif($count<count($uids)){
			
			$this->mtReturn(200, '发送消息成功,但@的用户中有'.(count($uids)-$count).'个不存在！');
		}else{
			$this->mtReturn(200, '发送消息成功!');
		}
		
	}else{
		
		$uids=D('member')->where(array('status'=>1))->getField('uid',true);
		$uids = array_subtract($uids, array(1));
		foreach ($uids as $uid) {
				
			$title = $data['title'];
			$message = $data['content'];
			
			sendMessage($uid, 1, $title, $message,  0);
		}
	}
	$this->mtReturn(200, '发送消息成功!');
	
	
}
	
}

?>