<?php

/**
 * Checkout.php
 *
 * @copyright  2017 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Sam Chen <samchen@opencart.cn>
 * @created    2017-07-31 15:37
 * @modified         2017-08-14 19:09:49
 */

class ModelCheckoutCheckout extends Model {
	private $logger;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$this->load->model('checkout/order');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/city');
		$this->load->model('account/order');

		$this->logger = new Log("checkout.log");
	}

	public function createOrder() {
		$this->log(__FUNCTION__);

		// Default order data
		$order_data = array(
			'invoice_prefix'            => $this->config->get('config_invoice_prefix'),
			'store_id'                  => $this->config->get('config_store_id'),
			'store_name'                => $this->config->get('config_name'),
			'store_url'                 => $this->config->get('config_store_id') ? $this->config->get('config_url') : HTTP_SERVER,
			'customer_id'               => $this->customer->isLogged() ? $this->customer->getId() : 0,
			'customer_group_id'         => $this->customer->isLogged() ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id'),
			'firstname'                 => $this->customer->isLogged() ? $this->customer->getFirstName() : '',
            'lastname'                  => $this->customer->isLogged() ? $this->customer->getLastName() : '',
			'email'                     => $this->customer->isLogged() ? $this->customer->getEmail() : '',
			'calling_code'              => $this->customer->isLogged() ? $this->customer->getCallingCode() : '',
			'telephone'                 => $this->customer->isLogged() ? $this->customer->getTelephone() : '',
			'fax'                       => '',
			'custom_field'              => array(),

			// Payment address
			'payment_firstname'         => '',
            'payment_lastname'         => '',
			'payment_calling_code'      => '',
			'payment_telephone'         => '',
			'payment_company'           => '',
			'payment_address_1'         => '',
			'payment_address_2'         => '',
			'payment_city'              => '',
			'payment_postcode'          => '',
			'payment_country'           => '',
			'payment_country_id'        => '',
			'payment_tax_id'            => '',
			'payment_zone'              => '',
			'payment_zone_id'           => '',
			'payment_city'              => '',
			'payment_city_id'           => '',
			'payment_county'            => '',
			'payment_county_id'         => '',
			'payment_address_format'    => '',
			'payment_custom_field'      => array(),
			'payment_method'            => '',
			'payment_code'              => '',

			// Shipping address
			'shipping_firstname'         => '',
            'shipping_lastname'         => '',
			'shipping_telephone'        => '',
			'shipping_calling_code'     => '',
			'shipping_company'          => '',
			'shipping_address_1'        => '',
			'shipping_address_2'        => '',
			'shipping_city'             => '',
			'shipping_postcode'         => '',
			'shipping_country'          => '',
			'shipping_country_id'       => '',
			'shipping_zone'             => '',
			'shipping_zone_id'          => '',
			'shipping_city'             => '',
			'shipping_city_id'          => '',
			'shipping_county'           => '',
			'shipping_county_id'        => '',
			'shipping_address_format'   => '',
			'shipping_custom_field'     => array(),
			'shipping_method'           => '',
			'shipping_code'             => '',

			'comment'                   => '',
			'total'                     => '',
			'order_status_id'           => 0,
			'affiliate_id'              => '',
			'commission'                => '',
			'marketing_id'              => '',
			'tracking'                  => '',
			'language_id'								=> $this->config->get('config_language_id'),
			'currency_id'               => $this->currency->getId($this->session->data['currency']),
			'currency_code'             => $this->session->data['currency'],
			'currency_value'            => $this->currency->getValue($this->session->data['currency']),
			'ip'                        => $this->request->server['REMOTE_ADDR'],
			'forwarded_ip'              => array_get($this->request->server, 'HTTP_X_FORWARDED_FOR'),
			'user_agent'                => array_get($this->request->server, 'HTTP_USER_AGENT'),
			'accept_language'           => array_get($this->request->server, 'HTTP_ACCEPT_LANGUAGE')
		);

		// Update order data
		if (!$this->customer->isLogged()) {
			$order_data['firstname'] = array_get($this->session->data, 'guest.firstname', '');
            $order_data['lastname'] = array_get($this->session->data, 'guest.lastname', '');
			$order_data['email'] = array_get($this->session->data, 'guest.email', '');
			$order_data['calling_code'] = array_get($this->session->data, 'guest.calling_code', '');
			$order_data['telephone'] = array_get($this->session->data, 'guest.telephone', '');
			$order_data['fax'] = array_get($this->session->data, 'guest.fax', '');
		}

		if (array_get($this->session->data, 'payment_method.code')) {
			$order_data['payment_code'] = $this->session->data['payment_method']['code'];
			$order_data['payment_method'] = $this->session->data['payment_method']['title'];
		}

		if (array_get($this->session->data, 'shipping_method.code')) {
			if ($this->cart->hasshipping()) {
				$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
				$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
			} else {
				unset($this->session->data['shipping_address']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);
			}
		}

		$order_data['comment'] = array_get($this->session->data, 'comment', '');

		// Map session addresses to order data
		// Example. $this->session->data['payment_address']['firstname'] -> $this->session->data['payment_firstname']
		foreach (['payment', 'shipping'] as $type) {
			if (!empty($this->session->data[$type . '_address'])) {
				foreach ($this->session->data[$type . '_address'] as $key => $value) {
					$order_data["{$type}_{$key}"] = $value;
				}
			}

			// Format custom_field
			if (empty($order_data["{$type}_custom_field"])) {
				unset($order_data["{$type}_custom_field"]);
			}

			// Get country name
			if (!empty($this->session->data[$type . '_address']['country_id'])) {
				$country_info = $this->model_localisation_country->getCountry($this->session->data[$type . '_address']['country_id']);
				if ($country_info) {
					$order_data["{$type}_country"] = $country_info['name'];
					$order_data["{$type}_address_format"] = $country_info['address_format'];
				}
			}

			// Get zone name
			if (!empty($this->session->data[$type . '_address']['zone_id'])) {
				$zone_info = $this->model_localisation_zone->getZone($this->session->data[$type . '_address']['zone_id']);
				if ($zone_info) {
					$order_data["{$type}_zone"] = $zone_info['name'];
				}
			}

			// Get city name
			if (!empty($this->session->data[$type . '_address']['city_id'])) {
				$city_info = $this->model_localisation_city->getCity($this->session->data[$type . '_address']['city_id']);
				if ($city_info) {
					$order_data["{$type}_city"] = $city_info['name'];
				}
			}

			// Get county name
			if (!empty($this->session->data[$type . '_address']['county_id'])) {
				$county_info = $this->model_localisation_city->getCity($this->session->data[$type . '_address']['county_id']);
				if ($county_info) {
					$order_data["{$type}_county"] = $county_info['name'];
				}
			}
		}

		$order_data['products'] = $this->getProducts();
		$order_data['vouchers'] = $this->getVouchers();
		$order_data['totals'] = $this->getTotals();
		$order_data['total'] = $this->getTotal();

		$this->log($order_data);
		// Create new order
		$order_id = $this->model_checkout_order->addOrder($order_data);
		$this->session->data['order_id'] = $order_id;
		return $order_id;
	}

	// Private
	private function getProducts() {
		$products = array();

		foreach ($this->cart->getProducts() as $product) {
			$option_data = array();

			foreach ($product['option'] as $option) {
				$option_data[] = array(
					'product_option_id'       => $option['product_option_id'],
					'product_option_value_id' => $option['product_option_value_id'],
					'option_id'               => $option['option_id'],
					'option_value_id'         => $option['option_value_id'],
					'name'                    => $option['name'],
					'value'                   => $option['value'],
					'type'                    => $option['type']
				);
			}

			$products[] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'download'   => $product['download'],
				'quantity'   => $product['quantity'],
				'subtract'   => $product['subtract'],
				'price'      => $product['price'],
				'total'      => $product['total'],
				'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
				'reward'     => $product['reward']
			);
		}

		return $products;
	}

	private function getVouchers() {
		// Gift Voucher
		$vouchers = array();

		if (empty($this->session->data['vouchers'])) {
			return $vouchers;
		}

		foreach ($this->session->data['vouchers'] as $voucher) {
			$vouchers[] = array(
				'description'      => $voucher['description'],
				'code'             => token(10),
				'to_name'          => $voucher['to_name'],
				'to_email'         => $voucher['to_email'],
				'from_name'        => $voucher['from_name'],
				'from_email'       => $voucher['from_email'],
				'voucher_theme_id' => $voucher['voucher_theme_id'],
				'message'          => $voucher['message'],
				'amount'           => $voucher['amount']
			);
		}

		return $vouchers;
	}

	// Public
	public function getDefaultCountryId() {
		return $this->config->get('config_country_id');
	}

	public function getDefaultZoneId() {
		return $this->config->get('config_zone_id');
	}

	/*
	 * $code = 'pp_express'
	 */
	public function setPaymentMethod($code = '') {
		$methods = $this->getPaymentMethods();

		if (!$methods) {
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_method']);
			return false;
		}

		// Use first method in payment methods session when code is empty
		if (empty($code)) {
			$this->session->data['payment_method'] = reset($methods);
			return true;
		}

		// Check if selected payment code still available
		if (!isset($methods[$code])) {
			unset($this->session->data['payment_method']);
			return false;
		} else {
			$this->session->data['payment_method'] = $methods[$code];
			return true;
		}
	}

	public function getPaymentMethods() {
		$payment_methods = array();

		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensions('payment');
		$total = $this->getTotal();

		foreach ($results as $result) {
			if (!$this->config->get('payment_' . $result['code'] . '_status')) {
				continue;
			}

			$this->load->model('extension/payment/' . $result['code']);
			$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

			if (!$method) {
				continue;
			}

			$payment_methods[$result['code']] = $method;
		}

		$sort_order = array();
		foreach ($payment_methods as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $payment_methods);
		$this->session->data['payment_methods'] = $payment_methods;

		return $payment_methods;
	}

	/*
	 * $code = 'flat.flat'
	 */
	public function setShippingMethod($code = '') {
		$methods = $this->getShippingMethods();

		if (!$methods) {
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			return false;
		}

		// Use first method in payment methods session when code is empty
		if (empty($code)) {
			$first = reset($methods);
			$this->session->data['shipping_method'] = current($first['quote']);
			return true;
		}

		// Check if selected shipping code still available
		$shipping = explode('.', $code);
		if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($methods[$shipping[0]]['quote'][$shipping[1]])) {
			unset($this->session->data['shipping_method']);
			return false;
		} else {
			$this->session->data['shipping_method'] = $methods[$shipping[0]]['quote'][$shipping[1]];
			return true;
		}
	}

	public function getShippingMethods() {
		$method_data = array();

		if (!$this->cart->hasShipping() && !isset($this->session->data['shipping_address'])) {
			return $method_data;
		}

		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensions('shipping');
		foreach ($results as $result) {
			if (!$this->config->get('shipping_' . $result['code'] . '_status')) {
				continue;
			}

			$this->load->model('extension/shipping/' . $result['code']);
			$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);
			if ($quote) {
				$method_data[$result['code']] = array(
					'title' => $quote['title'],
					'quote' => $quote['quote'],
					'sort_order' => $quote['sort_order'],
					'error' => $quote['error']
				);
			}
		}

		$sort_order = array();
		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $method_data);

		$this->session->data['shipping_methods'] = $method_data;
		return $method_data;
	}

	public function getTotal() {
		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);

		$this->load->model('setting/extension');
		$sort_order = array();
		$results = $this->model_setting_extension->getExtensions('total');
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);
		foreach ($results as $result) {
			if (!$this->config->get('total_' . $result['code'] . '_status')) {
				continue;
			}

			$this->load->model('extension/total/' . $result['code']);
			// We have to put the totals in an array so that they pass by reference.
			$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
		}

		return $total;
	}

	public function getTotals() {
		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);

		$this->load->model('setting/extension');
		$sort_order = array();
		$results = $this->model_setting_extension->getExtensions('total');
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);
		foreach ($results as $result) {
			if ($this->config->get('total_' . $result['code'] . '_status')) {
				$this->load->model('extension/total/' . $result['code']);
				// We have to put the totals in an array so that they pass by reference.
				$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			}
		}

		$sort_order = array();
		foreach ($totals as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $totals);

		return $totals;
	}

	public function bindOrderCustomerId($order_id, $customer_id, $customer_group_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET customer_id = '" . (int)$customer_id . "', customer_group_id = '" . (int)$customer_group_id . "' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function syncCustomerIdInCartTableAfterRegistered() {
		$this->db->query("UPDATE " . DB_PREFIX . "cart SET customer_id = '" . (int)$this->customer->getId() . "' WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function log($data = null) {
		if (!$data) {
			return;
		}

		if (!defined('DEBUG') || DEBUG == false) {
			return;
		}

		if (is_array($data)) {
			$this->logger->write(json_encode($data));
		} else {
			$this->logger->write($data);
		}
	}
}
