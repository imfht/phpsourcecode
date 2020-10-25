<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class TranslatedConfigurationCore extends Configuration
{
	protected $webserviceParameters = array(
		'objectNodeName' => 'translated_configuration',
		'objectsNodeName' => 'translated_configurations',
		'fields' => array(
			'value' => array(),
			'date_add' => array(),
			'date_upd' => array(),
		),
	);

	public static $definition = array(
		'table' => 'configuration',
		'primary' => 'id_configuration',
		'multilang' => true,
		'fields' => array(
			'name' => 			array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 32),
			'id_shop_group' => 	array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
			'id_shop' => 		array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
			'value' => 			array('type' => self::TYPE_STRING, 'lang' => true),
			'date_add' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	public function __construct($id = NULL, $id_lang = NULL)
	{
		$this->def = ObjectModel::getDefinition($this);
		// Check if the id configuration is set in the configuration_lang table.
		// Otherwise configuration is not set as translated configuration.
		if ($id !== null)
		{
			$id_translated = Db::getInstance()->executeS('
				SELECT `'.$this->def['primary'].'`
				FROM `'.pSQL(_DB_PREFIX_.$this->def['table']).'_lang`
				WHERE `'.$this->def['primary'].'`='.pSQL($id).' LIMIT 0,1
			');

			if (empty($id_translated))
				$id = null;
		}
		parent::__construct($id,$id_lang);
	}
	
	public function add($autodate = true, $nullValues = false)
	{
		return $this->update($nullValues);
	}
	
	public function update($nullValues = false)
	{
		$ishtml = false;
		foreach ($this->value as $i18n_value)
		{
			if (Validate::isCleanHtml($i18n_value))
			{
				$ishtml = true;
				break;
			}
		}
		Configuration::updateValue($this->name, $this->value, $ishtml);
		
		$last_insert = Db::getInstance()->getRow('
			SELECT `id_configuration` AS id
			FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` = \''.pSQL($this->name).'\'');
		if ($last_insert)
			$this->id = $last_insert['id'];
		
		return true;
	}
	
	public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$query = '
		SELECT DISTINCT main.`'.$this->def['primary'].'` FROM `'._DB_PREFIX_.$this->def['table'].'` main
		'.$sql_join.'
		WHERE id_configuration IN 
		(	SELECT id_configuration
			FROM '._DB_PREFIX_.$this->def['table'].'_lang
		) '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '').'
		';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}
}