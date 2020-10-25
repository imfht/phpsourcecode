<?php 
class ControllerCommonTopCart extends Controller {
	public function index() {
		$this->load_language('checkout/cart');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->data['store_url'] = $this->config->get('config_url');
		$this->data['store_status'] = $this->config->get('config_store_status');
		$this->data['seat'] = $this->config->get('config_seat');
		$this->data['country_id'] = $this->config->get('config_country_id');
		$this->data['name'] = $this->customer->getName();
		$this->data['telephone'] = $this->customer->getTelephone();
		$this->data['address'] = $this->customer->getAddress();
		
		
		$this->data['zone_id'] = $this->customer->getZoneId();
		
		
	
		$this->data['city_id'] = $this->customer->getCityId();
		
		if($this->customer->getBalance()){
			$this->data['balance'] =  $this->customer->getBalance();
			}else{
				$this->data['balance'] = 0;
				}
        $this->data['shipping_time'] = '填写地址自动计算到达时间';
		$this->data['shipping_cost'] = 0;
		
	if(isset($this->data['zone_id'])){
		$this->load->model('localisation/zone');
		$this->load->model('localisation/city');
		$zones = $this->model_localisation_zone->getZone($this->data['zone_id']);
		$citys = $this->model_localisation_city->getCity($this->data['city_id']);
		if(isset($zones)){
			$this->data['zone_name'] = $zones['name'];
		}
		if(isset($citys)){
			$this->data['city_name'] = $citys['name'];
		}

		$this->load->model('shipping/weight');
		$this->data['shipping_cost'] = $this->model_shipping_weight->getCost($this->data['zone_id'],$this->data['city_id']);	
		
		$this->data['shipping_time'] = $this->model_shipping_weight->getShippingTime($this->data['zone_id'],$this->data['city_id']);	
		if(empty($this->data['shipping_cost'])){
		$this->data['shipping_cost'] = 0;
		}
		
		if(empty($this->data['shipping_time'])){
		$this->data['shipping_time'] = '无预计配送时间';
		}
		
	}

	   
      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	);
			
    	
      		$this->data['heading_title'] = $this->language->get('heading_title');
			
			$this->data['text_select'] = $this->language->get('text_select');
			$this->data['text_weight'] = $this->language->get('text_weight');
			
		    $this->data['text_empty'] = $this->language->get('text_empty');
			$this->data['entry_category'] = $this->language->get('entry_category');
		
     		$this->data['column_remove'] = $this->language->get('column_remove');
      		$this->data['column_image'] = $this->language->get('column_image');
      		$this->data['column_name'] = $this->language->get('column_name');
      		$this->data['column_model'] = $this->language->get('column_model');
      		$this->data['column_quantity'] = $this->language->get('column_quantity');
			$this->data['column_price'] = $this->language->get('column_price');
      		$this->data['column_total'] = $this->language->get('column_total');
			
      		$this->data['button_update'] = $this->language->get('button_update');
      		$this->data['button_shopping'] = $this->language->get('button_shopping');
      		$this->data['button_checkout'] = $this->language->get('button_checkout');
			
			if ($this->config->get('config_customer_price')) {
				$this->data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
			} else {
				$this->data['attention'] = '';
			}
			$this->data['action'] = $this->url->link('checkout/cart');
			$this->data['weight'] = false;
		 
			$this->load->model('tool/image');
			
      		$this->data['products'] = array();
      		foreach ($this->cart->getProducts() as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				} else {
					$image = '';
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

		
			$this->data['carAllCash'] = $this->data['shipping_cost'] + $this->data['totals']['0']['value'];
			$this->data['moToFixed'] =  $this->data['totals']['0']['value'];
			$this->data['count'] = $this->cart->countProducts();
		
				
			// Modules
			$this->data['modules'] = array();
			
			if (isset($results)) {
				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status') && file_exists(DIR_APPLICATION . 'controller/total/' . $result['code'] . '.php')) {
						$this->data['modules'][] = $this->getChild('total/' . $result['code']);
					}
				}
			}
			
			if (isset($this->session->data['redirect'])) {
      			$this->data['continue'] = $this->session->data['redirect'];
				unset($this->session->data['redirect']);
			} else {
				$this->data['continue'] = $this->url->link('common/home');
			}
			
    		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
      			$this->data['error_warning'] = $this->language->get('error_stock');
			} elseif (isset($this->session->data['error'])) {
				$this->data['error_warning'] = $this->session->data['error'];
			
				unset($this->session->data['error']);			
			} else {
				$this->data['error_warning'] = '';
			}
			
    		if (isset($this->session->data['success'])) {
				$this->data['success'] = $this->session->data['success'];
			
				unset($this->session->data['success']);
			} else {
				$this->data['success'] = '';
			}
			
				
		
			
			$this->data['checkout'] = $this->url->link('checkout/confirm', '', 'SSL');
            
			
		if(!$this->cart->getProducts()){
			
			$this->template = $this->config->get('config_template') . '/template/common/topnocart.tpl';
				
			
			}else{
				
			$this->template = $this->config->get('config_template') . '/template/common/topcart.tpl';
			
				}
		
			$this->render();					
    	}
		


		
}
?>