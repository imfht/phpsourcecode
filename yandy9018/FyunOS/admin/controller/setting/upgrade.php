<?php
class ControllerSettingUpgrade extends Controller {
	private $error = array();
 
	public function index() {
		$this->load_language('setting/upgrade'); 

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['text_current_version'] = sprintf($this->language->get('text_current_version'), VERSION);
		
		$this->data['token'] = $this->session->data['token'];

 		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('setting/upgrade', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
	
		$this->template = 'setting/upgrade.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
}
?>