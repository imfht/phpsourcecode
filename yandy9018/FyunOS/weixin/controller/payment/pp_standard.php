<?php
class ControllerPaymentPPStandard extends Controller {
	private $error = array();

	public function index() {
		$this->load_language('payment/pp_standard');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pp_standard', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
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
			'href'      => $this->url->link('payment/pp_standard', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);

		$this->data['action'] = $this->url->link('payment/pp_standard', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pp_standard_email'])) {
			$this->data['pp_standard_email'] = $this->request->post['pp_standard_email'];
		} else {
			$this->data['pp_standard_email'] = $this->config->get('pp_standard_email');
		}

		if (isset($this->request->post['pp_standard_test'])) {
			$this->data['pp_standard_test'] = $this->request->post['pp_standard_test'];
		} else {
			$this->data['pp_standard_test'] = $this->config->get('pp_standard_test');
		}

		if (isset($this->request->post['pp_standard_transaction'])) {
			$this->data['pp_standard_transaction'] = $this->request->post['pp_standard_transaction'];
		} else {
			$this->data['pp_standard_transaction'] = $this->config->get('pp_standard_transaction');
		}

		if (isset($this->request->post['pp_standard_pdt_token'])) {
			$this->data['pp_standard_pdt_token'] = $this->request->post['pp_standard_pdt_token'];
		} else {
			$this->data['pp_standard_pdt_token'] = $this->config->get('pp_standard_pdt_token');
		}

		if (isset($this->request->post['pp_standard_debug'])) {
			$this->data['pp_standard_debug'] = $this->request->post['pp_standard_debug'];
		} else {
			$this->data['pp_standard_debug'] = $this->config->get('pp_standard_debug');
		}
		
		if (isset($this->request->post['pp_standard_total'])) {
			$this->data['pp_standard_total'] = $this->request->post['pp_standard_total'];
		} else {
			$this->data['pp_standard_total'] = $this->config->get('pp_standard_total'); 
		} 

		if (isset($this->request->post['pp_standard_canceled_reversal_status_id'])) {
			$this->data['pp_standard_canceled_reversal_status_id'] = $this->request->post['pp_standard_canceled_reversal_status_id'];
		} else {
			$this->data['pp_standard_canceled_reversal_status_id'] = $this->config->get('pp_standard_canceled_reversal_status_id');
		}
		
		if (isset($this->request->post['pp_standard_completed_status_id'])) {
			$this->data['pp_standard_completed_status_id'] = $this->request->post['pp_standard_completed_status_id'];
		} else {
			$this->data['pp_standard_completed_status_id'] = $this->config->get('pp_standard_completed_status_id');
		}	
		
		if (isset($this->request->post['pp_standard_denied_status_id'])) {
			$this->data['pp_standard_denied_status_id'] = $this->request->post['pp_standard_denied_status_id'];
		} else {
			$this->data['pp_standard_denied_status_id'] = $this->config->get('pp_standard_denied_status_id');
		}
		
		if (isset($this->request->post['pp_standard_expired_status_id'])) {
			$this->data['pp_standard_expired_status_id'] = $this->request->post['pp_standard_expired_status_id'];
		} else {
			$this->data['pp_standard_expired_status_id'] = $this->config->get('pp_standard_expired_status_id');
		}
				
		if (isset($this->request->post['pp_standard_failed_status_id'])) {
			$this->data['pp_standard_failed_status_id'] = $this->request->post['pp_standard_failed_status_id'];
		} else {
			$this->data['pp_standard_failed_status_id'] = $this->config->get('pp_standard_failed_status_id');
		}	
								
		if (isset($this->request->post['pp_standard_pending_status_id'])) {
			$this->data['pp_standard_pending_status_id'] = $this->request->post['pp_standard_pending_status_id'];
		} else {
			$this->data['pp_standard_pending_status_id'] = $this->config->get('pp_standard_pending_status_id');
		}
									
		if (isset($this->request->post['pp_standard_processed_status_id'])) {
			$this->data['pp_standard_processed_status_id'] = $this->request->post['pp_standard_processed_status_id'];
		} else {
			$this->data['pp_standard_processed_status_id'] = $this->config->get('pp_standard_processed_status_id');
		}

		if (isset($this->request->post['pp_standard_refunded_status_id'])) {
			$this->data['pp_standard_refunded_status_id'] = $this->request->post['pp_standard_refunded_status_id'];
		} else {
			$this->data['pp_standard_refunded_status_id'] = $this->config->get('pp_standard_refunded_status_id');
		}

		if (isset($this->request->post['pp_standard_reversed_status_id'])) {
			$this->data['pp_standard_reversed_status_id'] = $this->request->post['pp_standard_reversed_status_id'];
		} else {
			$this->data['pp_standard_reversed_status_id'] = $this->config->get('pp_standard_reversed_status_id');
		}

		if (isset($this->request->post['pp_standard_voided_status_id'])) {
			$this->data['pp_standard_voided_status_id'] = $this->request->post['pp_standard_voided_status_id'];
		} else {
			$this->data['pp_standard_voided_status_id'] = $this->config->get('pp_standard_voided_status_id');
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['pp_standard_geo_zone_id'])) {
			$this->data['pp_standard_geo_zone_id'] = $this->request->post['pp_standard_geo_zone_id'];
		} else {
			$this->data['pp_standard_geo_zone_id'] = $this->config->get('pp_standard_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['pp_standard_status'])) {
			$this->data['pp_standard_status'] = $this->request->post['pp_standard_status'];
		} else {
			$this->data['pp_standard_status'] = $this->config->get('pp_standard_status');
		}
		
		if (isset($this->request->post['pp_standard_sort_order'])) {
			$this->data['pp_standard_sort_order'] = $this->request->post['pp_standard_sort_order'];
		} else {
			$this->data['pp_standard_sort_order'] = $this->config->get('pp_standard_sort_order');
		}

		$this->template = 'payment/pp_standard.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();	
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/pp_standard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['pp_standard_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>