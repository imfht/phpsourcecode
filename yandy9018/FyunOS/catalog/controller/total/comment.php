<?php 
class ControllerTotalComment extends Controller {
	public function index() {
		$this->language->load('total/comment');
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['entry_comment'] = $this->language->get('entry_comment');
		
		$this->data['button_comment'] = $this->language->get('button_comment');
				
		if (isset($this->session->data['comment'])) {
			$this->data['comment'] = $this->session->data['comment'];
		} else {
			$this->data['comment'] = '';
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/total/comment.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/total/comment.tpl';
		} else {
			$this->template = 'default/template/total/comment.tpl';
		}
					
		$this->render();
  	}
  	
  	public function add() {
	  	$this->language->load('total/comment');
	  	$this->load->model('checkout/order');		
	  	
	  	$json = array();
	  	
	  	if (!$this->cart->hasProducts()) {
	  		$json['redirect'] = $this->url->link('checkout/cart');
	  	}	
	  	
  		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->session->data['comment']=$this->request->post['comment'];
  			$this->model_checkout_order->updateOrderComment($this->session->data['order_id'],$this->request->post['comment']);
  				
			$json['success'] = $this->language->get('text_success');
  		}
  		
	  	$this->response->setOutput(json_encode($json));		
	 }
}
?>