<?php  
class ControllerCommonNav extends Controller {
	public function index() {
		$this->data['lang'] = $this->language->get('code');
		$this->data['storeType'] = $this->config->get('config_store_type');
		$this->data['direction'] = $this->language->get('direction');
		$this->load_language('common/nav');
			//判断用户来自哪里
		if (!$this->customer->isLogged()) {
			if($this->config->get('config_bind_status')=='1'){
				if($this->config->get('config_phone_login')=='1'){
					$this->data['login'] = $this->url->link('account/login&phone=1', '', 'SSL');
				}else{
					$this->customer->weixinLogin();
					}
		}elseif($this->config->get('config_bind_status')=='2'){
				if(isset($this->request->get['openId'])){
					$this->session->data['open_id'] = $this->request->get['openId'];
					$this->data['login'] = $this->url->link('account/account', '', 'SSL');
					}else{
						$this->data['login'] = $this->url->link('account/login&ubind=1', '', 'SSL');
						}
				}else{
				$this->data['login'] = $this->url->link('account/login', '', 'SSL');
					}
		}else{
			$this->data['login'] = $this->url->link('account/login', '', 'SSL');
			}
			
		$this->load->model('catalog/nav');
		 $this->data['navs'] = $this->model_catalog_nav->getNavs();
	
        $this->data['home'] = $this->url->link('common/home');
		$this->data['category'] = $this->url->link('product/category&path=0');
		$this->data['cart'] = $this->url->link('checkout/cart');
		$this->data['map'] = "http://api.map.baidu.com/marker?location=".$this->config->get('config_latlng')."&title=".$this->config->get('config_name')."&content=".$this->config->get('config_address')."&output=html";
		$this->data['account'] = $this->url->link('account/account');
		$this->data['products'] = array();
      		foreach ($this->cart->getProducts() as $result) {
				if ($result['image']) {
					$image = $result['image'].'!40list';
				} else {
					$image = "http://fyunimage.b0.upaiyun.com/no_image.jpg!40list";
				}

				if ($result['minimum'] > $result['quantity']) {
					$this->session->data['error'] = sprintf($this->language->get('error_minimum'), $result['name'], $result['minimum']);
				}

        	
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')),'','',false);
				} else {
					$price = false;
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$total = $this->currency->format($this->tax->calculate($result['total'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$total = false;
				}
				
				$this->load->model('localisation/unit');
				$units = $this->model_localisation_unit->getUnit($result['unit']);
				$unit_id = $units['name'];
				
        		$this->data['products'][] = array(
          			'key'      => $result['key'],
          			'thumb'    => $image,
					'product_id'     => $result['product_id'],
					'name'     => $result['name'],
          			'model'    => $result['model'],
          			'quantity' => $result['quantity'],
					'quantity1' => $result['quantity1'],
          			'stock'    => $result['stock'],
					'points'   => $result['points'],
					'allReward'   => $result['allReward'],
					'reward'   => $result['reward'],
					'price'    => $price,
					'unit'    => $unit_id,
					'total'    => $total,
        			'remove'   => $this->url->link('checkout/cart', 'remove=' . $result['key']),
					'href'     => $this->url->link('product/product', 'product_id=' . $result['product_id'])
        		);
      		}
			
			// Gift Voucher
			$this->data['vouchers'] = array();
			
			if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$this->data['vouchers'][] = array(
						'key'         => $key,
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'])
					);
				}
			} 
						
			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();
			
			if ($this->config->get('config_customer_price') || !$this->config->get('config_customer_price')) {						 
				$this->load->model('setting/extension');
				
				$sort_order = array(); 
				
				$results = $this->model_setting_extension->getExtensions('total');
				
				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}
				
				array_multisort($sort_order, SORT_ASC, $results);
				
				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('total/' . $result['code']);
		
						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					}
				}
				
				$sort_order = array(); 
			  
				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
	
				array_multisort($sort_order, SORT_ASC, $total_data);
			}
			
			$this->data['totals'] = $total_data;
	     	//$this->data['carAllCash'] = $this->data['shipping_cost'] + $this->data['totals']['0']['value'];
			$this->data['moToFixed'] =  $this->data['totals']['0']['value'];
		
		
		if(isset($this->session->data['menu'])){
			$this->data['menu'] = $this->session->data['menu'];
			}else{
				$this->data['menu'] = 'home';
				}
		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/nav.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/nav.tpl';
		} else {
			$this->template = 'default/template/common/nav.tpl';
		}
								
		$this->render();
	}
}
?>