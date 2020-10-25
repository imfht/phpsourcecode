<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * @since 1.5.0
 */
class GenderCore extends ObjectModel
{
	public $id_gender;
	public $name;
	public $type;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'gender',
		'primary' => 'id_gender',
		'multilang' => true,
		'fields' => array(
			'type' => array('type' => self::TYPE_INT, 'required' => true),

			// Lang fields
			'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 20),
		),
	);

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id, $id_lang, $id_shop);

		$this->image_dir = _PS_GENDERS_DIR_;
	}

	public static function getGenders($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = Context::getContext()->language->id;

		$genders = new Collection('Gender', $id_lang);
		return $genders;
	}

	public function getImage($use_unknown = false)
	{
		if (!file_exists(_PS_GENDERS_DIR_.$this->id.'.jpg'))
			return ($use_unknown) ?  _PS_ADMIN_IMG_.'unknown.gif' : false;
		return _THEME_GENDERS_DIR_.$this->id.'.jpg';
	}
}