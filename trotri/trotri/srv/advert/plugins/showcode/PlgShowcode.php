<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace advert\plugins\showcode;

use tfc\ap\Event;
use tfc\ap\Singleton;
use tfc\saf\Log;
use libsrv\Service;
use libapp\ErrorNo;
use advert\services\DataAdverts;

/**
 * PlgShowcode class file
 * 拼接广告展现代码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PlgShowcode.php 1 2013-04-05 01:38:06Z huan.song $
 * @package advert.plugins.showcode
 * @since 1.0
 */
class PlgShowcode extends Event
{
	/**
	 * @var array 特殊广告位
	 */
	protected $_specialTypes = array(
	);

	/**
	 * @var instance of tfc\mvc\Html
	 */
	protected $_html = null;

	/**
	 * 新增或编辑前执行
	 * @param string $context
	 * @param array $row
	 * @param mixed $params
	 * @return void
	 */
	public function onBeforeSave($context, array &$row, $params = null)
	{
		$isCreate = ($context === 'advert\services\Adverts::create')     ? true : false;
		$isModify = ($context === 'advert\services\Adverts::modifyByPk') ? true : false;

		if (!$isCreate && !$isModify) {
			return ;
		}

		$enum = DataAdverts::getShowTypeEnum();
		$showType = isset($row['show_type']) ? trim($row['show_type']) : '';
		if (!isset($enum[$showType])) {
			return ;
		}

		if ($showType === DataAdverts::SHOW_TYPE_CODE) {
			return ;
		}

		if (isset($row['show_code'])) { unset($row['show_code']); }

		$columns = array('advert_url', 'title', 'advert_src', 'advert_src2', 'attr_alt', 'attr_width', 'attr_height', 'attr_fontsize', 'attr_target');

		$hasColumn = false;
		foreach ($row as $columnName => $value) {
			if (in_array($columnName, $columns)) {
				$hasColumn = true;
				break;
			}
		}

		if (!$hasColumn) {
			return ;
		}

		if ($isModify) {
			if (($advertId = (int) $params) <= 0) {
				return ;
			}

			$advert = Service::getInstance('Adverts', 'advert')->findByPk($advertId);
			if (!$advert || !is_array($advert) || !isset($advert['advert_id']) || !isset($advert['advert_url'])) {
				Log::warning(sprintf(
					'PlgShowcode is unable to find the result by id "%d"', $advertId
				), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

				return ;
			}

			foreach ($columns as $columnName) {
				if (!isset($row[$columnName])) {
					$row[$columnName] = $advert[$columnName];
				}
			}
		}

		$typeKey = isset($row['type_key']) ? strtolower(trim($row['type_key'])) : '';
		if (in_array($typeKey, $this->_specialTypes)) {
			$object = Singleton::getInstance('advert\\plugins\\showcode\\Special' . ucfirst($typeKey));
		}
		else {
			$object = $this;
		}

		$object->setShowCode($showType, $row, $this);
	}

	/**
	 * 获取展现代码
	 * @param string $showType
	 * @param array $row
	 * @param advert\plugins\showcode\PlgShowcode $object
	 * @return string
	 */
	public function setShowCode($showType, array &$row, PlgShowcode $object = null)
	{
		$showType = isset($row['show_type']) ? trim($row['show_type']) : '';

		$url = isset($row['advert_url']) ? trim($row['advert_url']) : '';
		$title = isset($row['title']) ? trim($row['title']) : '';
		$src = isset($row['advert_src']) ? trim($row['advert_src']) : '';
		$src2 = isset($row['advert_src2']) ? trim($row['advert_src2']) : '';
		$alt = isset($row['attr_alt']) ? trim($row['attr_alt']) : '';
		$width = isset($row['attr_width']) ? (int) $row['attr_width'] : 0;
		$height = isset($row['attr_height']) ? (int) $row['attr_height'] : 0;
		$fontsize = isset($row['attr_fontsize']) ? trim($row['attr_fontsize']) : '';
		$target = isset($row['attr_target']) ? trim($row['attr_target']) : '';

		switch (true) {
			case $showType === DataAdverts::SHOW_TYPE_TEXT:
				$row['show_code'] = $this->getTextCode($url, $title, $fontsize, $target);
				break;
			case $showType === DataAdverts::SHOW_TYPE_IMAGE:
				$row['show_code'] = $this->getImageCode($url, $src, $alt, $src2, $width, $height, $target);
				break;
			case $showType === DataAdverts::SHOW_TYPE_FLASH:
				$row['show_code'] = $this->getFlashCode($src, $width, $height);
				break;
			default :
				break;
		}
	}

	/**
	 * 获取文字类型展现代码
	 * @param string $url
	 * @param string $title
	 * @param string $fontsize
	 * @param string $target
	 * @param array $attributes
	 * @return string
	 */
	public function getTextCode($url, $title, $fontsize, $target = '_blank', array $attributes = array())
	{
		if (($url = trim($url)) === '') {
			return '';
		}

		if (($title = trim($title)) === '') {
			return '';
		}

		if (($fontsize = trim($fontsize)) !== '') {
			$attributes['style'] = 'font-size: ' . $fontsize;
		}

		if (($target = trim($target)) !== '') {
			$attributes['target'] = $target;
		}

		return $this->getHtml()->a($title, $url, $attributes);
	}

	/**
	 * 获取图片类型展现代码
	 * @param string $url
	 * @param string $src
	 * @param string $alt
	 * @param string $src2
	 * @param integer $width
	 * @param integer $height
	 * @param string $target
	 * @param array $attributes
	 * @return string
	 */
	public function getImageCode($url, $src, $alt, $src2, $width = 0, $height = 0, $target = '_blank', array $attributes = array())
	{
		if (($url = trim($url)) === '') {
			return '';
		}

		if (($src = trim($src)) === '') {
			return '';
		}

		if (($alt = trim($alt)) === '') {
			return '';
		}

		$html = $this->getHtml();

		if (($width = (int) $width) > 0) {
			$attributes['width'] = $width;
		}

		if (($height = (int) $height) > 0) {
			$attributes['height'] = $height;
		}

		$content = $html->img($src, $alt, $attributes);
		if (($src2 = trim($src2)) !== '') {
			$content .= $html->img($src2, $alt, $attributes);
		}

		return $html->a($content, $url, ((($target = trim($target)) !== '') ? array('target' => $target) : array()));
	}

	/**
	 * 获取Flash类型展现代码
	 * @param string $src
	 * @param integer $width
	 * @param integer $height
	 * @param array $attributes
	 * @return string
	 */
	public function getFlashCode($src, $width = 0, $height = 0, array $attributes = array())
	{
		if (($src = trim($src)) === '') {
			return '';
		}

		$attributes['type'] = 'application/x-shockwave-flash';
		$attributes['wmode'] = 'transparent';
		$attributes['src'] = $src;

		if (($width = (int) $width) > 0) {
			$attributes['width'] = $width;
		}

		if (($height = (int) $height) > 0) {
			$attributes['height'] = $height;
		}

		return $this->getHtml()->tag('embed', $attributes, '');
	}

	/**
	 * 获取页面辅助类
	 * @return tfc\mvc\Html
	 */
	public function getHtml()
	{
		if ($this->_html === null) {
			$this->_html = Singleton::getInstance('tfc\\mvc\\Html');
		}

		return $this->_html;
	}

}
