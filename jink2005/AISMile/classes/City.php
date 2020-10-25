<?php
/*
* 
* 2007-2011 Power by Ã×ÀÖÉÌ³Ç 
* 
*/

class CityCore extends ObjectModel
{
	/** @var integer State id which city belongs */
	public 		$id_state;

	/** @var string Name */
	public 		$name;

	/** @var boolean Status for delivery */
	public		$active = true;

 	protected 	$fieldsRequired = array('id_state', 'name');
 	protected 	$fieldsSize = array('name' => 32);
 	protected 	$fieldsValidate = array('id_state' => 'isUnsignedId', 'name' => 'isGenericName', 'active' => 'isBool');

	protected 	$table = 'city';
	protected 	$identifier = 'id_city';

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_state' => array('xlink_resource'=> 'countries'),
			'name' => 		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
			'active' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		),
	);

	public function getFields()
	{
		parent::validateFields();
		$fields['id_state'] = (int)($this->id_state);
		$fields['name'] = pSQL($this->name);
		$fields['active'] = (int)($this->active);
		return $fields;
	}

	public static function getCitys($id_lang = false, $active = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT `id_city`, `id_state`, `name`, `active`
		FROM `'._DB_PREFIX_.'city`
		'.($active ? 'WHERE active = 1' : '').'
		ORDER BY `name` ASC');
	}

	/**
	* Get a city name with its ID
	*
	* @param integer $id_city Country ID
	* @return string State name
	*/
	public static function getNameById($id_city)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `name`
		FROM `'._DB_PREFIX_.'city`
		WHERE `id_city` = '.(int)($id_city));

        return $result['name'];
    }

	/**
	* Get a city id with its name
	*
	* @param string $id_city Country ID
	* @return integer city id
	*/
	public static function getIdByName($city)
    {
	  	$result = Db::getInstance()->getRow('
		SELECT `id_city`
		FROM `'._DB_PREFIX_.'city`
		WHERE `name` LIKE \''.pSQL($city).'\'');

        return ((int)($result['id_city']));
    }

	/**
	* Delete a city only if is not in use
	*
	* @return boolean
	*/
	public function delete()
	{
		if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());

		if (!$this->isUsed())
		{
			/* Database deletion */
			$result = Db::getInstance()->Execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
			if (!$result)
				return false;

			/* Database deletion for multilingual fields related to the object */
			if (method_exists($this, 'getTranslationsFieldsChild'))
				Db::getInstance()->Execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'_lang` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
			return $result;
		}
		else
			return false;
	}

	/**
	 * Check if a city is used
	 *
	 * @return boolean
	 */
	public function isUsed()
	{
		return ($this->countUsed() > 0);
	}

	/**
	 * Returns the number of utilisation of a city
	 *
	 * @return integer count for this city
	 */
	public function countUsed()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(*) AS nb_used
		FROM `'._DB_PREFIX_.'address`
		WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
		return $row['nb_used'];
	}

    public static function getCitysByIdState($id_state)
    {
        if (empty($id_state))
            die(Tools::displayError());

        return Db::getInstance()->ExecuteS('
        SELECT *
        FROM `'._DB_PREFIX_.'city` s
        WHERE s.`id_state` = '.(int)$id_state
        );
    }
	
	public static function getIdState($id_city)
	{
		if (!Validate::isUnsignedId($id_city))
			die(Tools::displayError());

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_state`
		FROM `'._DB_PREFIX_.'city`
		WHERE `id_city` = '.(int)($id_city));
	}
}

