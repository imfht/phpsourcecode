<?php
class ControllerSettingSetting extends Controller {
	private $error = array();
 
	public function index() {
		$this->load_language('setting/setting'); 
		
		$this->document->setTitle($this->language->get('heading_title_store_setting'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->updateSetting('config', $this->request->post);

			if ($this->config->get('config_currency_auto')) {
				$this->load->model('localisation/currency');
		
				$this->model_localisation_currency->updateCurrencies();
			}	
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['token'] = $this->session->data['token'];

		$error_keys=array(
			'warning' => 'error_warning',
			'name' => 'error_name',
			'owner' => 'error_owner',
			'address' => 'error_address',
			'email' => 'error_email',
			'telephone' => 'error_telephone',
			'title' => 'error_title',
			'image_thumb' => 'error_image_thumb',
			'image_popup' => 'error_image_popup',
			'image_product' => 'error_image_product',
			'image_category' => 'error_image_category',
			'image_manufacturer' => 'error_image_manufacturer',
			'image_additional' => 'error_image_additional',
			'image_related' => 'error_image_related',
			'image_compare' => 'error_image_compare',
			'image_wishlist' => 'error_image_additional',
			'image_cart' => 'error_image_cart',
			'error_filename' => 'error_error_filename',
			'catalog_limit' => 'error_catalog_limit',
			'image_wishlist' =>'error_image_wishlist'
		);
		
		foreach ($error_keys as $key => $value) {
			if (isset($this->error[$key])) {
				$this->data[$value] = $this->error[$key];
			} else {
				$this->data[$value] = '';
			}
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
   		
   		$this->data['breadcrumbs'][] = array(
   		    'text'      => $this->language->get('heading_title_store_setting'),
   			'href'      => $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL'),
   		    'separator' => $this->language->get('text_breadcrumb_separator')
   		);
   		
   	
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL');

		$post_keys=array(
			'config_name',
			'config_owner',
			'config_home_image_s',
			'config_color_css',
			'config_address',
			'config_email',
			'config_telephone',
			'config_fax',
			'config_title',
			'config_home_banner',
			'config_home_search',
			'config_home_image',
			'config_meta_description',
			'config_layout_id',
			'config_zone_id',
			'config_template',
			'config_country_id',
			'config_language',
			'config_meta_keyword',
			'config_currency',
			'config_catalog_limit',
			'config_hoursfrom',
			'config_hoursto',
			'config_sms_notice',
			'config_sms_notice_mobile',
			'config_print_notice',
			'config_tax',
			'config_seat',
			'config_latlng',
			'config_distribution',
			'config_customer_group_id',
			'config_customer_price',
			'config_customer_approval',
			'config_guest_checkout',
			'config_account_id',
			'config_checkout_id',
			'config_stock_checkout',
			'config_order_nopay_status_id',
			'config_order_status_id',
			'config_logo',
			'config_stock_display',
			'config_icon'
		);
		
		foreach ($post_keys as $value) {
			if (isset($this->request->post[$value])) {
				$this->data[$value] = $this->request->post[$value];
			} else {
				$this->data[$value] = $this->config->get($value);
			}
		}
		
		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->data['templates'] = array();
		$directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);
		foreach ($directories as $directory) {
			$this->data['templates'][] = basename($directory);
		}					

		$dirnow=DIR_CATALOG. 'view/theme/' .$this->config->get('config_template') . '/stylesheet/skins'; 
		$colorCss= scandir($dirnow, 1);//目录中文件
		foreach($colorCss as $i){
			if($i!='.' && $i!='..'){
				 $this->data['color_css'][] = $i;
				}
		}
		$this->load->model('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();

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
				
		$this->load->model('tool/image');

		if ($this->config->get('config_logo')) {
			$this->data['logo'] = $this->config->get('config_logo').'!list';	
		} else {
			$this->data['logo'] = "http://fyunimage.b0.upaiyun.com/no_image.jpg!list";
		}

		
		if ($this->config->get('config_home_image')) {
			$this->data['home_image'] = $this->config->get('config_home_image');		
		} else {
			$this->data['home_image'] = "http://fyunimage.b0.upaiyun.com/no_image.jpg!100list";
		}
		
		$this->template = 'setting/setting.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'setting/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['config_name']) {
			$this->error['name'] = $this->language->get('error_name');
		}	
		
		if ((strlen(utf8_decode($this->request->post['config_owner'])) < 1) || (strlen(utf8_decode($this->request->post['config_owner'])) > 64)) {
			$this->error['owner'] = $this->language->get('error_owner');
		}

		if ((strlen(utf8_decode($this->request->post['config_address'])) < 1) || (strlen(utf8_decode($this->request->post['config_address'])) > 256)) {
			$this->error['address'] = $this->language->get('error_address');
		}
		
    	if ((strlen(utf8_decode($this->request->post['config_email'])) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['config_email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

    	if ((strlen(utf8_decode($this->request->post['config_telephone'])) < 1) || (strlen(utf8_decode($this->request->post['config_telephone'])) > 32)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
    	}

		if (!$this->request->post['config_title']) {
			$this->error['title'] = $this->language->get('error_title');
		}	
		
	
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
			
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function template() {
		$template = basename($this->request->get['template']);
		
		if (file_exists(DIR_IMAGE . 'templates/' . $template . '.png')) {
			$image = HTTPS_IMAGE . 'templates/' . $template . '.png';
		} else {
			$image = HTTPS_IMAGE . 'no_image.jpg';
		}
		
		$this->response->setOutput('<img src="' . $image . '" alt="" title="" style="border: 1px solid #EEEEEE;" />');
	}		
	
	public function updateStatus(){
		$this->load->model('setting/setting');
	    $status = $this->model_setting_setting->updateStatus($this->request->get['status']);	
        $this->response->setOutput($status);
	}	
	
}
?>