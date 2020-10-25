<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class ZoneCore extends ObjectModel
{
 	/** @var string Name */
	public $name;

	/** @var boolean Zone status */
	public $active = true;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'zone',
		'primary' => 'id_zone',
		'fields' => array(
			'name' => 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		),
	);

	protected $webserviceParameters = array();

	/**
	 * Get all available geographical zones
	 *
	 * @param bool $active
	 * @return array Zones
	 */
	public static function getZones($active = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'zone`
			'.($active ? 'WHERE active = 1' : '').'
			ORDER BY `name` ASC
		');
	}

	/**
	 * Get a zone ID from its default language name
	 *
	 * @param string $name
	 * @return integer id_zone
	 */
	public static function getIdByName($name)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_zone`
			FROM `'._DB_PREFIX_.'zone`
			WHERE `name` = \''.pSQL($name).'\'
		');
	}

	/**
	* Delete a zone
	*
	* @return boolean Deletion result
	*/
	public function delete()
	{
		if (parent::delete())
		{
			// Delete regarding delivery preferences
			$result = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'carrier_zone WHERE id_zone = '.(int)$this->id);
			$result &= Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'delivery WHERE id_zone = '.(int)$this->id);

			// Update Country & state zone with 0
			$result &= Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'country SET id_zone = 0 WHERE id_zone = '.(int)$this->id);
			$result &= Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'state SET id_zone = 0 WHERE id_zone = '.(int)$this->id);

			return $result;
		}

		return false;
	}
}