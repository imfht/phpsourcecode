<?php
class ControllerExtensionModuleLatest extends Controller {
	public function index($setting) {
		$this->load->model('catalog/product');

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProducts($filter_data);
		if (!$results) {
			return;
		}

		$this->load->language('extension/module/latest');

		$data['products'] = array();
		foreach ($results as $result) {
			$data['products'][] = $this->model_catalog_product->handleSingleProduct($result, $setting['width'], $setting['height']);
		}

		return $this->load->view('extension/module/latest', $data);
	}
}
