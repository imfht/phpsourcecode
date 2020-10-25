<?php 

class KnowledgeWidget extends Widget {
	
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;	
		$data['list'] = D('KnowledgeView')->order('knowledge_id desc')->limit($limit)->select();
		$userRole = M('userRole');
		foreach($data['list'] as $k => $v){
			$data['list'][$k]['owner'] = getUserByRoleId($v['role_id']);
		}
		
		return $this->renderFile("index", $data);
	}
}
