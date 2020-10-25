<?php
class ControllerModuleLatest extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load_language('module/latest');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('latest', $this->request->post);		
			
			$this->cache->delete('product');
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/latest', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['action'] = $this->url->link('module/latest', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['modules'] = array();
		
		if (isset($this->request->post['latest_module'])) {
			$this->data['modules'] = $this->request->post['latest_module'];
		} elseif ($this->config->get('latest_module')) { 
			$this->data['modules'] = $this->config->get('latest_module');
		}				
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/latest.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();	
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/latest')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['latest_module'])) {
			foreach ($this->request->post['latest_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
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