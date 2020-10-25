<?php
class ControllerExtensionFeed extends Controller {
	public function index() {
		$this->load_language('extension/feed');
		 
		$this->document->setTitle($this->language->get('heading_title')); 

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];
		
			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getInstalled('feed');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/feed/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('feed', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
						
		$files = glob(DIR_APPLICATION . 'controller/feed/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				if($this->startsWith($extension,'_')==1)
					continue;
				
				$this->load_language('feed/' . $extension);

				$action = array();
			
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->language->get('text_install'),
						'href' => $this->url->link('extension/feed/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
				} else {
					$action[] = array(
						'text' => $this->language->get('text_edit'),
						'href' => $this->url->link('feed/' . $extension . '', 'token=' . $this->session->data['token'], 'SSL')
					);
							
					$action[] = array(
						'text' => $this->language->get('text_uninstall'),
						'href' => $this->url->link('extension/feed/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
				}
									
				$this->data['extensions'][] = array(
					'name'   => $this->language->get('heading_title'),
					'status' => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'action' => $action
				);
			}
		}

		$this->template = 'extension/feed.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
	public function install() {
    	if (!$this->user->hasPermission('modify', 'extension/feed')) {
      		$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
    	} else {
			$this->load->model('setting/extension');
		
			$this->model_setting_extension->install('feed', $this->request->get['extension']);
		
			$this->load->model('user/user_group');
		
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'feed/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'feed/' . $this->request->get['extension']);
		
			require_once(DIR_APPLICATION . 'controller/feed/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerFeed' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'install')) {
				$class->install();
			}
		
			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));			
		}
	}
	
	public function uninstall() {
    	if (!$this->user->hasPermission('modify', 'extension/feed')) {
      		$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
    	} else {		
			$this->load->model('setting/extension');
			$this->load->model('setting/setting');
			
			$this->model_setting_extension->uninstall('feed', $this->request->get['extension']);
		
			$this->model_setting_setting->deleteSetting($this->request->get['extension']);
		
			require_once(DIR_APPLICATION . 'controller/feed/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerFeed' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
		
			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
}
?>