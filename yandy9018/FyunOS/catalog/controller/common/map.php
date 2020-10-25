<?php  
class ControllerCommonNav extends Controller {
	public function index() {
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->load_language('common/nav');
		
        $this->data['home'] = $this->url->link('common/home');
		$this->data['category'] = $this->url->link('product/category&path=0');
		$this->data['cart'] = $this->url->link('checkout/cart');
		$this->data['account'] = $this->url->link('account/account');
		$this->data['menu'] = $this->session->data['menu'];
		
	
	    $this->template = $this->config->get('config_template') . '/template/common/map.tpl';					
		$this->response->setOutput($this->render());
	}
}
?>