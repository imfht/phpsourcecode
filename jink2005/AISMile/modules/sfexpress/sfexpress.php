<?php
if ( !defined( '_MB_VERSION_' ) )
  exit;
class Sfexpress extends CarrierModule
{
	public function __construct()
	{
		$this->name = 'sfexpress';
		$this->tab = 'shipping_logistics';
		$this->author = 'MileBiz';
		$this->version = 1.0;
		$this->limited_countries = array('cn');
		
		parent::__construct(); // The parent construct is required for translations
	
		$this->displayName = $this->l('Sf Express');
		$this->description = $this->l('Domestic Sf Express shipping');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
		$this->_primaryKey = array('country','city');
		$this->_fieldsList = array(
			'country' => 
				array(
					'name' =>$this->l('Please select state'),
					'validate'=>'isInt',
					'type'=>'select',
					'dbtype'=>'int(3)',
					'range' => parent::getStateArray(),
					'value'=>''
				),
			'city' => 
				array(
					'name' =>$this->l('Please select city'),
					'validate'=>'isInt',
					'type'=>'select',
					'dbtype'=>'int(3)',
					'range' => array(),
					'value'=>''
				),
			'price' => 
				array(
					'name' =>$this->l('Please enter costs'),
					'validate'=>'isFloat',
					'type'=>'text',
					'dbtype'=>'float',
					'value'=>''
				),
			'xzprice' => 
				array(
					'name' =>$this->l('Please enter 1000g zx costs'),
					'validate'=>'isFloat',
					'type'=>'text',
					'dbtype'=>'float',
					'value'=>''
				),
			'disinfomation' => 
				array(
					'name' =>$this->l('Display Infomation'),
					'validate'=>'isString',
					'type'=>'text',
					'dbtype'=>'varchar(255)',
					'value' => $this->displayName
				)
		);
		if (self::isInstalled($this->name))
		{			// Getting carrier list
			global $cookie;
			$carriers = Carrier::getCarriers($cookie->id_lang, true, false, false, NULL, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);

			// Saving id carrier list
			$id_carrier_list = array();
			foreach($carriers as $carrier)
				$id_carrier_list[] .= $carrier['id_carrier'];

			// Testing if Carrier Id exists
			$warning = array();
			if (!in_array((int)(Configuration::get($this->name.'_CARRIER_ID')), $id_carrier_list))
				$warning[] .= $this->l('"carrire id"').' ';
			if (count($warning))
				$this->warning .= implode(' , ',$warning).$this->l('must be configured to use this module correctly').' ';
		}
	}

	public function install()
	{
		$carrierConfig = array(
			0 => array('name' => $this->displayName,
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' => array(Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $this->description),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->name,
				'need_range' => true
			)
		);
		$id_carrier1 = parent::installExternalCarrier($carrierConfig[0]);
		Configuration::updateValue($this->name.'_CARRIER_ID', (int)$id_carrier1);
		if (!parent::install() || !$this->registerHook('updateCarrier'))
			return false;
		
		if(!parent::createTable())
		{
			return false;
		}

		return true;
	}

	public function uninstall()
	{
		// Delete External Carrier
		$Carrier1 = new Carrier((int)(Configuration::get($this->name.'_CARRIER_ID')));

		// If external carrier is default set other one as default
		if (Configuration::get('PS_CARRIER_DEFAULT') == (int)($Carrier1->id))
		{
			global $cookie;
			$carriersD = Carrier::getCarriers($cookie->id_lang, true, false, false, NULL, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
			foreach($carriersD as $carrierD)
				if ($carrierD['active'] AND !$carrierD['deleted'] AND ($carrierD['name'] != $this->_config['name']))
					Configuration::updateValue('PS_CARRIER_DEFAULT', $carrierD['id_carrier']);
		}

		// Then delete Carrier
		$Carrier1->deleted = 1;
		if (!$Carrier1->update())
			return false;
			
		// Uninstall
	 	if (!parent::uninstall() || !Configuration::deleteByName($this->name.'_CARRIER_ID') || !$this->unregisterHook('updateCarrier') || !parent::dropTable())
	 		return false;

		return true;
	}


	public function getContent()
	{
		$_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST) || !empty($_GET))
		{
			$_html .= parent::postValidation();
		}
		else
			$_html .= '<br />';

		$_html .= parent::displayForm();
		$_html .= parent::displayConfigList();
		
		return $_html;
	}


	public function hookupdateCarrier($params)
	{
		if ((int)($params['id_carrier']) == (int)(Configuration::get($this->name.'_CARRIER_ID')))
			Configuration::updateValue($this->name.'_CARRIER_ID', (int)($params['carrier']->id));
	}


	/*
	** Front Methods
	**
	** If you set need_range at true when you created your carrier (in install method), the method called by the cart will be getOrderShippingCost
	** If not, the method called will be getOrderShippingCostExternal
	**
	** $params var contains the cart, the customer, the address
	** $shipping_cost var contains the price calculated by the range in carrier tab
	**
	*/
	
	public function getOrderShippingCost($params, $shipping_cost)
	{
		// Init var
		$address = new Address($params->id_address_delivery);
		if (!Validate::isLoadedObject($address))
		{
			return false;
		}

		$state_id = $address->id_state;

		$sqlcountry="select * from `"._DB_PREFIX_.$this->name."` where country='".$state_id."' AND (city = '0' OR city = '".$address->id_city."') limit 1";

		$result = Db::getInstance()->ExecuteS($sqlcountry);
		if($result){
			if($params->getTotalWeight()<=1){
				return (float)$result[0]['price'];
			}
			else{
				return (float)($result[0]['price'] + $result[0]['xzprice']*(round($params->getTotalWeight()+0.5-1)));
			}
		}
		return false;
	}
	
	public function getOrderShippingCostExternal($params)
	{
		// This example returns the overcost directly, but you can call a webservice or calculate what you want before returning the final value to the Cart
		return getOrderShippingCost($params,0);
	}
	
	public function isAvailable($address){
		$state_id = $address->id_state;

		$sqlcountry="select * from `"._DB_PREFIX_.$this->name."` where country='".$state_id."'";

		$result = Db::getInstance()->ExecuteS($sqlcountry);
		if($result){
		    foreach($result as $item)
		        if($item['city'] == 0 || $item['city'] == $address->id_city)
		            return true;
		}
		return false;
	}
}

?>
