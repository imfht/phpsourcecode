<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class MessageController extends UserController {
    //消息首页
    public function index(){ 
    	C('TOKEN_ON',false); //令牌验证 
    	$m = M('Message');
    	//删除 三个月前数据
    	$time = strtotime("-3 month");
    	$m->where(array('to_uid'=>UID,'post_time'=>array('gt',$time)));
    	$map['type']  = array('neq','letter'); // $type 消息类型 system系统，letter私信，app应用
    	$map['to_uid']  = UID;
    	$list = $this->lists('Message',$map,'id desc');
		if (!empty($list)){
			foreach ($list as $k => $v) {
				$map['id'] = $v['id'];
				$m-> where($map)->setField('is_read',1);
			}
		}
    	$this->meat_title = '消息中心 - '.C('WEB_SITE_TITLE');
    	$this->assign('list', $list);
    	$this->display();
    } 
    
   public function remove(){ 
    	$id = intval(I('id'));   	
    	$return = M('Message')->where(array('id'=>$id))->delete();
    	if ($return){
    		$this->success('操作成功',U('Message/index'));
    	}else{
    		$this->error('操作失败！');
    	}
    }
    
    public function removeall() {
    	$id = UID;
    	if($id){
    		$map['to_uid'] = $id;
    		$map['type'] = array('neq','letter');;
    		$data = M("Message")->where($map)->delete();
    		if($data){
    			$this->success('操作成功',U('Message/index'));
    		}else{
    			$this->error('操作失败！');	
    		}
    	}else{
    		
    		$this->error('参数错误！');	
    	}    	   
   }
    
}