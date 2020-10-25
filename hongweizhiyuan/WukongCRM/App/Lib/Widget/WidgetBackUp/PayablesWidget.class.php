<?php 

class PayablesWidget extends Widget {
	
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;	
		$where['is_deleted'] = 0;
		$where['owner_role_id'] = session('role_id');
		$data['list'] = D('PayablesView')->where($where)->order('payables.payables_id desc')->limit($limit)->select();
		
		return $this->renderFile("index", $data);
	}
}
