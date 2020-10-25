<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class ProductCommentCriterion
{
	/**
	 * Add a Comment Criterion
	 *
	 * @return boolean succeed
	 */
	public static function add($id_lang, $name)
	{
		if (!Validate::isUnsignedId($id_lang) ||
			!Validate::isMessage($name))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'product_comment_criterion`
		(`id_lang`, `name`) VALUES(
		'.(int)($id_lang).',
		\''.pSQL($name).'\')'));
	}
	
	/**
	 * Link a Comment Criterion to a product
	 *
	 * @return boolean succeed
	 */
	public static function addToProduct($id_product_comment_criterion, $id_product)
	{
		if (!Validate::isUnsignedId($id_product_comment_criterion) ||
			!Validate::isUnsignedId($id_product))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'product_comment_criterion_product`
		(`id_product_comment_criterion`, `id_product`) VALUES(
		'.(int)($id_product_comment_criterion).',
		'.(int)($id_product).')'));
	}
	
	/**
	 * Add grade to a criterion
	 *
	 * @return boolean succeed
	 */
	public static function addGrade($id_product_comment, $id_product_comment_criterion, $grade)
	{
		if (!Validate::isUnsignedId($id_product_comment) ||
			!Validate::isUnsignedId($id_product_comment_criterion))
			die(Tools::displayError());
		if ($grade < 0)
			$grade = 0;
		else if ($grade > 10)
			$grade = 10;
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'product_comment_grade`
		(`id_product_comment`, `id_product_comment_criterion`, `grade`) VALUES(
		'.(int)($id_product_comment).',
		'.(int)($id_product_comment_criterion).',
		'.(int)($grade).')'));
	}
	
	/**
	 * Update criterion
	 *
	 * @return boolean succeed
	 */
	public static function update($id_product_comment_criterion, $id_lang, $name)
	{
		if (!Validate::isUnsignedId($id_product_comment_criterion) ||
			!Validate::isUnsignedId($id_lang) ||
			!Validate::isMessage($name))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'product_comment_criterion` SET
		`name` = \''.pSQL($name).'\'
		WHERE `id_product_comment_criterion` = '.(int)($id_product_comment_criterion).' AND
		`id_lang` = '.(int)($id_lang)));
	}
	
	/**
	 * Get criterion by Product
	 *
	 * @return array Criterion
	 */
	public static function getByProduct($id_product, $id_lang)
	{
		if (!Validate::isUnsignedId($id_product) ||
			!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		return (Db::getInstance()->executeS('
		SELECT pcc.`id_product_comment_criterion`, pcc.`name`
		FROM `'._DB_PREFIX_.'product_comment_criterion` pcc
		INNER JOIN `'._DB_PREFIX_.'product_comment_criterion_product` pccp ON pcc.`id_product_comment_criterion` = pccp.`id_product_comment_criterion`
		WHERE pccp.`id_product` = '.(int)($id_product).' AND 
		pcc.`id_lang` = '.(int)($id_lang)));
	}
	
	/**
	 * Get Criterions
	 *
	 * @return array Criterions
	 */
	public static function get($id_lang)
	{
		if (!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		return (Db::getInstance()->executeS('
		SELECT pcc.`id_product_comment_criterion`, pcc.`name`
		  FROM `'._DB_PREFIX_.'product_comment_criterion` pcc
		WHERE pcc.`id_lang` = '.(int)($id_lang).'
		ORDER BY pcc.`name` ASC'));
	}
	
	/**
	 * Delete product criterion by product
	 *
	 * @return boolean succeed
	 */
	public static function deleteByProduct($id_product)
	{
		if (!Validate::isUnsignedId($id_product))
			die(Tools::displayError());
		return (Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'product_comment_criterion_product`
		WHERE `id_product` = '.(int)($id_product)));
	}
	
	/**
	 * Delete all reference of a criterion
	 *
	 * @return boolean succeed
	 */
	public static function delete($id_product_comment_criterion)
	{
		if (!Validate::isUnsignedId($id_product_comment_criterion))
			die(Tools::displayError());
		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'product_comment_grade`
		WHERE `id_product_comment_criterion` = '.(int)($id_product_comment_criterion));
		if ($result === false)
			return ($result);
		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'product_comment_criterion_product`
		WHERE `id_product_comment_criterion` = '.(int)($id_product_comment_criterion));
		if ($result === false)
			return ($result);
		return (Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'product_comment_criterion`
		WHERE `id_product_comment_criterion` = '.(int)($id_product_comment_criterion)));
	}
};