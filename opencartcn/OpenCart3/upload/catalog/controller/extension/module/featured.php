<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		if (empty($setting['product'])) {
			return;
		}

		$this->load->language('extension/module/featured');
		$this->load->model('catalog/product');

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		$data['products'] = array();
		foreach ($setting['product'] as $product_id) {
			$result = $this->model_catalog_product->getProduct($product_id);
			if (!$result) {
				continue;
			}

			$data['products'][] = $this->model_catalog_product->handleSingleProduct($result, $setting['width'], $setting['height']);

			if (count($data['products']) >= (int)$setting['limit']) {
				break;
			}
		}

		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}
}
