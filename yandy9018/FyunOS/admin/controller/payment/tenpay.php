<?php 
class ControllerPaymentTenpay extends Controller {
	private $error = array(); 

	public function index() {
		$this->load_language('payment/tenpay');

		$this->document->settitle($this->language->get('heading_title'));
		
		if (isset($this->error['bargainor_id'])) {
			$this->data['error_bargainor_id'] = $this->error['bargainor_id'];
		} else {
			$this->data['error_bargainor_id'] = '';
		}

		if (isset($this->error['key'])) {
			$this->data['error_key'] = $this->error['key'];
		} else {
			$this->data['error_key'] = '';
		}
		
		if (isset($this->error['seller'])) {
			$this->data['error_seller'] = $this->error['seller'];
		} else {
			$this->data['error_seller'] = '';
		}
		
   		$this->data['breadcrumbs']  = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_payment'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/tenpay&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
   		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('tenpay', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect( HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}


		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/tenpay&token=' . $this->session->data['token'];
		
		$this->data['cancel'] =  HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		
		if (isset($this->request->post['tenpay_bargainor_id'])) {
			$this->data['tenpay_bargainor_id'] = $this->request->post['tenpay_bargainor_id'];
		} else {
			$this->data['tenpay_bargainor_id'] = $this->config->get('tenpay_bargainor_id');
		}

		if (isset($this->request->post['tenpay_seller'])) {
			$this->data['tenpay_seller'] = $this->request->post['tenpay_seller'];
		} else {
			$this->data['tenpay_seller'] = $this->config->get('tenpay_seller');
		}		

		if (isset($this->request->post['tenpay_key'])) {
			$this->data['tenpay_key'] = $this->request->post['tenpay_key'];
		} else {
			$this->data['tenpay_key'] = $this->config->get('tenpay_key');
		}
		
		if (isset($this->request->post['tenpay_mch_type'])) {
			$this->data['tenpay_mch_type'] = $this->request->post['tenpay_mch_type'];
		} else {
			$this->data['tenpay_mch_type'] = $this->config->get('tenpay_mch_type');
		}
		

		if (isset($this->request->post['tenpay_cmdno'])) {
			$this->data['tenpay_cmdno'] = $this->request->post['tenpay_cmdno'];
		} else {
			$this->data['tenpay_cmdno'] = $this->config->get('tenpay_cmdno');
		}
		
	/*	if (isset($this->request->post['tenpay_anti_phishing'])) {
			$this->data['tenpay_anti_phishing'] = $this->request->post['tenpay_anti_phishing'];
		} else {
			$this->data['tenpay_anti_phishing'] = $this->config->get('tenpay_anti_phishing');
		}
		*/
		if (isset($this->request->post['tenpay_order_status_id'])) {
			$this->data['tenpay_order_status_id'] = $this->request->post['tenpay_order_status_id'];
		} else {
			$this->data['tenpay_order_status_id'] = $this->config->get('tenpay_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
			
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['tenpay_status'])) {
			$this->data['tenpay_status'] = $this->request->post['tenpay_status'];
		} else {
			$this->data['tenpay_status'] = $this->config->get('tenpay_status');
		}
		
		if (isset($this->request->post['tenpay_sort_order'])) {
			$this->data['tenpay_sort_order'] = $this->request->post['tenpay_sort_order'];
		} else {
			$this->data['tenpay_sort_order'] = $this->config->get('tenpay_sort_order');
		}
		
		$this->template = 'payment/tenpay.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();	
	}


	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/tenpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
	
		if (!$this->request->post['tenpay_bargainor_id']) {
			$this->error['bargainor_id'] = $this->language->get('error_bargainor_id');
		}

		if (!$this->request->post['tenpay_seller']) {
			$this->error['seller'] = $this->language->get('error_seller');
		}

		if (!$this->request->post['tenpay_key']) {
			$this->error['key'] = $this->language->get('tenpay_key');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>