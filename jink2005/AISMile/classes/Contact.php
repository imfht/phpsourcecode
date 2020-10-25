<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class ContactCore extends ObjectModel
{
	public $id;

	/** @var string Name */
	public $name;

	/** @var string e-mail */
	public $email;

	/** @var string Detailed description */
	public $description;

	public $customer_service;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'contact',
		'primary' => 'id_contact',
		'multilang' => true,
		'fields' => array(
			'email' => 				array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
			'customer_service' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

			// Lang fields
			'name' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
			'description' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
		),
	);

	/**
	  * Return available contacts
	  *
	  * @param integer $id_lang Language ID
	  * @param Context
	  * @return array Contacts
	  */
	public static function getContacts($id_lang)
	{
		$shop_ids = Shop::getContextListShopID();
		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'contact` c
				'.Shop::addSqlAssociation('contact', 'c', false).'
				LEFT JOIN `'._DB_PREFIX_.'contact_lang` cl ON (c.`id_contact` = cl.`id_contact`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND contact_shop.`id_shop` IN ('.implode(', ', array_map('intval', $shop_ids)).')
				ORDER BY `name` ASC';

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	/**
	 * Return available categories contacts
	 * @return array Contacts
	 */
	public static function getCategoriesContacts()
	{
		return Db::getInstance()->executeS('
			SELECT cl.*
			FROM '._DB_PREFIX_.'contact ct
			LEFT JOIN '._DB_PREFIX_.'contact_lang cl
				ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.(int)Context::getContext()->language->id.')
			WHERE ct.customer_service = 1
		');
	}
}

