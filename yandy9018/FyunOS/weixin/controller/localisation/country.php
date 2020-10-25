<?php 
class ControllerLocalisationCountry extends Controller {
	private $error = array();
 
	public function index() {
		$this->load_language('localisation/country');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/country');
		
		$this->getList();
	}

	public function insert() {
		$this->load_language('localisation/country');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/country');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_country->addCountry($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('localisation/country', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->load_language('localisation/country');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/country');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_country->editCountry($this->request->get['country_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
					
			$this->redirect($this->url->link('localisation/country', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
 
	public function delete() {
		$this->load_language('localisation/country');
 
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/country');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $country_id) {
				$this->model_localisation_country->deleteCountry($country_id);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->redirect($this->url->link('localisation/country', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
			
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		
   		$this->data['breadcrumbs'][] = array(
   		   	'text'      => $this->language->get('heading_title_1'),
   		   	'href'      => $this->url->link('setting/parameter', 'token=' . $this->session->data['token'], 'SSL'),
   		   	'separator' => $this->language->get('text_breadcrumb_separator')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('localisation/country', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['insert'] = $this->url->link('localisation/country/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('localisation/country/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		 
		$this->data['countries'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$country_total = $this->model_localisation_country->getTotalCountries();
		
		$results = $this->model_localisation_country->getCountries($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('localisation/country/update', 'token=' . $this->session->data['token'] . '&country_id=' . $result['country_id'] . $url, 'SSL')
			);

			$this->data['countries'][] = array(
				'country_id' => $result['country_id'],
				'name'       => $result['name'] . (($result['country_id'] == $this->config->get('config_country_id')) ? $this->language->get('text_default') : null),
				'iso_code_2' => $result['iso_code_2'],
				'iso_code_3' => $result['iso_code_3'],
				'selected'   => isset($this->request->post['selected']) && in_array($result['country_id'], $this->request->post['selected']),				
				'action'     => $action
			);
		}

 
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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['sort_name'] = $this->url->link('localisation/country', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
	
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $country_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('localisation/country', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'localisation/country_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}

	private function getForm() {
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}
				
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
   		   	'text'      => $this->language->get('heading_title_1'),
   		   	'href'      => $this->url->link('setting/parameter', 'token=' . $this->session->data['token'], 'SSL'),
   		   	'separator' => $this->language->get('text_breadcrumb_separator')
   		);
   		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('localisation/country', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
							
		if (!isset($this->request->get['country_id'])) { 
			$this->data['action'] = $this->url->link('localisation/country/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('localisation/country/update', 'token=' . $this->session->data['token'] . '&country_id=' . $this->request->get['country_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('localisation/country', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		if (isset($this->request->get['country_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
		}

		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (isset($country_info)) {
			$this->data['name'] = $country_info['name'];
		} else {
			$this->data['name'] = '';
		}

		if (isset($this->request->post['iso_code_2'])) {
			$this->data['iso_code_2'] = $this->request->post['iso_code_2'];
		} elseif (isset($country_info)) {
			$this->data['iso_code_2'] = $country_info['iso_code_2'];
		} else {
			$this->data['iso_code_2'] = '';
		}

		if (isset($this->request->post['iso_code_3'])) {
			$this->data['iso_code_3'] = $this->request->post['iso_code_3'];
		} elseif (isset($country_info)) {
			$this->data['iso_code_3'] = $country_info['iso_code_3'];
		} else {
			$this->data['iso_code_3'] = '';
		}

		if (isset($this->request->post['address_format'])) {
			$this->data['address_format'] = $this->request->post['address_format'];
		} elseif (isset($country_info)) {
			$this->data['address_format'] = $country_info['address_format'];
		} else {
			$this->data['address_format'] = '';
		}

		if (isset($this->request->post['postcode_required'])) {
			$this->data['postcode_required'] = $this->request->post['postcode_required'];
		} elseif (isset($country_info)) {
			$this->data['postcode_required'] = $country_info['postcode_required'];
		} else {
			$this->data['postcode_required'] = 0;
		}
				
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (isset($country_info)) {
			$this->data['status'] = $country_info['status'];
		} else {
			$this->data['status'] = '1';
		}

		$this->template = 'localisation/country_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/country')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((strlen(utf8_decode($this->request->post['name'])) < 1) || (strlen(utf8_decode($this->request->post['name'])) > 128)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/country')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->load->model('setting/store');
		$this->load->model('sale/customer');
		$this->load->model('sale/affiliate');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/geo_zone');
		
		foreach ($this->request->post['selected'] as $country_id) {
			if ($this->config->get('config_country_id') == $country_id) {
				$this->error['warning'] = $this->language->get('error_default');
			}
			
			$store_total = $this->model_setting_store->getTotalStoresByCountryId($country_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
			
			$address_total = $this->model_sale_customer->getTotalAddressesByCountryId($country_id);
	
			if ($address_total) {
				$this->error['warning'] = sprintf($this->language->get('error_address'), $address_total);
			}

			$affiliate_total = $this->model_sale_affiliate->getTotalAffiliatesByCountryId($country_id);
	
			if ($affiliate_total) {
				$this->error['warning'] = sprintf($this->language->get('error_affiliate'), $affiliate_total);
			}
							
			$zone_total = $this->model_localisation_zone->getTotalZonesByCountryId($country_id);
		
			if ($zone_total) {
				$this->error['warning'] = sprintf($this->language->get('error_zone'), $zone_total);
			}
		
			$zone_to_geo_zone_total = $this->model_localisation_geo_zone->getTotalZoneToGeoZoneByCountryId($country_id);
		
			if ($zone_to_geo_zone_total) {
				$this->error['warning'] = sprintf($this->language->get('error_zone_to_geo_zone'), $zone_to_geo_zone_total);
			}
		}
	
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>