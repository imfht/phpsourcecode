<?php
class ControllerExtensionShipping extends Controller {
	public function index() {
		$this->load_language('extension/shipping');
		 
		$this->document->setTitle($this->language->get('heading_title')); 
  		
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
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

		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->updateSetting('config', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
				
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		
		$extensions = $this->model_setting_extension->getInstalled('shipping');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/shipping/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('shipping', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/shipping/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				if($this->startsWith($extension,'_')==1)
					continue;
				
				$this->load->language('shipping/' . $extension);
	
				$action = array();
				$installed=true;
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->language->get('text_install'),
						'href' => $this->url->link('extension/shipping/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
					$installed=false;
				} else {
					$action[] = array(
						'text' => $this->language->get('text_edit'),
						'href' => $this->url->link('shipping/' . $extension . '', 'token=' . $this->session->data['token'], 'SSL')
					);
								
					$action[] = array(
						'text' => $this->language->get('text_uninstall'),
						'href' => $this->url->link('extension/shipping/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
				}
	
				$this->data['default_shpping'] =$this->config->get('config_default_shipping');
				
				$this->data['extensions'][] = array(
					'name'       => $this->language->get('heading_title'),
					'code'       => $extension,
					'status'     => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'installed' => $installed,
					'sort_order' => $this->config->get($extension . '_sort_order'),
					'action'     => $action
				);
			}
		}
	
		$this->data['default'] =$this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->template = 'extension/shipping.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
	public function install() {
		if (!$this->user->hasPermission('modify', 'extension/shipping')) {
			$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		} else {		
			$this->load->model('setting/extension');
		
			$this->model_setting_extension->install('shipping', $this->request->get['extension']);

			$this->load->model('user/user_group');
		
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'shipping/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'shipping/' . $this->request->get['extension']);

			require_once(DIR_APPLICATION . 'controller/shipping/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerShipping' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'install')) {
				$class->install();
			}
			
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
	
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/shipping')) {
			$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		} else {		
			$this->load->model('setting/extension');
			$this->load->model('setting/setting');
				
			$this->model_setting_extension->uninstall('shipping', $this->request->get['extension']);
		
			$this->model_setting_setting->deleteSetting($this->request->get['extension']);
		
			require_once(DIR_APPLICATION . 'controller/shipping/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerShipping' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
		
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
}
?>