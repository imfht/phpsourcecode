<?php
class ControllerCatalogNav extends Controller { 
	private $error = array();

	public function index() {
		$this->load_language('catalog/nav');

		$this->document->setTitle($this->language->get('heading_title'));
		 
		$this->load->model('catalog/nav');

		$this->getList();
	}

	public function insert() {
		$this->load_language('catalog/nav');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/nav');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_nav->addNav($this->request->post);
			
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
			
			$this->redirect($this->url->link('catalog/nav', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->load_language('catalog/nav');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/nav');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_nav->editNav($this->request->get['nav_id'], $this->request->post);
			
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
			
			$this->redirect($this->url->link('catalog/nav', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
 
	public function delete() {
		$this->load_language('catalog/nav');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/nav');
		
		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $nav_id) {
				$this->model_catalog_nav->deleteNav($nav_id);
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
			
			$this->redirect($this->url->link('catalog/nav', 'token=' . $this->session->data['token'] . $url, 'SSL'));
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
		
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		

  	
		$this->data['insert'] = $this->url->link('catalog/nav/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/nav/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$this->data['navs'] = array();

		
	
	
		$navs = $this->model_catalog_nav->getNavs();
        foreach($navs as $i){
				$i['update'] = $this->url->link('catalog/nav/update&nav_id='.$i['nav_id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
				$this->data['navs'][] = $i;	
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

	
		
		$this->data['sort_title'] = $this->url->link('catalog/nav', 'token=' . $this->session->data['token'] . '&sort=id.title' . $url, 'SSL');
		$this->data['sort_sort_order'] = $this->url->link('catalog/nav', 'token=' . $this->session->data['token'] . '&sort=i.sort_order' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'catalog/nav_list.tpl';
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
		
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		
							
		if (!isset($this->request->get['nav_id'])) {
			$this->data['action'] = $this->url->link('catalog/nav/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/nav/update', 'token=' . $this->session->data['token'] . '&nav_id=' . $this->request->get['nav_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/nav', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['nav_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$nav_info = $this->model_catalog_nav->getNav($this->request->get['nav_id']);
		}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
	

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (isset($nav_info)) {
			$this->data['status'] = $nav_info['status'];
		} else {
			$this->data['status'] = 1;
		}
		
		
		if (isset($this->request->post['ishome'])) {
			$this->data['ishome'] = $this->request->post['ishome'];
		} elseif (isset($nav_info)) {
			$this->data['ishome'] = $nav_info['ishome'];
		} else {
			$this->data['ishome'] = 0;
		}
		
		
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (isset($nav_info)) {
			$this->data['sort_order'] = $nav_info['sort_order'];
		} else {
			$this->data['sort_order'] = '';
		}
		
		if (isset($this->request->post['title'])) {
			$this->data['title'] = $this->request->post['title'];
		} elseif (isset($nav_info)) {
			$this->data['title'] = $nav_info['title'];
		} else {
			$this->data['title'] = '';
		}
		
		if (isset($this->request->post['url'])) {
			$this->data['url'] = $this->request->post['url'];
		} elseif (isset($nav_info)) {
			$this->data['url'] = $nav_info['url'];
		} else {
			$this->data['url'] = '';
		}
		
		if (isset($this->request->post['tid'])) {
			$this->data['tid'] = $this->request->post['tid'];
		} elseif (isset($nav_info)) {
			$this->data['tid'] = $nav_info['tid'];
		} else {
			$this->data['tid'] = '';
		}
		
	

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
				
		$this->template = 'catalog/nav_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/nav')) {
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

	
}
?>