<?php    
class ControllerSaleCustomer extends Controller { 
	private $error = array();
  
  	public function index() {
		$this->load_language('sale/customer');
		 
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/customer');
		
    	$this->getList();
  	}
  
  	public function insert() {
		$this->load_language('sale/customer');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/customer');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      	  	$this->model_sale_customer->addCustomer($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
		  
			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}
			
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			
			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}
					
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
							
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    	
    	$this->getForm();
  	} 
   
  	public function update() {
		$this->load_language('sale/customer');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/customer');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_customer->editCustomer($this->request->get['customer_id'], $this->request->post);
	  		
			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}
			
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			
			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
		
			
			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}
					
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
						
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    
    	$this->getForm();
  	}   

  	public function delete() {
		$this->load_language('sale/customer');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/customer');
			
    	if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $customer_id) {
				$this->model_sale_customer->deleteCustomer($customer_id);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}
			
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			
			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			

				
			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}
					
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
						
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
    
    	$this->getList();
  	}  
	
	public function approve() {
		$this->load_language('sale/customer');
    	
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/customer');
		
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} elseif (isset($this->request->post['selected'])) {
			$approved = 0;
			
			foreach ($this->request->post['selected'] as $customer_id) {
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);
				
				if ($customer_info && !$customer_info['approved']) {
					$this->model_sale_customer->approve($customer_id);
					
					$approved++;
				}
			} 
			
			$this->session->data['success'] = sprintf($this->language->get('text_approved'), $approved);	
			
			$url = '';
		
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}
		
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			
			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}
		
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}
						
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
						
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
	
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
							
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}	
	
			$this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));			
		}
		
		$this->getList();
	} 
    
  	private function getList() {
		if (isset($this->request->get['filter_firstname'])) {
			$filter_firstname = $this->request->get['filter_firstname'];
		} else {
			$filter_firstname = null;
		}

		if (isset($this->request->get['filter_telephone'])) {
			$filter_telephone = $this->request->get['filter_telephone'];
		} else {
			$filter_telephone = null;
		}
		
		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		
	
		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = $this->request->get['filter_ip'];
		} else {
			$filter_ip = null;
		}
				
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}		
		
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

		if (isset($this->request->get['filter_firstname'])) {
			$url .= '&filter_firstname=' . $this->request->get['filter_firstname'];
		}
		
		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
		}
		
		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}
			
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
						
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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['approve'] = $this->url->link('sale/customer/approve', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['insert'] = $this->url->link('sale/customer/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('sale/customer/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['customers'] = array();

		$data = array(
			'filter_firstname'              => $filter_firstname, 
			'filter_telephone'             => $filter_telephone, 
			'filter_customer_group_id' => $filter_customer_group_id, 
			'filter_status'            => $filter_status, 
			'filter_date_added'        => $filter_date_added,
			'filter_ip'                => $filter_ip,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                    => $this->config->get('config_admin_limit')
		);
		
		$customer_total = $this->model_sale_customer->getTotalCustomers($data);
	
		$results = $this->model_sale_customer->getCustomers($data);
 
    	foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . $url, 'SSL')
			);
			
			$this->data['customers'][] = array(
				'customer_id'    => $result['customer_id'],
				'firstname'           => $result['firstname'],
				'telephone'          => $result['telephone'],
				'customer_group' => $result['customer_group'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'approved'       => ($result['approved'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
				'ip'             => $result['ip'],
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'login'          => $this->url->link('sale/customer/login', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL'),
				'selected'       => isset($this->request->post['selected']) && in_array($result['customer_id'], $this->request->post['selected']),
				'action'         => $action
			);
		}	
		
		$this->data['token'] = $this->session->data['token'];

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

		if (isset($this->request->get['filter_firstname'])) {
			$url .= '&filter_firstname=' . $this->request->get['filter_firstname'];
		}
		
		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
		}
		
		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}
			
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}
				
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
			
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['sort_firstname'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.firstname' . $url, 'SSL');
		$this->data['sort_telephone'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.telephone' . $url, 'SSL');
		$this->data['sort_customer_group'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=customer_group' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.status' . $url, 'SSL');
		$this->data['sort_approved'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.approved' . $url, 'SSL');
		$this->data['sort_ip'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.ip' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.date_added' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['filter_firstname'])) {
			$url .= '&filter_firstname=' . $this->request->get['filter_firstname'];
		}
		
		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
		}
		
		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		
		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}
				
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $customer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['filter_firstname'] = $filter_firstname;
		$this->data['filter_telephone'] = $filter_telephone;
		$this->data['filter_customer_group_id'] = $filter_customer_group_id;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_ip'] = $filter_ip;
		$this->data['filter_date_added'] = $filter_date_added;
		
		$this->load->model('sale/customer_group');
		
    	$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->template = 'sale/customer_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
  	}
  
  	private function getForm() {
   		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['customer_id'])) {
			$this->data['customer_id'] = $this->request->get['customer_id'];
		} else {
			$this->data['customer_id'] = 0;
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
 		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}
		
		if (isset($this->error['address_firstname'])) {
			$this->data['error_address_firstname'] = $this->error['address_firstname'];
		} else {
			$this->data['error_address_firstname'] = '';
		}

 		if (isset($this->error['address_lastname'])) {
			$this->data['error_address_lastname'] = $this->error['address_lastname'];
		} else {
			$this->data['error_address_lastname'] = '';
		}
		
		if (isset($this->error['address_address_1'])) {
			$this->data['error_address_address_1'] = $this->error['address_address_1'];
		} else {
			$this->data['error_address_address_1'] = '';
		}
		
		if (isset($this->error['address_city'])) {
			$this->data['error_address_city'] = $this->error['address_city'];
		} else {
			$this->data['error_address_city'] = '';
		}
		
		if (isset($this->error['address_postcode'])) {
			$this->data['error_address_postcode'] = $this->error['address_postcode'];
		} else {
			$this->data['error_address_postcode'] = '';
		}
		
		if (isset($this->error['address_country'])) {
			$this->data['error_address_country'] = $this->error['address_country'];
		} else {
			$this->data['error_address_country'] = '';
		}
		
		if (isset($this->error['address_zone'])) {
			$this->data['error_address_zone'] = $this->error['address_zone'];
		} else {
			$this->data['error_address_zone'] = '';
		}
		
		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		
		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);

		if (!isset($this->request->get['customer_id'])) {
			$this->data['action'] = $this->url->link('sale/customer/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . $url, 'SSL');
		}
		  
    	$this->data['cancel'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL');

    	if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$customer_info = $this->model_sale_customer->getCustomer($this->request->get['customer_id']);
    	}
			
    	if (isset($this->request->post['firstname'])) {
      		$this->data['firstname'] = $this->request->post['firstname'];
		} elseif (isset($customer_info)) { 
			$this->data['firstname'] = $customer_info['firstname'];
		} else {
      		$this->data['firstname'] = '';
    	}

    	if (isset($this->request->post['email'])) {
      		$this->data['email'] = $this->request->post['email'];
    	} elseif (isset($customer_info)) { 
			$this->data['email'] = $customer_info['email'];
		} else {
      		$this->data['email'] = '';
    	}
    	
    	if (isset($this->request->post['lastname'])) {
      		$this->data['lastname'] = $this->request->post['lastname'];
    	} elseif (isset($customer_info)) { 
			$this->data['lastname'] = $customer_info['lastname'];
		} else {
      		$this->data['lastname'] = '';
    	}

		if (isset($this->request->post['telephone'])) {
      		$this->data['telephone'] = $this->request->post['telephone'];
    	} elseif (isset($customer_info)) { 
			$this->data['telephone'] = $customer_info['telephone'];
		} else {
      		$this->data['telephone'] = '';
    	}

    	if (isset($this->request->post['fax'])) {
      		$this->data['fax'] = $this->request->post['fax'];
    	} elseif (isset($customer_info)) { 
			$this->data['fax'] = $customer_info['fax'];
		} else {
      		$this->data['fax'] = '';
    	}

    	if (isset($this->request->post['newsletter'])) {
      		$this->data['newsletter'] = $this->request->post['newsletter'];
    	} elseif (isset($customer_info)) { 
			$this->data['newsletter'] = $customer_info['newsletter'];
		} else {
      		$this->data['newsletter'] = '';
    	}
		
		$this->load->model('sale/customer_group');
			
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

    	if (isset($this->request->post['customer_group_id'])) {
      		$this->data['customer_group_id'] = $this->request->post['customer_group_id'];
    	} elseif (isset($customer_info)) { 
			$this->data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
      		$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
    	}
		
    	if (isset($this->request->post['status'])) {
      		$this->data['status'] = $this->request->post['status'];
    	} elseif (isset($customer_info)) { 
			$this->data['status'] = $customer_info['status'];
		} else {
      		$this->data['status'] = 1;
    	}

    	if (isset($this->request->post['password'])) { 
			$this->data['password'] = $this->request->post['password'];
		} else {
			$this->data['password'] = '';
		}
		
		if (isset($this->request->post['confirm'])) { 
    		$this->data['confirm'] = $this->request->post['confirm'];
		} else {
			$this->data['confirm'] = '';
		}
		
		if (isset($this->request->post['address'])) { 
      		$this->data['addresses'] = $this->request->post['address'];
		} elseif (isset($this->request->get['customer_id'])) {
			$this->data['addresses'] = $this->model_sale_customer->getAddresses($this->request->get['customer_id']);
		} else {
			$this->data['addresses'] = array();
    	}
		
		$this->data['ips'] = array();
    	
		$this->data['country_id'] = $this->config->get('config_country_id');
		
		if (isset($customer_info)) {
			$results = $this->model_sale_customer->getIpsByCustomerId($this->request->get['customer_id']);
		
			foreach ($results as $result) {
				$this->data['ips'][] = array(
					'ip'         => $result['ip'],
					'total'      => $this->model_sale_customer->getTotalCustomersByIp($result['ip']),
					'date_added' => date('d/m/y', strtotime($result['date_added'])),
					'filter_ip'  => HTTPS_SERVER . 'index.php?route=sale/customer&token=' . $this->session->data['token'] . '&filter_ip=' . $result['ip']
				);
			}
		}		
		
		$this->template = 'sale/customer_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
			 
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'sale/customer')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}    

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/customer')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}	
	  	 
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}  
  	} 
	
	public function login() {
		$json = array();
		
		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}
		
		$this->load->model('sale/customer');
				
		$customer_info = $this->model_sale_customer->getCustomer($customer_id);
				
		if ($customer_info) {
			$this->session->data['customer_id'] = $customer_id;
				
			$this->redirect(HTTP_CATALOG);
		} else {
			$this->load_language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->data['heading_title'] = $this->language->get('heading_title');

			$this->data['text_not_found'] = $this->language->get('text_not_found');

			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
			);

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => $this->language->get('text_breadcrumb_separator')
			);
		
			$this->template = 'error/not_found.tpl';
			$this->children = array(
				'common/header',
				'common/footer',
			);
		
			$this->response->setOutput($this->render());
		}
	}
	
	public function transaction() {
    	$this->language->load('sale/customer');
		
		$this->load->model('sale/customer');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/customer')) { 
			$this->model_sale_customer->addTransaction($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['amount']);
				
			$this->data['success'] = $this->language->get('text_success');
		} else {
			$this->data['success'] = '';
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/customer')) {
			$this->data['error_warning'] = $this->language->get('error_permission');
		} else {
			$this->data['error_warning'] = '';
		}		
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_balance'] = $this->language->get('text_balance');
		
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_description'] = $this->language->get('column_description');
		$this->data['column_amount'] = $this->language->get('column_amount');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  
		
		$this->data['transactions'] = array();
			
		$results = $this->model_sale_customer->getTransactions($this->request->get['customer_id'], ($page - 1) * 10, 10);
      		
		foreach ($results as $result) {
        	$this->data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
        		'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
        	);
      	}			
		
		$this->data['balance'] = $this->currency->format($this->model_sale_customer->getTransactionTotal($this->request->get['customer_id']), $this->config->get('config_currency'));
		
		$transaction_total = $this->model_sale_customer->getTotalTransactions($this->request->get['customer_id']);
			
		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page = $page;
		$pagination->limit = 10; 
		$pagination->url = $this->url->link('sale/customer/transaction', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->template = 'sale/customer_transaction.tpl';		
		
		$this->response->setOutput($this->render());
	}
			
	public function reward() {
    	$this->language->load('sale/customer');
		
		$this->load->model('sale/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/customer')) { 
			$this->model_sale_customer->addReward($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['points']);
				
			$this->data['success'] = $this->language->get('text_success');
		} else {
			$this->data['success'] = '';
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/customer')) {
			$this->data['error_warning'] = $this->language->get('error_permission');
		} else {
			$this->data['error_warning'] = '';
		}	
				
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_balance'] = $this->language->get('text_balance');
		
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_description'] = $this->language->get('column_description');
		$this->data['column_points'] = $this->language->get('column_points');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  
		
		$this->data['rewards'] = array();
			
		$results = $this->model_sale_customer->getRewards($this->request->get['customer_id'], ($page - 1) * 10, 10);
      		
		foreach ($results as $result) {
        	$this->data['rewards'][] = array(
				'points'      => $result['points'],
				'description' => $result['description'],
        		'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
        	);
      	}			
		
		$this->data['balance'] = $this->model_sale_customer->getRewardTotal($this->request->get['customer_id']);
		
		$reward_total = $this->model_sale_customer->getTotalRewards($this->request->get['customer_id']);
			
		$pagination = new Pagination();
		$pagination->total = $reward_total;
		$pagination->page = $page;
		$pagination->limit = 10; 
		$pagination->url = $this->url->link('sale/customer/reward', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->template = 'sale/customer_reward.tpl';		
		
		$this->response->setOutput($this->render());
	}

	public function autocomplete() {
		$json = array();
		
		if (isset($this->request->post['filter_name'])) {
			$this->load->model('sale/customer');
			
			$data = array(
				'filter_name' => $this->request->post['filter_name'],
				'start'       => 0,
				'limit'       => 20
			);
		
			$results = $this->model_sale_customer->getCustomers($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'customer_id'    => $result['customer_id'], 
					'name'           => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
					'customer_group' => $result['customer_group'],
					'firstname'      => $result['firstname'],
					'lastname'       => $result['lastname'],
					'email'          => $result['email'],
					'telephone'      => $result['telephone'],
					'fax'            => $result['fax'],
					'address'        => $this->model_sale_customer->getAddresses($result['customer_id'])
				);					
			}
		}

		$sort_order = array();
	  
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);
				
		$this->load->library('json');

		$this->response->setOutput(Json::encode($json));
	}		
	
	public function address() {
		$json = array();
		
		if (isset($this->request->post['address_id']) && $this->request->post['address_id']) {
			$this->load->model('sale/customer');
			
			$json = $this->model_sale_customer->getAddress($this->request->post['address_id']);
		}
		
		$this->load->library('json');

		$this->response->setOutput(Json::encode($json));		
	}
}
?>