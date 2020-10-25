<?php 
class ControllerAccountOrder extends Controller {
	private $error = array();
	// if you modify here, you need also modify ControllerCheckoutCheckout
	private $direct_payments= array('cod','cheque','free_checkout','bank_transfer');
		
	public function index() {
		
	//判断用户来自哪里
		if (!$this->customer->isLogged()) {
			if($this->config->get('config_bind_status')=='1'){
				if($this->config->get('config_phone_login')=='1'){
					$this->redirect($this->url->link('account/login&phone=1', '', 'SSL'));
				}else{
					$this->customer->weixinLogin();
					}
		}elseif($this->config->get('config_bind_status')=='2'){
				if(isset($this->request->get['openId'])){
					$this->session->data['open_id'] = $this->request->get['openId'];
					$this->redirect($this->url->link('account/account', '', 'SSL'));
					}else{
						$this->redirect($this->url->link('account/login&ubind=1', '', 'SSL'));
						}
				}else{
					$this->redirect($this->url->link('account/login', '', 'SSL'));
					}
		}
		
    	
		$this->session->data['menu']="account";
    	$this->load_language('account/order');

    	$this->document->setTitle($this->language->get('heading_title'));

     
		$this->data['action'] = $this->url->link('account/order', '', 'SSL');
		
		$this->load->model('account/order');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$this->data['orders'] = array();
		
		$order_total = $this->model_account_order->getTotalOrders();
		$this->data['order_total'] =$order_total;
		$results = $this->model_account_order->getOrders(($page - 1) * $this->config->get('config_catalog_limit'), $this->config->get('config_catalog_limit'));
		$common = new Common($this->registry);
		
		foreach ($results as $result) {
			
			
			 $products = $this->model_account_order->getOrderProducts($result['order_id']);
			 foreach ($products as $product) { 
			  $this->data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
          			'name'             => $product['name'],
          			'quantity'         => $product['quantity'],
					'total'            => round($product['total'],2),
        		);
			 }

			$this->data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'reward'     => $result['reward'],
				'express'     => $result['express'],
				'express_website'     => $result['express_website'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'products'   => $this->data['products'],
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'href'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
			);
		}

		$pagination = new MobilePagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_catalog_limit'); 
		$pagination->style_links = 'pageNumber';
		$pagination->url = $this->url->link('account/order', 'page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();


		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/order_list.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/order_list.tpl';
		} else {
			$this->template = 'default/template/account/order_list.tpl';
		}
		
				$this->children = array(
				'common/header',
				'common/nav',
			);
						
