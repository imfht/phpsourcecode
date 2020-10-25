<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AddressesControllerCore extends FrontController
{
	public $auth = true;
	public $php_self = 'addresses';
	public $authRedirection = 'addresses';
	public $ssl = true;

	/**
	 * Set default medias for this controller
	 */
	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'addresses.css');
		$this->addJS(_THEME_JS_DIR_.'tools.js');
	}

	/**
	 * Initialize addresses controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		if (!Validate::isLoadedObject($this->context->customer))
			die(Tools::displayError('Customer not found'));
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$total = 0;
		$multiple_addresses_formated = array();
		$ordered_fields = array();
		$addresses = $this->context->customer->getAddresses($this->context->language->id);
		// @todo getAddresses() should send back objects
		foreach ($addresses as $detail)
		{
			$address = new Address($detail['id_address']);
			$multiple_addresses_formated[$total] = AddressFormat::getFormattedLayoutData($address);
			unset($address);
			++$total;

			// Retro theme < 1.4.2
			$ordered_fields = AddressFormat::getOrderedAddressFields($detail['id_country'], false, true);
		}

		// Retro theme 1.4.2
		if ($key = array_search('Country:name', $ordered_fields))
			$ordered_fields[$key] = 'country';

		$addresses_style = array(
			'company' => 'address_company',
			'vat_number' => 'address_company',
			'firstname' => 'address_name',
			'lastname' => 'address_name',
			'address1' => 'address_address1',
			'address2' => 'address_address2',
			'city' => 'address_city',
			'country' => 'address_country',
			'phone' => 'address_phone',
			'phone_mobile' => 'address_phone_mobile',
			'alias' => 'address_title',
		);

		$this->context->smarty->assign(array(
			'addresses_style' => $addresses_style,
			'multipleAddresses' => $multiple_addresses_formated,
			'ordered_fields' => $ordered_fields,
			'addresses' => $addresses, // Retro Compatibility Theme < 1.4.1
		));

		$this->setTemplate(_PS_THEME_DIR_.'addresses.tpl');
	}
}

