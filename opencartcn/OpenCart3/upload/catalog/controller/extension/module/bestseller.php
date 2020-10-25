<?php
class ControllerExtensionModuleBestSeller extends Controller {
	public function index($setting) {
		$this->load->model('catalog/product');

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
		if (!$results) {
			return;
		}

		$this->load->language('extension/module/bestseller');

		$data['products'] = array();
		foreach ($results as $result) {
			$data['products'][] = $this->model_catalog_product->handleSingleProduct($result, $setting['width'], $setting['height']);
		}

		return $this->load->view('extension/module/bestseller', $data);
	}
}
