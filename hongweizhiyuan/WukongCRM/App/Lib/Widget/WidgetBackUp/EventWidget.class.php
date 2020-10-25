<?php 

class EventWidget extends Widget {
	
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;	
		$where['owner_role_id'] = session('role_id');
		$list = M('Event')->where($where)->limit($limit)->select();
		foreach($list as $key=>$value){
			$list[$key]["owner"] = getUserByRoleId($value['owner_role_id']);
		}
		$data['list'] = $list;
		
		return $this->renderFile("index", $data);
	}
}
