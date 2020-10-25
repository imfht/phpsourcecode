<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;

/**
 * 后台信息留言控制器
 * @author JYmusic
 */
class MessageController extends AdminController {
	public function index ($type=null) {
		if(isset($content)){
            $map['content']   =   array('like', '%'.$title.'%');
        }
        if(isset($status)){
            $map['status']  =   $status;
        }else{
            $map['status']  =   array('in', '0,1,2');
        }
       	if(!empty($type)){
            $map['type']   =   I('get.type');
        }
        
		$list   = $this->lists('Message', $map);
        //int_to_string($list);
        $this->assign('list', $list);
        $this->meta_title = '信息管理';
        $this->display();			
	}
	
	public function add(){
		if(IS_POST){
            $Message = D('Message');
            $data = $Message->create();                     
            if($data){
            	$uid = @explode(",",$data['take_user_id']);				
				if(count($uid) > 0){
					for( $i=0;$i<count($uid);$i++){
	 					$data['take_user_id'] = $uid[$i];
						$Message->add($data);
					}
					$this->success('操作成功！');           		
            	}else{
            		$this->error('用户ID格式错误');           		
            	}
            } else {
                $this->error($Message->getError());
            }
        } else {
			$this->meta_title = '添加信息';
			$this->display();
        }			
	}
	
	public function del(){
        $id = array_unique((array)I('ids',0));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
		//dump($id);
        $map = array('id' => array('in', $id) );
        if(M('Message')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            $data['status']  = 1;
            $data['info'] = '删除成功';
        } else {
        	$data['status']  = 0;
            $data['info'] = '删除失败！';
        }
        $this->ajaxReturn($data);
    }
	
	//更改信息状态
    public function setStatus () {
    	    	
    	return parent::setStatus('Message');
    }

}
