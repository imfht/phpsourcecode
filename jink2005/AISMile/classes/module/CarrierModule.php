<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

abstract class CarrierModuleCore extends Module
{
	public $_fieldsList = array();
	public $_primaryKey = "";
	public $_isSupprotCOD = true;
	abstract function getOrderShippingCost($params,$shipping_cost);	
	abstract function getOrderShippingCostExternal($params);
	public function isAvailable($address){
		return true;
	}
	public function isSupportCOD(){
	    return $this->_isSupprotCOD;
	}
	public function createTable(){
	    $_createSql = 'CREATE TABLE `'._DB_PREFIX_.$this->name.'` (';
	    foreach($this->_fieldsList as $key=> $value)
	        $_createSql .= '`'.$key.'` '.$value['dbtype'].' NOT NULL,';
	    $_createSql .= 'PRIMARY KEY  (';
        if(is_array($this->_primaryKey)){
    	    $first = true;
    	    foreach($this->_primaryKey as $_primaryKey){
    	        if($first){
    	            $_createSql .= '`'.$_primaryKey.'`';
    	            $first = false;
    	        }else{
    	            $_createSql .= ',`'.$_primaryKey.'`';
    	        }
    	    }
        }else{
	        $_createSql .= '`'.$this->_primaryKey.'`';
	    }
		$_createSql .= ')) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	    return Db::getInstance()->Execute($_createSql);
	}
	public function dropTable(){
	    return Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.$this->name);
	}
	public function getStateArray(){
	    $state = array();
		$countryId = Country::getByIso('cn');
		$resultstate = State::getStatesByIdCountry($countryId);
		foreach($resultstate as $v=>$k){
		    $state[$resultstate[$v]["id_state"]] = $resultstate[$v]["name"];
	    }
		return $state;
	}
	public function displayForm()
	{
	    $_html = '';
		$_html .=
		'<form action="'.$this->_getOriUrl().'" method="post">
			<fieldset>
				<legend><img src="../img/admin/contact.gif" />'.$this->commonL('Configuration information').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->commonL('Please enter the correct configuration information, if we do not know how to set up, please refer to examples of Web site').'.<br /><br /></td></tr>';
		foreach($this->_fieldsList as $field => $detail){
			$_html .= '
			<tr>
				<td width="130" style="height: 35px;">'.$detail['name'].'</td>
				<td>';
				$value = Tools::getValue($field,isset($detail['value'])?$detail['value']:'');
				if($detail['type'] == 'text'){
					$_html .= '
					<input type="text" name="'.$field.'" value="'.$value.'" size="40" />
					';
				}
				else if($detail['type'] == 'textarea'){
					$_html .= '
					<textarea name="'.$field.'" cols="80" rows="5">'.$value.'</textarea>
					';
				}
				else if($detail['type'] == 'select'){
					$_html .= '<div id = "container_'.$field.'"><select id="'.$field.'" name="'.$field.'">';
					foreach($detail['range'] as $key => $option){
						if($key == $value){
							$_html .= '<option value="'.$key.'" selected="selected">'.$option.'</option>';
						}else{
							$_html .= '<option value="'.$key.'">'.$option.'</option>';
						}
      				}
      				if(count($detail['range']) == 0 && isset($value))
      				    $_html .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
      				$_html .= '</select></div>';
				}else{
					$_html .= '
						type error!
					';
				}
			$_html .= '
				</td>
			</tr>
			';
		}
		$_html .= '
					<script type="text/javascript">
					$(document).ready(function(){
							ajaxCities ();
        					$(\'#country\').change(function() {
        						ajaxCities ();
        					});
        					function ajaxCities ()
        					{
        						$.ajax({
        						  url: "ajax.php",
        						  cache: false,
        						  data: "ajaxCities=1&id_state="+$(\'#country\').val()+"&id_city="+$(\'#city\').val(),
        						  success: function(html)
        						  {
        						  	if (html == \'false\')
        							{
        						  		$(\'#container_city\').fadeOut();
        								$(\'#id_city option[value=0]\').attr(\'selected\', \'selected\');
        							}
        						  	else
        						  	{
        						  		$(\'#city\').html(html);
        						  		$(\'#container_city\').fadeIn();
        						  	}
        						  }
        						});
        					}; }); </script>';
		$_html .= '
					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->commonL('Update settings').'" type="submit" /></td></tr>
				</table>
		  </fieldset>
		</form>';
		return $_html;
	}
	
	public function displayConfigList()
	{
        $_html = "";
	 	$links = $this->_getLinks();
	 	global $currentIndex, $cookie, $adminObj;
		
	 	$_html .= '
	 	<h3 class="blue space">'.$this->commonL('Infomation list').'</h3>
		<table class="table" width=100%>
			<tr>';
			foreach($this->_fieldsList as $field => $detail)
				$_html .= '<th>'.$detail['name'].'</th>';
			$_html .= '<th>'.$this->commonL('Actions').'</th></tr>';
			
		if (!$links)
			$_html .= '
			<tr>
				<td colspan="3">'.Tools::displayError('No record!').'</td>
			</tr>';
		else
			foreach ($links AS $link)
			{
			    $oriLink = $link;
			    if(isset($link['country'])){
			        $link['country'] = $this->_getStateNameById($link['country']);
			    }
			    if(isset($link['city'])){
			        $link['city'] != 0 ? $link['city'] = City::getNameById($link['city']) : $link['city'] = '----';
			    }

				$updateParam = '';
				$_html .= '
				<tr>';
				foreach($link as $key=> $linkvalue){
				    $_html .= '	<td>'.$linkvalue.'</td>';
				    $updateParam .= '&'.$key.'='.$oriLink[$key];
				}
				$_html .= '<td>
						<a href="'.$this->_getOriUrl().'&btnModify=1'.$updateParam.'"><img src="../img/admin/edit.gif" alt="" title="" style="cursor: pointer" /></a>
						<a href="'.$this->_getOriUrl().'&btnDelete=1&'.$this->_getUrlParam($oriLink).'"><img src="../img/admin/delete.gif" alt="" title="" style="cursor: pointer" /></a>
					</td>
				</tr>';
			}	
		$_html .= '
		</table>
		';
		return $_html;
	}
	
	public static function installExternalCarrier($config)
	{
		$carrier = new Carrier();
		$carrier->name = $config['name'];
		$carrier->id_tax_rules_group = $config['id_tax_rules_group'];
		$carrier->id_zone = $config['id_zone'];
		$carrier->active = $config['active'];
		$carrier->deleted = $config['deleted'];
		$carrier->delay = $config['delay'];
		$carrier->shipping_handling = $config['shipping_handling'];
		$carrier->range_behavior = $config['range_behavior'];
		$carrier->is_module = $config['is_module'];
		$carrier->shipping_external = $config['shipping_external'];
		$carrier->external_module_name = $config['external_module_name'];
		$carrier->need_range = $config['need_range'];

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')))
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
		}

		if ($carrier->add())
		{
			$groups = Group::getGroups(true);
			foreach ($groups as $group)
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', array('id_carrier' => (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');

			$rangePrice = new RangePrice();
			$rangePrice->id_carrier = $carrier->id;
			$rangePrice->delimiter1 = '0';
			$rangePrice->delimiter2 = '10000';
			$rangePrice->add();

			$rangeWeight = new RangeWeight();
			$rangeWeight->id_carrier = $carrier->id;
			$rangeWeight->delimiter1 = '0';
			$rangeWeight->delimiter2 = '10000';
			$rangeWeight->add();

			$zones = Zone::getZones(true);
			foreach ($zones as $zone)
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_zone', array('id_carrier' => (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => NULL, 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => NULL, 'id_range_weight' => (int)($rangeWeight->id), 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
			}

			// Copy Logo
			if(file_exists(dirname(__FILE__).'/logo.jpg'))
			    if (!copy(dirname(__FILE__).'/logo.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
				    return false;

			// Return ID Carrier
			return (int)($carrier->id);
		}

		return false;
	}

	public function postProcess()
	{
	    $_html = "";
     	if (Tools::getValue('btnSubmit'))
     	{
			if (!$this->updaterecord())
				$_html .= $this->displayError(Tools::displayError('An error occured during record updating'));
			else
				$_html .= $this->displayConfirmation($this->commonL('The record has been updated successfully'));
     	}
     	elseif (Tools::getValue('btnDelete'))
		{
			if (!$this->deleteLink())
			    $_html .= $this->displayError(Tools::displayError('An error occurred during record deletion'));
			else
				$_html .= $this->displayConfirmation($this->commonL('The record has been deleted successfully'));
		}
		return $_html;
	}
	public function postValidation()
	{
	    if (Tools::getValue('btnSubmit') || Tools::getValue('btnModify')){
    		$validate = new Validate();
    		foreach($this->_fieldsList as $field => $detail)
    		{
    			$method = $detail['validate'];
    			if (!method_exists($validate, $method))
    				die (Tools::displayError('Validation function not found.').' '.$method);
    			if(!call_user_func(array('Validate', $method), Tools::getValue($field))){
    				return '<div class="alert error">'. $detail['name'].Tools::displayError(' format Incorrect .').'</div>';
    			}
    		}
    		
    		foreach($this->_fieldsList as $field => $detail)
    		{
    			$this->_fieldsList[$field]['value'] = Tools::getValue($field);
    		}
	    }
	    return $this->postProcess();
	}
	private function _getStateNameById($stateId){	
	    $state = new State($stateId);
		return $state->name;
	}

	private	function addrecord()
	{
		$sqlcountry="select ".$this->_getKey()." from `"._DB_PREFIX_.$this->name."` where ".$this->_getWhere()."  limit 1";
		
		if(Db::getInstance()->ExecuteS($sqlcountry)) {return false;}
        $fileds = '';
        $values = '';
        foreach($this->_fieldsList as $field => $detail){
            if(!empty($values)){
                $fileds .= ',';
                $values .= ',';
            }
            $fileds .= '`'.$field.'`';
            $values .= '\''.Tools::getValue($field).'\'';
        }
		if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.$this->name.' ('.$fileds.') VALUES ('.$values.')'))
			return false;
		return true;

	}

	private function updaterecord()
	{
		$sqlcountry="select ".$this->_getKey()." from `"._DB_PREFIX_.$this->name."` where ".$this->_getWhere()." limit 1";
		$result = Db::getInstance()->ExecuteS($sqlcountry);

		if(!$result) {
		    return $this->addrecord();
		}
		$fileds = '';
		foreach($this->_fieldsList as $field => $detail){
            if(''!==$fileds){
                $fileds .= ',';
            }
            $fileds .= '`'.$field.'`=\''.pSQL(Tools::getValue($field)).'\'';
        }
	    
		if (!Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.$this->name.' SET '.$fileds.' WHERE '.$this->_getWhere())){
		    return false;
		}
		return true;

	}
	
	private function _getWhere()
	{
	    $where = '';
        if(is_array($this->_primaryKey)){
    	    $first = true;
    	    foreach($this->_primaryKey as $_primaryKey){
    	        if(!$first)
    	            $where .= ' AND ';
    	        $first = false;
    	        $where .= '`'.$_primaryKey.'` = "'.Tools::getValue($_primaryKey).'"';
    	    }
    	}else{
    	    $where .= '`'.$this->_primaryKey.'` = "'.Tools::getValue($this->_primaryKey).'"';
    	}
    	return $where;
	}
	
	private function _getKey()
	{
	    $key = '';
        if(is_array($this->_primaryKey)){
    	    $first = true;
    	    foreach($this->_primaryKey as $_primaryKey){
    	        if(!$first)
    	            $key .= ',';
    	        $first = false;
    	        $key .= '`'.$_primaryKey.'`';
    	    }
    	}else{
    	    $key = '`'.$this->_primaryKey.'`';
    	}
    	return $key;
	}
	
	private function _getUrlParam($param)
	{
	    $key = '';
        if(is_array($this->_primaryKey)){
    	    $first = true;
    	    foreach($this->_primaryKey as $_primaryKey){
    	        if(!$first)
    	            $key .= '&';
    	        $first = false;
    	        $key .= $_primaryKey .'='.$param[$_primaryKey].'';
    	    }
    	}else{
    	    $key = $this->_primaryKey.'='.$param[$this->_primaryKey].'';
    	}
    	return $key;
	}

	private function deleteLink()
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.$this->name.' WHERE '.$this->_getWhere());
		return true;
	}

	private function _getLinks()
	{
	 	$result = array();

	 	if (!$links = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.$this->name))
	 		return false;
	 	$i = 0;
		
	 	foreach ($links as $link)
	 	{
		 	foreach($this->_fieldsList as $field => $detail)
			    $result[$i][$field] = $link[$field];
			$i++;
		}
	 	return $result;
	}
	private function _getOriUrl()
	{
	    $url = 'index.php?';
	    $url .= 'tab='.Tools::getValue('tab');
	    $url .= '&configure='.Tools::getValue('configure');
	    $url .= '&id='.Tools::getValue('id');
	    $url .= '&iname='.Tools::getValue('iname');
	    $url .= '&token='.Tools::getValue('token');
	    return $url;
	}
}

