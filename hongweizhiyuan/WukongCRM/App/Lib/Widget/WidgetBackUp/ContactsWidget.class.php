<?php 

class ContactsWidget extends Widget {
	
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;
		$where['owner_role_id'] = session('role_id');
		$where['is_deleted'] = 0;
		$data['list'] = M('Contacts')->where($where)->limit($limit)->select();
		
		return $this->renderFile("index", $data);
	}
}
