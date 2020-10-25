<?php 
class ControllerPaymentBankTransfer extends Controller {
	private $error = array(); 

	public function index() {
		$this->load_language('payment/bank_transfer');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('bank_transfer', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (isset($this->error['bank_' . $language['language_id']])) {
				$this->data['error_bank_' . $language['language_id']] = $this->error['bank_' . $language['language_id']];
			} else {
				$this->data['error_bank_' . $language['language_id']] = '';
			}
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/bank_transfer', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
				
		$this->data['action'] = $this->url->link('payment/bank_transfer', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('localisation/language');
		
		foreach ($languages as $language) {
			if (isset($this->request->post['bank_transfer_bank_' . $language['language_id']])) {
				$this->data['bank_transfer_bank_' . $language['language_id']] = $this->request->post['bank_transfer_bank_' . $language['language_id']];
			} else {
				$this->data['bank_transfer_bank_' . $language['language_id']] = $this->config->get('bank_transfer_bank_' . $language['language_id']);
			}
		}
		
		$this->data['languages'] = $languages;
		
		if (isset($this->request->post['bank_transfer_total'])) {
			$this->data['bank_transfer_total'] = $this->request->post['bank_transfer_total'];
		} else {
			$this->data['bank_transfer_total'] = $this->config->get('bank_transfer_total'); 
		} 
				
		if (isset($this->request->post['bank_transfer_order_status_id'])) {
			$this->data['bank_transfer_order_status_id'] = $this->request->post['bank_transfer_order_status_id'];
		} else {
			$this->data['bank_transfer_order_status_id'] = $this->config->get('bank_transfer_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['bank_transfer_geo_zone_id'])) {
			$this->data['bank_transfer_geo_zone_id'] = $this->request->post['bank_transfer_geo_zone_id'];
		} else {
			$this->data['bank_transfer_geo_zone_id'] = $this->config->get('bank_transfer_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['bank_transfer_status'])) {
			$this->data['bank_transfer_status'] = $this->request->post['bank_transfer_status'];
		} else {
			$this->data['bank_transfer_status'] = $this->config->get('bank_transfer_status');
		}
		
		if (isset($this->request->post['bank_transfer_sort_order'])) {
			$this->data['bank_transfer_sort_order'] = $this->request->post['bank_transfer_sort_order'];
		} else {
			$this->data['bank_transfer_sort_order'] = $this->config->get('bank_transfer_sort_order');
		}
		

		$this->template = 'payment/bank_transfer.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();	
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/bank_transfer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (!$this->request->post['bank_transfer_bank_' . $language['language_id']]) {
				$this->error['bank_' .  $language['language_id']] = $this->language->get('error_bank');
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