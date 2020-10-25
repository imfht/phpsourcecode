<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class reinsuranceClass extends ObjectModel
{
	/** @var integer reinsurance id*/
	public $id;
	
	/** @var integer reinsurance id shop*/
	public $id_shop;
	
	/** @var string reinsurance file name icon*/
	public $file_name;

	/** @var string reinsurance text*/
	public $text;


	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'reinsurance',
		'primary' => 'id_reinsurance',
		'multilang' => true,
		'fields' => array(
			'id_shop' =>				array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
			'file_name' =>				array('type' => self::TYPE_STRING, 'validate' => 'isFileName'),
			// Lang fields
			'text' =>					array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
		)
	);

	public function copyFromPost()
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		/* Multilingual fields */
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}
}
