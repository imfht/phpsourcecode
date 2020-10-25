<?php 
class ControllerAccountAddress extends Controller {
	private $error = array();
	  
  	public function index() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	}
	
    	$this->load_language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/address');
		
		$this->getList();
  	}

  	public function insert() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} 

    	$this->load_language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/address');
			
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_address->addAddress($this->request->post);
			
      		$this->session->data['success'] = $this->language->get('text_insert');

	  		$this->redirect($this->url->link('account/address', '', 'SSL'));
    	} 
	  	
		$this->getForm();
  	}

  	public function update() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} 
		
    	$this->load_language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/address');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
       		$this->model_account_address->editAddress($this->request->get['address_id'], $this->request->post);
	  		
			if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
	  			unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);	

				if ($this->cart->hasShipping()) {
					$this->tax->setZone($this->request->post['country_id'], $this->request->post['zone_id']);
				}
			}

			if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
	  			unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);
				
				if (!$this->cart->hasShipping()) {
					$this->tax->setZone($this->request->post['country_id'], $this->request->post['zone_id']);
				}		
			}
			
			$this->session->data['success'] = $this->language->get('text_update');
	  
	  		$this->redirect($this->url->link('account/address', '', 'SSL'));
    	} 
	  	
		$this->getForm();
  	}

  	public function delete() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} 
			
    	$this->load_language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/address');
		
    	if (isset($this->request->get['address_id']) && $this->validateDelete()) {
			$this->model_account_address->deleteAddress($this->request->get['address_id']);	

			if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
	  			unset($this->session->data['shipping_address_id']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);	
				
				
				if ($this->cart->hasShipping()) {
					$this->tax->setZone($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
				}				
			}

			if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
	  			unset($this->session->data['payment_address_id']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);	
				
				if (!$this->cart->hasShipping()) {
					$this->tax->setZone($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
				}								
			}
			
			$this->session->data['success'] = $this->language->get('text_delete');
	  
	  		$this->redirect($this->url->link('account/address', '', 'SSL'));
    	}
	
		$this->getList();	
  	}

  	private function getList() {
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
			'href'      => $this->url->link('account/address', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);

		if (isset($this->error['warning'])) {
    		$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
    		unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
    	$this->data['addresses'] = array();
		
		$results = $this->model_account_address->getAddresses();

    	foreach ($results as $result) {
			if ($result['address_format']) {
      			$format = $result['address_format'];
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
     			'{phone}',
     			'{mobile}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);
	
			$replace = array(
	  			'firstname' => $result['firstname'],
	  			'lastname'  => $result['lastname'],
	  			'company'   => $result['company'],
      			'address_1' => $result['address_1'],
      			'address_2' => $result['address_2'],
      			'city'      => $result['city'],
				'phone'      => $result['phone'],
      			'mobile'      => $result['mobile'],
      			'postcode'  => $result['postcode'],
      			'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
      			'country'   => $result['country']  
			);

      		$this->data['addresses'][] = array(
        		'address_id' => $result['address_id'],
        		'address'    => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
        		'update'     => $this->url->link('account/address/update', 'address_id=' . $result['address_id'], 'SSL'),
				'delete'     => $this->url->link('account/address/delete', 'address_id=' . $result['address_id'], 'SSL')
      		);
    	}

    	$this->data['insert'] = $this->url->link('account/address/insert', '', 'SSL');
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
				
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/address_list.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/address_list.tpl';
		} else {
			$this->template = 'default/template/account/address_list.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'		
		);
						
		$this->response->setOutput($this->render());		
  	}

  	private function getForm() {
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
			'href'      => $this->url->link('account/address', '', 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);
		
		if (!isset($this->request->get['address_id'])) {
      		$this->data['breadcrumbs'][] = array(
        		'text'      => $this->language->get('text_edit_address'),
				'href'      => $this->url->link('account/address/insert', '', 'SSL'),       		
        		'separator' => $this->language->get('text_separator')
      		);
		} else {
      		$this->data['breadcrumbs'][] = array(
        		'text'      => $this->language->get('text_edit_address'),
				'href'      => $this->url->link('account/address/update', 'address_id=' . $this->request->get['address_id'], 'SSL'),       		
        		'separator' => $this->language->get('text_separator')
      		);
		}
		
		$errors=array(
			'firstname' => 'error_firstname',
			'address_1' => 'error_address_1',
			'city' => 'error_city',
			'mobile' => 'error_mobile',
			'postcode' => 'error_postcode',
			'zone' => 'error_zone',
			'address_1' => 'error_address_1'
		);				
		
		foreach ($errors as $key => $value) {
			if (isset($this->error[$key])) {
	    		$this->data[$value] = $this->error[$key];
			} else {
				$this->data[$value] = '';
			}
		}

    	if (!isset($this->request->get['address_id'])) {
    		$this->data['action'] = $this->url->link('account/address/insert', '', 'SSL');
		} else {
    		$this->data['action'] = $this->url->link('account/address/update', 'address_id=' . $this->request->get['address_id'], 'SSL');
		}
		
    	if (isset($this->request->get['address_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$address_info = $this->model_account_address->getAddress($this->request->get['address_id']);
		}
	
		$values=array(
			'firstname' => '',
			'company' => '',
			'address_1' => '',
			'address_2' => '',
			'postcode' => '',
			'city_id' => '',
			'mobile' => '',
			'phone' => '',
			'country_id' => $this->config->get('config_country_id'),
			'zone_id' => '',
		);
		
		foreach ($values as $key => $value) {
			if (isset($this->request->post[$key])) {
	      		$this->data[$key] = $this->request->post[$key];
	    	} elseif (isset($address_info)) {
	      		$this->data[$key] = $address_info[$key];
	    	} else {
				$this->data[$key] = $value;
			}
		}
		
    	$this->load->model('localisation/country');
		
    	$this->data['countries'] = $this->model_localisation_country->getCountries();

    	if (isset($this->request->post['default'])) {
      		$this->data['default'] = $this->request->post['default'];
    	} elseif (isset($this->request->get['address_id'])) {
      		$this->data['default'] = $this->customer->getAddressId() == $this->request->get['address_id'];
    	} else {
			$this->data['default'] = false;
		}

    	$this->data['back'] = $this->url->link('account/address', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/address_form.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/address_form.tpl';
		} else {
			$this->template = 'default/template/account/address_form.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'		
		);
						
		$this->response->setOutput($this->render());	
  	}
	
  	private function validateForm() {
    	if ((strlen(utf8_decode($this->request->post['firstname'])) < 1) || (strlen(utf8_decode($this->request->post['firstname'])) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
    	}
    	
    	if ((strlen(utf8_decode($this->request->post['mobile'])) < 1) || (strlen(utf8_decode($this->request->post['firstname'])) > 20)) {
      		$this->error['mobile'] = $this->language->get('error_mobile');
    	}

		if ((strlen(utf8_decode($this->request->post['address_1'])) < 1) || (strlen(utf8_decode($this->request->post['address_1'])) > 128)) {
      		$this->error['address_1'] = $this->language->get('error_address_1');
    	}

    	if ($this->request->post['city_id'] == '') {
    		$this->error['city'] = $this->language->get('error_city');
    	}
    	
		if ($this->request->post['zone_id'] == '') {
      		$this->error['zone'] = $this->language->get('error_zone');
    	}
		
    	if (!$this->error) {
      		return true;
		} else {
      		return false;
    	}
  	}

  	private function validateDelete() {
    	if ($this->model_account_address->getTotalAddresses() == 1) {
      		$this->error['warning'] = $this->language->get('error_delete');
    	}

    	if ($this->customer->getAddressId() == $this->request->get['address_id']) {
      		$this->error['warning'] = $this->language->get('error_default');
    	}

    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
  	}
 
}
?>