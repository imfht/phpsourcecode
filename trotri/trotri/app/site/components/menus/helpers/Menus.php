<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\menus\helpers;

use libsrv\Service;
use tfc\mvc\Mvc;
use tfc\auth\Identity;

/**
 * Menus class file
 * 菜单帮助类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Menus.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.menus.helpers
 * @since 1.0
 */
class Menus
{
	/**
	 * 递归获取指定类型下的所有菜单
	 * @param string $typeKey
	 * @param integer $menuPid
	 * @return array
	 */
	public static function findRows($typeKey, $menuPid = 0)
	{
		$allowUnregistered = Identity::isLogin() ? true : false;
		$rows = self::getService()->findRows($typeKey, $menuPid, $allowUnregistered);
		return $rows;
	}

	/**
	 * 获取A标签
	 * @param array $data
	 * @param array $attributes
	 * @return string
	 */
	public static function a(array &$data, array $attributes = array())
	{
		if ($attributes === array()) {
			$attributes = self::getAttributes($data);
		}

		$url     = isset($data['menu_url'])  ? $data['menu_url']  : '#';
		$content = isset($data['menu_name']) ? $data['menu_name'] : '';
		return Mvc::getView()->getHtml()->a($content, $url, $attributes);
	}

	/**
	 * 获取A标签的属性
	 * @param array $data
	 * @return array
	 */
	public static function getAttributes(array &$data)
	{
		$attributes = array();

		$target = isset($data['attr_target']) ? $data['attr_target'] : '';
		$title  = isset($data['attr_title'])  ? $data['attr_title']  : '';
		$rel    = isset($data['attr_rel'])    ? $data['attr_rel']    : '';
		$class  = isset($data['attr_class'])  ? $data['attr_class']  : '';
		$style  = isset($data['attr_style'])  ? $data['attr_style']  : '';

		if ($target !== '') {
			$attributes['target'] = $target;
		}

		if ($title !== '') {
			$attributes['title'] = $title;
		}

		if ($rel !== '') {
			$attributes['rel'] = $rel;
		}

		if ($class !== '') {
			$attributes['class'] = $class;
		}

		if ($style !== '') {
			$attributes['style'] = $style;
		}

		return $attributes;
	}

	/**
	 * 获取菜单业务处理类
	 * @return \menus\services\Menus
	 */
	public static function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Menus', 'menus');
		}

		return $service;
	}

}
