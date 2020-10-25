<?php
class ControllerLocalisationCity extends Controller {

	private $error = array();

	public function index() {
		$this->load_language('localisation/city');
 
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/city');
		
    	$this->getList();
	}

	public function insert() {
		$this->load_language('localisation/city');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/city');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_city->addCity($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
		  
			$url = '';

			if (isset($this->request->get['filter_zone'])) {
				$url .= '&filter_zone=' . $this->request->get['filter_zone'];
			}
			
			if (isset($this->request->get['filter_city'])) {
				$url .= '&filter_city=' . $this->request->get['filter_city'];
			}
			
			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}
			
			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
							
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->redirect($this->url->link('localisation/city', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    	
    	$this->getForm();

	}

	public function update() {
		$this->load_language('localisation/city');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/city');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_city->editCity($this->request->get['city_id'], $this->request->post);
	  		
			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = '';

			if (isset($this->request->get['filter_zone'])) {
				$url .= '&filter_zone=' . $this->request->get['filter_zone'];
			}
			
			if (isset($this->request->get['filter_city'])) {
				$url .= '&filter_city=' . $this->request->get['filter_city'];
			}
			
			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}
			
			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
						
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->redirect($this->url->link('localisation/city', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    
    	$this->getForm();
  	}   

	public function delete() {
		$this->load_language('localisation/city');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/city');
			
    	if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $city_id) {
				$this->model_localisation_city->deleteCity($city_id);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_zone'])) {
				$url .= '&filter_zone=' . $this->request->get['filter_zone'];
			}
			
			if (isset($this->request->get['filter_city'])) {
				$url .= '&filter_city=' . $this->request->get['filter_city'];
			}
			
			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}
			
			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
						
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->redirect($this->url->link('localisation/city', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
    
    	$this->getList();
  	}  

	private function getList() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'city_zone'; 
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['filter_zone'])) {
			$filter_zone = $this->request->get['filter_zone'];
		} else {
			$filter_zone = NULL;
		}

		if (isset($this->request->get['filter_city'])) {
			$filter_city = $this->request->get['filter_city'];
		} else {
			$filter_city = NULL;
		}
		
		if (isset($this->request->get['filter_code'])) {
			$filter_code = $this->request->get['filter_code'];
		} else {
			$filter_code = NULL;
		}

