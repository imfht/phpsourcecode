<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class MenuTopLinks
{
	public static function gets($id_lang, $id_linksmenutop = null, $id_shop)
	{
		$sql = 'SELECT l.id_linksmenutop, l.new_window, ll.link, ll.label
				FROM '._DB_PREFIX_.'linksmenutop l
				LEFT JOIN '._DB_PREFIX_.'linksmenutop_lang ll ON (l.id_linksmenutop = ll.id_linksmenutop AND ll.id_lang = '.(int)$id_lang.' AND ll.id_shop='.(int)$id_shop.')
				WHERE 1 '.((!is_null($id_linksmenutop)) ? ' AND l.id_linksmenutop = "'.(int)$id_linksmenutop.'"' : '').'
				AND l.id_shop IN (0, '.(int)$id_shop.')';

		return Db::getInstance()->executeS($sql);
	}

	public static function get($id_linksmenutop, $id_lang, $id_shop)
	{
		return self::gets($id_lang, $id_linksmenutop, $id_shop);
	}

	public static function getLinkLang($id_linksmenutop, $id_shop)
	{
		$ret = Db::getInstance()->executeS('
			SELECT l.id_linksmenutop, l.new_window, ll.link, ll.label, ll.id_lang
			FROM '._DB_PREFIX_.'linksmenutop l
			LEFT JOIN '._DB_PREFIX_.'linksmenutop_lang ll ON (l.id_linksmenutop = ll.id_linksmenutop AND ll.id_shop='.(int)$id_shop.')
			WHERE 1
			'.((!is_null($id_linksmenutop)) ? ' AND l.id_linksmenutop = "'.(int)$id_linksmenutop.'"' : '').'
			AND l.id_shop IN (0, '.(int)$id_shop.')
		');

		$link = array();
		$label = array();
		$new_window = false;

		foreach ($ret as $line)
		{
			$link[$line['id_lang']] = Tools::safeOutput($line['link']);
			$label[$line['id_lang']] = Tools::safeOutput($line['label']);
			$new_window = (bool)$line['new_window'];
		}

		return array('link' => $link, 'label' => $label, 'new_window' => $new_window);
	}

	public static function add($link, $label, $newWindow = 0, $id_shop)
	{
		if(!is_array($label))
			return false;
		if(!is_array($link))
			return false;

		Db::getInstance()->insert(
			'linksmenutop',
			array(
				'new_window'=>(int)$newWindow,
				'id_shop' => (int)$id_shop
			)
		);
		$id_linksmenutop = Db::getInstance()->Insert_ID();

		foreach ($label as $id_lang=>$label)
		Db::getInstance()->insert(
			'linksmenutop_lang',
			array(
				'id_linksmenutop'=>(int)$id_linksmenutop,
				'id_lang'=>(int)$id_lang,
				'id_shop'=>(int)$id_shop,
				'label'=>pSQL($label),
				'link'=>pSQL($link[$id_lang])
			)
		);
	}

	public static function update($link, $labels, $newWindow = 0, $id_shop, $id_link)
	{
		if(!is_array($labels))
			return false;
		if(!is_array($link))
			return false;

		Db::getInstance()->update(
			'linksmenutop',
			array(
				'new_window'=>(int)$newWindow,
				'id_shop' => (int)$id_shop
			),
			'id_linksmenutop = '.(int)$id_link
		);

		foreach ($labels as $id_lang => $label)
			Db::getInstance()->update(
				'linksmenutop_lang',
				array(
					'id_shop'=>(int)$id_shop,
					'label'=>pSQL($label),
					'link'=>pSQL($link[$id_lang])
				),
				'id_linksmenutop = '.(int)$id_link.' AND id_lang = '.(int)$id_lang
			);
	}


	public static function remove($id_linksmenutop, $id_shop)
	{
		Db::getInstance()->delete('linksmenutop', 'id_linksmenutop = '.(int)$id_linksmenutop.' AND id_shop = '.(int)$id_shop);
		Db::getInstance()->delete('linksmenutop_lang', 'id_linksmenutop = '.(int)$id_linksmenutop);
	}

}

?>