		$this->response->setOutput($this->render());				
	}
	
	public function info() { 
		if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->url->link('account/order', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
			
		$this->load_language('account/order');
		
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}	
		
		$this->load->model('account/order');
			
		$order_info = $this->model_account_order->getOrder($order_id);
		
		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));
			
			$this->data['breadcrumbs'] = array();
		
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),        	
				'separator' => false
			); 
		
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),        	
				'separator' => $this->language->get('text_separator')
			);
			
			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/order', $url, 'SSL'),      	
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_order'),
				'href'      => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
					
  			if (isset($this->error['warning'])) {
				$this->data['error_warning'] = $this->error['warning'];
			} else {
				$this->data['error_warning'] = '';
			}
			
			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}
			$common = new Common($this->registry);
			$this->data['order_id'] =$this->request->get['order_id'];
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			
			
			if ($order_info['shipping_address_format']) {
      			$format = $order_info['shipping_address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" .  "\n" . '{zone}' .'{city}' .'{address_1}' . "\n" . '{address_2}' .  "\n" . ' {postcode} '.  "\n" .' {mobile}/ {phone}';
			}
		
    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{zone}',
      			'{city}',
    			'{company}',
      			'{address_1}',
      			'{address_2}',
      			'{postcode}',
				'{mobile}',
				'{phone}'
			);
	
			$replace = array(
	  			'firstname' => $order_info['shipping_firstname'],
	  			'lastname'  => $order_info['shipping_lastname'],
				'zone'      => $order_info['shipping_zone'],
	  			'city'      => $order_info['shipping_city'],
	  			'company'   => $order_info['shipping_company'],
      			'address_1' => $order_info['shipping_address_1'],
      			'address_2' => $order_info['shipping_address_2'],
				'postcode'  => $order_info['shipping_postcode'],
      			'mobile'    => $order_info['shipping_mobile'],
      			'phone'    => $order_info['shipping_phone'],
      			'zone_code' => $order_info['shipping_zone_code'],
      			'country'   => $order_info['shipping_country']  
			);

			
			if($order_info['shipping_firstname']=='')
				$this->data['shipping_required']=0;
			else 	
				$this->data['shipping_required']=1;
			
			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			
			$this->data['shipping_method'] = $order_info['shipping_method'];

			if ($order_info['payment_address_format']) {
      			$format = $order_info['payment_address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
		
    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);
	
			$replace = array(
	  			'firstname' => $order_info['payment_firstname'],
	  			'lastname'  => $order_info['payment_lastname'],
	  			'company'   => $order_info['payment_company'],
      			'address_1' => $order_info['payment_address_1'],
      			'address_2' => $order_info['payment_address_2'],
      			'city'      => $order_info['payment_city'],
      			'postcode'  => $order_info['payment_postcode'],
      			'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
      			'country'   => $order_info['payment_country']  
			);
			
			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

      		$this->data['payment_method'] = $order_info['payment_method'];
			
      		if($order_info['payment_code']!=''&&!in_array($order_info['payment_code'],$this->direct_payments)&&$order_info['order_status_id']==$this->config->get('config_order_nopay_status_id'))
      			$this->data['payment'] = $this->getChild('payment/' . $order_info['payment_code'].'/reorder');
      		else 
      			$this->data['payment'] ='';
      		
      		if(isset($order_info['express'])){
      			$this->data['express'] = $order_info['express'];
      			$this->data['express_website'] = $order_info['express_website'];
      			$this->data['express_no'] = $order_info['express_no'];
      		}
      		
			$this->data['products'] = array();
			
			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);

      		foreach ($products as $product) {
				$option_data = array();
				
				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

         		foreach ($options as $option) {
          			if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (strlen($option['value']) > 20 ? substr($option['value'], 0, 20) . '..' : $option['value']),
						);
					} else {
						$filename = substr($option['value'], 0, strrpos($option['value'], '.'));
						
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (strlen($filename) > 20 ? substr($filename, 0, 20) . '..' : $filename)
						);						
					}
        		}

        		$this->data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
          			'name'             => $product['name'],
          			'model'            => $product['model'],
        			'href'    	 	   => $this->url->link('product/product', 'product_id=' . $product['product_id']),
          			'option'           => $option_data,
          			'quantity'         => $product['quantity'],
          			'price'            => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
					'total'            => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']),
					'selected'         => isset($this->request->post['selected']) && in_array($result['order_product_id'], $this->request->post['selected'])
        		);
      		}

      		$this->data['totals'] = $this->model_account_order->getOrderTotals($this->request->get['order_id']);
			
			$this->data['comment'] = $order_info['comment'];
			
			$this->data['histories'] = array();

			$results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

      		foreach ($results as $result) {
        		$this->data['histories'][] = array(
          			'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
          			'status'     => $result['status'],
          			'comment'    => nl2br($result['comment'])
        		);
      		}

      		$this->data['continue'] = $this->url->link('account/order', '', 'SSL');
		
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/order_info.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/account/order_info.tpl';
			} else {
				$this->template = 'default/template/account/order_info.tpl';
			}
			
					$this->children = array(
				'common/header',
				'common/nav',
			);
								
			$this->response->setOutput($this->render());		
    	} else {
			$this->document->setTitle($this->language->get('text_order'));
			
      		$this->data['heading_title'] = $this->language->get('text_order');

      		$this->data['text_error'] = $this->language->get('text_error');

      		$this->data['button_continue'] = $this->language->get('button_continue');
			
			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),
				'separator' => false
			);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			);

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/order', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_order'),
				'href'      => $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
												
      		$this->data['continue'] = $this->url->link('account/order', '', 'SSL');
			 			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
			}
			
					$this->children = array(
				'common/header',
				'common/nav',
			);
								
			$this->response->setOutput($this->render());				
    	}
  	}
	
	private function validate() {
		if (!isset($this->request->post['selected']) || !isset($this->request->post['action']) || !$this->request->post['action']) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}		
	}
}
?>