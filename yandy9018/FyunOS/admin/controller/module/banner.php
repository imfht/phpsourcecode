<?php
class ControllerModuleBanner extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load_language('module/banner');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('banner', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['dimension'])) {
			$this->data['error_dimension'] = $this->error['dimension'];
		} else {
			$this->data['error_dimension'] = array();
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
			'href'      => $this->url->link('module/banner', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['action'] = $this->url->link('module/banner', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['modules'] = array();
		
		if (isset($this->request->post['banner_module'])) {
			$this->data['modules'] = $this->request->post['banner_module'];
		} elseif ($this->config->get('banner_module')) { 
			$this->data['modules'] = $this->config->get('banner_module');
		}	
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->load->model('design/banner');
		
		$this->data['banners'] = $this->model_design_banner->getBanners();
				
		$this->template = 'module/banner.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();	
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/banner')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['banner_module'])) {
			foreach ($this->request->post['banner_module'] as $key => $value) {
				if (!$value['width'] || !$value['height']) {
					$this->error['dimension'][$key] = $this->language->get('error_dimension');
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