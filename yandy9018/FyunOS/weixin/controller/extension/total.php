<?php
class ControllerExtensionTotal extends Controller {
	public function index() {
		$this->load_language('extension/total');
		 
		$this->document->setTitle($this->language->get('heading_title')); 

   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
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

		$extensions = $this->model_setting_extension->getInstalled('total');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/total/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('total', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
				
		$files = glob(DIR_APPLICATION . 'controller/total/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				if($this->startsWith($extension,'_')==1)
					continue;
				
				$this->load->language('total/' . $extension);
	
				$action = array();
				$installed=true;
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->language->get('text_install'),
						'href' => $this->url->link('extension/total/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
					$installed=false;
				} else {
					$action[] = array(
						'text' => $this->language->get('text_edit'),
						'href' => $this->url->link('total/' . $extension . '', 'token=' . $this->session->data['token'], 'SSL')
					);
								
					$action[] = array(
						'text' => $this->language->get('text_uninstall'),
						'href' => $this->url->link('extension/total/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
				}
										
				$this->data['extensions'][] = array(
					'name'       => $this->language->get('heading_title'),
					'installed' => $installed,
					'status'     => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'sort_order' => $this->config->get($extension . '_sort_order'),
					'action'     => $action
				);
			}
		}

		$this->template = 'extension/total.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
	public function install() {
		if (!$this->user->hasPermission('modify', 'extension/total')) {
			$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		} else {				
			$this->load->model('setting/extension');
		
			$this->model_setting_extension->install('total', $this->request->get['extension']);

			$this->load->model('user/user_group');
		
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/' . $this->request->get['extension']);

			require_once(DIR_APPLICATION . 'controller/total/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerTotal' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'install')) {
				$class->install();
			}
			
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
	
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/total')) {
			$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		} else {			
			$this->load->model('setting/extension');
			$this->load->model('setting/setting');
		
			$this->model_setting_extension->uninstall('total', $this->request->get['extension']);
		
			$this->model_setting_setting->deleteSetting($this->request->get['extension']);
		
			require_once(DIR_APPLICATION . 'controller/total/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerTotal' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
		
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}	
}
?>