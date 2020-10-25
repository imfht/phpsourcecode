<?php
class ControllerCatalogMessage extends Controller { 
	private $error = array();

	public function index() {
		$this->load_language('catalog/message');

		$this->document->setTitle($this->language->get('heading_title'));
		 
		$this->load->model('catalog/message');

		$this->getList();
	}

	
	
	public function update() {
		$this->load_language('catalog/message');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/message');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_message->editMessage($this->request->get['message_id'], $this->request->post['reply']);

			
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
			
			$this->redirect($this->url->link('catalog/message', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
 
	public function delete() {
		$this->load_language('catalog/message');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/message');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $message_id) {
				$this->model_catalog_message->deleteMessage($message_id);
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
			
			$this->redirect($this->url->link('catalog/message', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'author';
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
			'href'      => $this->url->link('catalog/message', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
	
		$this->data['delete'] = $this->url->link('catalog/message/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$this->data['messages'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$information_total = $this->model_catalog_message->getTotalMessages();
	
		$results = $this->model_catalog_message->getMessages($data);
 
    	foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/message/update', 'token=' . $this->session->data['token'] . '&message_id=' . $result['message_id'] . $url, 'SSL')
			);
						
			$this->data['messages'][] = array(
				'message_id'    => $result['message_id'],
				'author'          => $result['author'],
				'email'          => $result['email'],
				'message'          => substr(strip_tags(html_entity_decode($result['message'], ENT_QUOTES, 'UTF-8')), 0, 150) . '..',
				'status'          => $result['status']==0 ? $this->language->get('text_no_reply') : $this->language->get('text_replyed'),
			    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'selected'       => isset($this->request->post['selected']) && in_array($result['message_id'], $this->request->post['selected']),
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
		
		$this->data['sort_title'] = $this->url->link('catalog/message', 'token=' . $this->session->data['token'] . '&sort=id.title' . $url, 'SSL');
		$this->data['sort_sort_order'] = $this->url->link('catalog/message', 'token=' . $this->session->data['token'] . '&sort=i.sort_order' . $url, 'SSL');
		
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
		$pagination->url = $this->url->link('catalog/message', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'catalog/message_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	private function getForm() {
		$this->data['token'] = $this->session->data['token'];

 		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),     		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/message', 'token=' . $this->session->data['token'] , 'SSL'),
      		'separator' => ' :: '
   		);

   		if (isset($this->error['warning'])) {
   			$this->data['error_warning'] = $this->error['warning'];
   		} else {
   			$this->data['error_warning'] = '';
   		}
   		
		$this->data['action'] = $this->url->link('catalog/message/update', 'token=' . $this->session->data['token'] . '&message_id=' . $this->request->get['message_id'] , 'SSL');
		$this->data['cancel'] = $this->url->link('catalog/message', 'token=' . $this->session->data['token'] , 'SSL');
		$this->data['message'] = array();
		if (isset($this->request->get['message_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$this->data['message'] = $this->model_catalog_message->getMessage($this->request->get['message_id']);
		}
				
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (isset($information_info)) {
			$this->data['status'] = $this->data['message']['status'];
		} else {
			$this->data['status'] = 1;
		}
		
		$this->template = 'catalog/message_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/message')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/message')) {
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