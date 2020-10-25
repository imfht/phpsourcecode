<?php
class ControllerExtensionModuleSpecial extends Controller {
	public function index($setting) {
		$this->load->model('catalog/product');

		$filter_data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProductSpecials($filter_data);
		if (!$results) {
			return;
		}

		$this->load->language('extension/module/special');
		$data['products'] = array();
		foreach ($results as $result) {
			$data['products'][] = $this->model_catalog_product->handleSingleProduct($result, $setting['width'], $setting['height']);
		}

		return $this->load->view('extension/module/special', $data);
	}
}
