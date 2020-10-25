<?php
class ControllerSettingCustom extends Controller {
	private $error = array();
 
	public function index() {
		$this->load_language('setting/setting'); 

		$this->document->setTitle($this->language->get('heading_title_custom'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->updateSetting('config', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('setting/custom', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title_custom');

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
       		'text'      => $this->language->get('heading_title_custom'),
			'href'      => $this->url->link('setting/custom', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('setting/custom', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$post_keys=array(
			'config_active',
			'config_review',
		    'config_bind_status',
		    'config_phone_login',
			'config_captcha',
			'config_store_type',
			'config_invite_points',
			'config_admin_language',
			'config_currency_auto',
			'config_length_class_id',
			'config_weight_class_id',
			'config_admin_limit',
			'config_stock_warning',
			'config_affiliate_id',
			'config_complete_status_id',
			'config_download',
			'config_upload_allowed',
			'config_return_status_id',
			'config_review_status',
			'config_stock_status_id',
			'config_cart_weight'
		);
		
		foreach ($post_keys as $value) {
			if (isset($this->request->post[$value])) {
				$this->data[$value] = $this->request->post[$value];
			} else {
				$this->data[$value] = $this->config->get($value);
			}
		}
		
		$this->load->model('localisation/length_class');
		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
		
		$this->load->model('localisation/weight_class');
		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->load->model('localisation/currency');
		$this->data['currencies'] = $this->model_localisation_currency->getCurrencies();
		
		$this->load->model('sale/customer_group');
		
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		
		if (isset($this->request->post['config_commission'])) {
			$this->data['config_commission'] = $this->request->post['config_commission'];
		} elseif ($this->config->has('config_commission')) {
			$this->data['config_commission'] = $this->config->get('config_commission');
		} else {
			$this->data['config_commission'] = '5.00';
		}
		
		$this->load->model('catalog/information');
		
		$this->data['informations'] = $this->model_catalog_information->getInformations();
		
		$this->load->model('localisation/stock_status');
		
		$this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$this->load->model('localisation/return_status');
		
		$this->data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();
		
		if (isset($this->request->post['config_invoice_prefix'])) {
			$this->data['config_invoice_prefix'] = $this->request->post['config_invoice_prefix'];
		} elseif ($this->config->get('config_invoice_prefix')) {
			$this->data['config_invoice_prefix'] = $this->config->get('config_invoice_prefix');
		} else {
			$this->data['config_invoice_prefix'] = 'INV-' . date('Y') . '-00';
		}
		
		$error_keys=array(
			'warning' => 'error_warning',
			'admin_limit' => 'error_admin_limit'
		);
		
		foreach ($error_keys as $key => $value) {
			if (isset($this->error[$key])) {
				$this->data[$value] = $this->error[$key];
			} else {
				$this->data[$value] = '';
			}
		}

		$this->template = 'setting/custom.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'setting/custom')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['config_admin_limit']) {
			$this->error['admin_limit'] = $this->language->get('error_limit');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	
}
?>