		if (isset($this->request->get['filter_country_id'])) {
			$filter_country_id = $this->request->get['filter_country_id'];
		} else {
			$filter_country_id = NULL;
		}
		
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = NULL;
		}
				
		$url = '';

		if (isset($this->request->get['filter_zone'])) {
			$url .= '&filter_zone=' . $this->request->get['filter_zone'];
		}
		
		if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . $this->request->get['filter_city'];
		}
		
		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}
			
		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}	
			
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);
   		
   		$this->data['breadcrumbs'][] = array(
   		   	'text'      => $this->language->get('heading_title_1'),
   		   	'href'      => $this->url->link('setting/parameter', 'token=' . $this->session->data['token'], 'SSL'),
   		   	'separator' => $this->language->get('text_breadcrumb_separator')
   		);

   		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . $url, 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		$this->data['insert'] = $this->url->link('localisation/city/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('localisation/city/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['reset'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cities'] = array();

		$data = array(
			'filter_zone'              => $filter_zone, 
			'filter_city'              => $filter_city, 
			'filter_code'			   => $filter_code, 
			'filter_country_id'        => $filter_country_id, 
			'filter_status'            => $filter_status, 
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                    => $this->config->get('config_admin_limit')
		);
		
		$city_total = $this->model_localisation_city->getTotalCities($data);
	
		$results = $this->model_localisation_city->getCities($data);
 
    	foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('localisation/city/update', 'token=' . $this->session->data['token'] . '&city_id=' . $result['city_id'] . $url, 'SSL')
			);
			
			$this->data['cities'][] = array(
				'zone'			=> $result['city_zone'],
				'city_id'		=> $result['city_id'],
				'name'			=> $result['city_name'],
				'code'			=> $result['city_code'],
				'country'		=> $result['city_country'],
				'status'		=> ($result['city_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'		=> isset($this->request->post['selected']) && in_array($result['city_id'], $this->request->post['selected']),
				'action'		=> $action
			);
		}	
					
		$this->data['token'] = $this->session->data['token'];

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
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

		if (isset($this->request->get['filter_zone'])) {
			$url .= '&filter_zone=' . $this->request->get['filter_zone'];
		}
		
		if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . $this->request->get['filter_city'];
		}
		
		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}
			
		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}	
			
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_zone'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . '&sort=city_zone' . $url, 'SSL');
		$this->data['sort_name'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . '&sort=city_name' . $url, 'SSL');
		$this->data['sort_code'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . '&sort=city_code' . $url, 'SSL');
		$this->data['sort_country'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . '&sort=city_country' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . '&sort=city_status' . $url, 'SSL');
				
		$url = '';

		if (isset($this->request->get['filter_zone'])) {
			$url .= '&filter_zone=' . $this->request->get['filter_zone'];
		}
		
		if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . $this->request->get['filter_city'];
		}
		
		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
					
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $city_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['filter_zone'] = $filter_zone;
		$this->data['filter_city'] = $filter_city;
		$this->data['filter_code'] = $filter_code;
		$this->data['filter_country_id'] = $filter_country_id;
		$this->data['filter_status'] = $filter_status;
		
		$this->load->model('localisation/country');
		
    	$this->data['countries'] = $this->model_localisation_country->getCountries(array('status' => 1));
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->template = 'localisation/city_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
  	}

	private function getForm() {
    	$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['country'])) {
			$this->data['error_country'] = $this->error['country'];
		} else {
			$this->data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$this->data['error_zone'] = $this->error['zone'];
		} else {
			$this->data['error_zone'] = '';
		}

 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}
		
		$url = '';
		
		if (isset($this->request->get['filter_zone'])) {
			$url .= '&filter_zone=' . $this->request->get['filter_zone'];
		}
		
		if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . $this->request->get['filter_city'];
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}
		
		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
								
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
 			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
   		   	'text'      => $this->language->get('heading_title_1'),
   		   	'href'      => $this->url->link('setting/parameter', 'token=' . $this->session->data['token'], 'SSL'),
   		   	'separator' => $this->language->get('text_breadcrumb_separator')
   		);
   		
   		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('localisation/city', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);

		if (!isset($this->request->get['city_id'])) {
			$this->data['action'] = $this->url->link('localisation/city/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('localisation/city/update', 'token=' . $this->session->data['token'] . '&city_id=' . $this->request->get['city_id'] . $url, 'SSL');
		}
		  
		$this->data['cancel'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'] . $url, 'SSL');

    	if (isset($this->request->get['city_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$city_info = $this->model_localisation_city->getCity($this->request->get['city_id']);
    	}

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (isset($city_info)) {
			$this->data['status'] = $city_info['status'];
		} else {
			$this->data['status'] = '1';
		}
		
		if (isset($this->request->post['center_status'])) {
			$this->data['center_status'] = $this->request->post['center_status'];
		} elseif (isset($city_info)) {
			$this->data['center_status'] = $city_info['center_status'];
		} else {
			$this->data['center_status'] = '0';
		}
		
		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (isset($city_info)) {
			$this->data['name'] = $city_info['name'];
		} else {
			$this->data['name'] = '';
		}

		if (isset($this->request->post['code'])) {
			$this->data['code'] = $this->request->post['code'];
		} elseif (isset($city_info)) {
			$this->data['code'] = $city_info['code'];
		} else {
			$this->data['code'] = '';
		}

		if (isset($this->request->post['zone_id'])) {
			$this->data['zone_id'] = $this->request->post['zone_id'];
		} elseif (isset($city_info)) {
			$this->data['zone_id'] = $city_info['zone_id'];
		} else {
			$this->data['zone_id'] = $this->config->get('config_zone_id');
		}

		if (isset($this->request->post['country_id'])) {
			$this->data['country_id'] = $this->request->post['country_id'];
		} elseif (isset($city_info)) {
			$this->data['country_id'] = $city_info['country_id'];
		} else {
			$this->data['country_id'] = $this->config->get('config_country_id');
		}
						
		$this->load->model('localisation/country');
		
    	$this->data['countries'] = $this->model_localisation_country->getCountries(array('status' => 1));
			
		$this->template = 'localisation/city_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}

	public function zone() {
		$output = '';
		
		$this->load->model('localisation/zone');
		
		$results = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
		
		foreach ($results as $result) {
			$output .= '<option value="' . $result['zone_id'] . '"';

			if (isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id'])) {
				$output .= ' selected="selected"';
			}

			$output .= '>' . $result['name'] . '</option>';
		}

		if (!$results) {
			if (!$this->request->get['zone_id']) {
		  		$output .= '<option value="0" selected="selected">' . $this->language->get('text_none') . '</option>';
			} else {
				$output .= '<option value="0">' . $this->language->get('text_none') . '</option>';
			}
		}

		$this->response->setOutput($output);
	}

	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'localisation/city')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

		if ($this->request->post['country_id'] == 'FALSE') {
      		$this->error['country'] = $this->language->get('error_country');
    	}

		if ($this->request->post['zone_id'] == 'FALSE') {
      		$this->error['zone'] = $this->language->get('error_zone');
    	}

		if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 128)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}

	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'localisation/city')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}	
	  	 
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}  
  	} 	


}
?>