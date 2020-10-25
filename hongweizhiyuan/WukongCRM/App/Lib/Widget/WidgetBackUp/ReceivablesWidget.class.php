<?php 

class ReceivablesWidget extends Widget {
	
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;	
		$where['is_deleted'] = 0;
		$where['owner_role_id'] = session('role_id');
		$data['list'] = D('ReceivablesView')->where($where)->order('receivables.receivables_id desc')->limit($limit)->select();
		
		return $this->renderFile("index", $data);
	}
}
