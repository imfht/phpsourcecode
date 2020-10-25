<?php 

class TaskWidget extends Widget {
	
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;	
		
		$where['isclose'] = 0;
		$where['status'] = array('neq','完成');
		$where['owner_role_id'] = session('role_id'); 
		
		$data['list'] = M('Task')->where($where)->limit($limit)->select();
		
		return $this->renderFile("index", $data);
	}
}
