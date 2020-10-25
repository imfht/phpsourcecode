<?php
class ControllerShippingWeight extends Controller { 
	private $error = array();
	
	public function index() {  
		$this->load_language('shipping/weight');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				 
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('weight', $this->request->post);	

			$this->session->data['success'] = $this->language->get('text_success');
									
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('shipping/weight', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['action'] = $this->url->link('shipping/weight', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'); 

		$this->load->model('localisation/geo_zone');
		
		$geo_zones = $this->model_localisation_geo_zone->getGeoZones();
		
		foreach ($geo_zones as $geo_zone) {
			if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_rate'])) {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_rate'];
			} else {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_rate');
			}		
			
			if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_time'])) {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_time'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_time'];
			} else {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_time'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_time');
			}		
			
			if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_status'])) {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_status');
			}		
		}
		
		$this->data['geo_zones'] = $geo_zones;

		if (isset($this->request->post['weight_tax_class_id'])) {
			$this->data['weight_tax_class_id'] = $this->request->post['weight_tax_class_id'];
		} else {
			$this->data['weight_tax_class_id'] = $this->config->get('weight_tax_class_id');
		}
		
		if (isset($this->request->post['weight_status'])) {
			$this->data['weight_status'] = $this->request->post['weight_status'];
		} else {
			$this->data['weight_status'] = $this->config->get('weight_status');
		}
		
		if (isset($this->request->post['weight_description'])) {
			$this->data['weight_description'] = $this->request->post['weight_description'];
		} else {
			$this->data['weight_description'] = $this->config->get('weight_description');
		}
		
		if (isset($this->request->post['weight_sort_order'])) {
			$this->data['weight_sort_order'] = $this->request->post['weight_sort_order'];
		} else {
			$this->data['weight_sort_order'] = $this->config->get('weight_sort_order');
		}	
		
		$this->load->model('localisation/tax_class');
				
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->template = 'shipping/weight.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
		
	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/weight')) {
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