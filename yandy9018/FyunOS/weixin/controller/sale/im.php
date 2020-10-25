<?php 
class ControllerSaleIm extends Controller {
	private $error = array();
	 
	public function index() {
		$this->load_language('sale/im');
 
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['token'] = $this->session->data['token'];
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
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
			'href'      => $this->url->link('sale/im', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
   		$this->data['ims'] = array();
   		
		$this->data['action'] = $this->url->link('sale/im', 'token=' . $this->session->data['token'], 'SSL');
    	$this->data['cancel'] = $this->url->link('sale/im', 'token=' . $this->session->data['token'], 'SSL');
		$this->load->model('setting/setting');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ims', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('sale/im', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
			
		
		if (isset($this->request->post['ims'])) {
			$this->data['ims'] = $this->request->post['ims'];
		} elseif ($this->config->get('ims')) { 
			$this->data['ims'] = $this->config->get('ims');
		}	
		
		$this->template = 'sale/im_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'sale/im')) {
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