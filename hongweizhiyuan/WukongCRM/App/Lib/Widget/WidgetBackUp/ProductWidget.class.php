<?php 

class ProductWidget extends Widget {
	
	public function render($data)
	{
		$data['class'] = $data['style'] == 1 ? 'span12' : 'span6';
		$limit = $data['limit'] > 0 ? intval($data['limit']) : 10;	
		$data['list'] = D('ProductView')->order('product_id desc')->limit($limit)->select();
		
		return $this->renderFile("index", $data);
	}
}
