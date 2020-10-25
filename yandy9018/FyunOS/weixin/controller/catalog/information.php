<?php
class ControllerCatalogInformation extends Controller { 
	private $error = array();

	public function index() {
		$this->load_language('catalog/information');

		$this->document->setTitle($this->language->get('heading_title'));
		 
		$this->load->model('catalog/information');

		$this->getList();
	}

	public function insert() {
		$this->load_language('catalog/information');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/information');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_information->addInformation($this->request->post);
			
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
			
			$this->redirect($this->url->link('catalog/information', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->load_language('catalog/information');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/information');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_information->editInformation($this->request->get['information_id'], $this->request->post);
			
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
			
			$this->redirect($this->url->link('catalog/information', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
 
	public function delete() {
		$this->load_language('catalog/information');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/information');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $information_id) {
				$this->model_catalog_information->deleteInformation($information_id);
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
			
			$this->redirect($this->url->link('catalog/information', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/information', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
							
		$this->data['insert'] = $this->url->link('catalog/information/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/information/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$this->data['informations'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$information_total = $this->model_catalog_information->getTotalInformations();
	
		$results = $this->model_catalog_information->getInformations($data);
 
    	foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/information/update', 'token=' . $this->session->data['token'] . '&information_id=' . $result['information_id'] . $url, 'SSL')
			);
						
			$this->data['informations'][] = array(
				'information_id' => $result['information_id'],
				'title'          => $result['title'],
				'sort_order'     => $result['sort_order'],
				'selected'       => isset($this->request->post['selected']) && in_array($result['information_id'], $this->request->post['selected']),
				'action'         => $action
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
		
		$this->data['sort_title'] = $this->url->link('catalog/information', 'token=' . $this->session->data['token'] . '&sort=id.title' . $url, 'SSL');
		$this->data['sort_sort_order'] = $this->url->link('catalog/information', 'token=' . $this->session->data['token'] . '&sort=i.sort_order' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $information_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/information', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'catalog/information_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	private function getForm() {
		$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = array();
		}
		
	 	if (isset($this->error['description'])) {
			$this->data['error_description'] = $this->error['description'];
		} else {
			$this->data['error_description'] = array();
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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/information', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
							
		if (!isset($this->request->get['information_id'])) {
			$this->data['action'] = $this->url->link('catalog/information/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/information/update', 'token=' . $this->session->data['token'] . '&information_id=' . $this->request->get['information_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/information', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['information_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$information_info = $this->model_catalog_information->getInformation($this->request->get['information_id']);
		}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['information_description'])) {
			$this->data['information_description'] = $this->request->post['information_description'];
		} elseif (isset($this->request->get['information_id'])) {
			$this->data['information_description'] = $this->model_catalog_information->getInformationDescriptions($this->request->get['information_id']);
		} else {
			$this->data['information_description'] = array();
		}

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (isset($information_info)) {
			$this->data['status'] = $information_info['status'];
		} else {
			$this->data['status'] = 1;
		}
		
		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		if (isset($this->request->post['information_store'])) {
			$this->data['information_store'] = $this->request->post['information_store'];
		} elseif (isset($information_info)) {
			$this->data['information_store'] = $this->model_catalog_information->getInformationStores($this->request->get['information_id']);
		} else {
			$this->data['information_store'] = array(0);
		}		
		
		if (isset($this->request->post['keyword'])) {
			$this->data['keyword'] = $this->request->post['keyword'];
		} elseif (isset($information_info)) {
			$this->data['keyword'] = $information_info['keyword'];
		} else {
			$this->data['keyword'] = '';
		}
		
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (isset($information_info)) {
			$this->data['sort_order'] = $information_info['sort_order'];
		} else {
			$this->data['sort_order'] = '';
		}
		
		if (isset($this->request->post['information_layout'])) {
			$this->data['information_layout'] = $this->request->post['information_layout'];
		} elseif (isset($information_info)) {
			$this->data['information_layout'] = $this->model_catalog_information->getInformationLayouts($this->request->get['information_id']);
		} else {
			$this->data['information_layout'] = array();
		}

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
				
		$this->template = 'catalog/information_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['information_description'] as $language_id => $value) {
			if ((strlen(utf8_decode($value['title'])) < 1) || (strlen(utf8_decode($value['title'])) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}
		
			if (strlen(utf8_decode($value['description'])) < 1) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
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
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/store');
		
		foreach ($this->request->post['selected'] as $information_id) {
			if ($this->config->get('config_account_id') == $information_id) {
				$this->error['warning'] = $this->language->get('error_account');
			}
			
			if ($this->config->get('config_checkout_id') == $information_id) {
				$this->error['warning'] = $this->language->get('error_checkout');
			}
			
			if ($this->config->get('config_affiliate_id') == $information_id) {
				$this->error['warning'] = $this->language->get('error_affiliate');
			}
						
			$store_total = $this->model_setting_store->getTotalStoresByInformationId($information_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
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