<?php
class ControllerTotalVoucher extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load_language('total/voucher');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('voucher', $this->request->post);
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
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
       		'text'      => $this->language->get('text_total'),
			'href'      => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('total/voucher', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['action'] = $this->url->link('total/voucher', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['voucher_status'])) {
			$this->data['voucher_status'] = $this->request->post['voucher_status'];
		} else {
			$this->data['voucher_status'] = $this->config->get('voucher_status');
		}

		if (isset($this->request->post['voucher_sort_order'])) {
			$this->data['voucher_sort_order'] = $this->request->post['voucher_sort_order'];
		} else {
			$this->data['voucher_sort_order'] = $this->config->get('voucher_sort_order');
		}

		$this->template = 'total/voucher.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/voucher')) {
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