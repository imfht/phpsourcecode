<?php 
class ControllerLocalisationLogistics extends Controller {
	private $error = array(); 
   
  	public function index() {
		$this->load_language('localisation/logistics');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/logistics');
		
    	$this->getList();
  	}
              
  	public function insert() {
		$this->load_language('localisation/logistics');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/logistics');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      		$this->model_localisation_logistics->addLogistics($this->request->post);
		  	
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
						
      		$this->redirect($this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
	
    	$this->getForm();
  	}

  	public function update() {
		$this->load_language('localisation/logistics');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/logistics');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	  		$this->model_localisation_logistics->editLogistics($this->request->get['logistics_id'], $this->request->post);
			
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
			
			$this->redirect($this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getForm();
  	}

  	public function delete() {
		$this->load_language('localisation/logistics');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/logistics');
		
    	if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $logistics_id) {
				$this->model_localisation_logistics->deleteLogistics($logistics_id);
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
			
			$this->redirect($this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . $url, 'SSL'));
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
			'href'      => $this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
							
		$this->data['insert'] = $this->url->link('localisation/logistics/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('localisation/logistics/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$this->data['logisticses'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$logistics_total = $this->model_localisation_logistics->getTotalLogistics();
	
		$results = $this->model_localisation_logistics->getLogisticses($data);
 
    	foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('localisation/logistics/update', 'token=' . $this->session->data['token'] . '&logistics_id=' . $result['logistics_id'] . $url, 'SSL')
			);
						
			$this->data['logisticses'][] = array(
				'logistics_id' => $result['logistics_id'],
				'name'            => $result['logistics_name'] . (($result['logistics_id'] == $this->config->get('config_logistics_id')) ? $this->language->get('text_default') : null),
				'selected'        => isset($this->request->post['selected']) && in_array($result['logistics_id'], $this->request->post['selected']),
				'action'          => $action
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
		
		$this->data['sort_name'] = $this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $logistics_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'localisation/logistics_list.tpl';
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
			$this->data['error_name'] = array();
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
			'href'      => $this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		if (!isset($this->request->get['logistics_id'])) {
			$this->data['action'] = $this->url->link('localisation/logistics/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('localisation/logistics/update', 'token=' . $this->session->data['token'] . '&logistics_id=' . $this->request->get['logistics_id'] . $url, 'SSL');
		}
			
		$this->data['cancel'] = $this->url->link('localisation/logistics', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['logistics'])) {
			$this->data['logistics'] = $this->request->post['logistics'];
		} elseif (isset($this->request->get['logistics_id'])) {
			$this->data['logistics'] = $this->model_localisation_logistics->getLogisticsDescriptions($this->request->get['logistics_id']);
		} else {
			$this->data['logistics'] = array();
		}

		$this->template = 'localisation/logistics_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
  	}
  	
	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'localisation/logistics')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
	
    	if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

  	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/logistics')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
	
		
		if (!$this->error) { 
	  		return true;
		} else {
	  		return false;
		}
  	}	  
}
?>