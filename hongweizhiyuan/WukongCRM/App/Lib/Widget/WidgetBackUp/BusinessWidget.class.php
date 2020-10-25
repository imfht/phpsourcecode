<?php 

class BusinessWidget extends Widget 
{
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;	
		$where['owner_role_id'] = session('role_id');
		$where['is_deleted'] = 0;
		
		$business_list = M('Business')->where($where)->order('update_time desc')->limit($limit)->select();
		foreach($business_list as $k=>$v) {
			$business_list[$k]['status'] = M('BusinessStatus')->where('status_id = %d', $v['status_id'])->getField('name');
			$business_list[$k]['customer'] = M('Customer')->where('customer_id = %d', $v['customer_id'])->find();
		}
		$data['list'] = $business_list;
		return $this->renderFile ("index", $data);
	}
}