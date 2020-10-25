<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\components;

use tfc\mvc\Mvc;
use tfc\saf\Text;
use library\SubmitType;

/**
 * ComponentsBuilder class file
 * 创建页面小组件类，用于创建按钮、图标等，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ComponentsBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.components
 * @since 1.0
 */
class ComponentsBuilder
{
	/**
	 * 获取表单的“保存”按钮信息
	 * @return array
	 */
	public static function getButtonSave()
	{
		$output = array(
			'type'      => 'button',
			'label'     => Text::_('CFG_SYSTEM_GLOBAL_SAVE'),
			'glyphicon' => ComponentsConstant::GLYPHICON_SAVE,
			'class'     => 'btn btn-primary',
			'onclick'   => 'return ' . ComponentsConstant::JSFUNC_FORMSUBMIT . '(this, \'' . SubmitType::TYPE_SAVE . '\', \'\');'
		);

		return $output;
	}

	/**
	 * 获取表单的“保存并关闭”按钮信息
	 * @return array
	 */
	public static function getButtonSaveClose()
	{
		$output = array(
			'type'      => 'button',
			'label'     => Text::_('CFG_SYSTEM_GLOBAL_SAVE2CLOSE'),
			'glyphicon' => ComponentsConstant::GLYPHICON_RESTORE,
			'class'     => 'btn btn-default',
			'onclick'   => 'return ' . ComponentsConstant::JSFUNC_FORMSUBMIT . '(this, \'' . SubmitType::TYPE_SAVE_CLOSE . '\', \'\');'
		);

		return $output;
	}

	/**
	 * 获取表单的“保存并新建”按钮信息
	 * @return array
	 */
	public static function getButtonSaveNew()
	{
		$output = array(
			'type'      => 'button',
			'label'     => Text::_('CFG_SYSTEM_GLOBAL_SAVE2NEW'),
			'glyphicon' => ComponentsConstant::GLYPHICON_CREATE,
			'class'     => 'btn btn-default',
			'onclick'   => 'return ' . ComponentsConstant::JSFUNC_FORMSUBMIT . '(this, \'' . SubmitType::TYPE_SAVE_NEW . '\', \'\');'
		);

		return $output;
	}

	/**
	 * 获取表单的“取消”按钮信息，如果存在“最后一次访问的列表页链接”，则跳转到“最后一次访问的列表页”，否则跳转到缺省页面
	 * @param string $url
	 * @return array
	 */
	public static function getButtonCancel($url = '')
	{
		$output = array(
			'type'      => 'button',
			'label'     => Text::_('CFG_SYSTEM_GLOBAL_CANCEL'),
			'glyphicon' => ComponentsConstant::GLYPHICON_REMOVE,
			'class'     => 'btn btn-danger',
			'onclick'   => 'return ' . ComponentsConstant::JSFUNC_HREF . '(\'' . $url . '\');'
		);

		return $output;
	}

	/**
	 * 获取表单的“返回”按钮信息，如果存在“最后一次访问的列表页链接”，则跳转到“最后一次访问的列表页”，否则跳转到缺省页面
	 * @return array
	 */
	public static function getButtonHistoryBack()
	{
		$output = array(
			'type'      => 'button',
			'label'     => Text::_('CFG_SYSTEM_GLOBAL_HISTORY_BACK'),
			'glyphicon' => ComponentsConstant::GLYPHICON_HISTORYBACK,
			'class'     => 'btn btn-default',
			'onclick'   => 'return history.back();'
		);

		return $output;
	}

	/**
	 * 获取美化版“是|否”选择项表单元素
	 * @param array $params
	 * @return string
	 */
	public static function getSwitch(array $params = array())
	{
		$on    = isset($params['on'])    ? $params['on']       : 'y';
		$off   = isset($params['off'])   ? $params['off']      : 'n';
		$id    = isset($params['id'])    ? (int) $params['id'] : 0;
		$name  = isset($params['name'])  ? $params['name']     : '';
		$value = isset($params['value']) ? $params['value']    : $off;
		$href  = isset($params['href'])  ? $params['href']     : '';

		$attributes = array(
			'class'          => 'switch',
			'data-on-label'  => Text::_('CFG_SYSTEM_GLOBAL_YES'),
			'data-off-label' => Text::_('CFG_SYSTEM_GLOBAL_NO'),
			'tbl_switch'     => 'yes',
			'id'             => 'label_switch_' . $name . '_' . $id,
			'name'           => 'label_switch',
		);

		if ($href !== '') {
			$attributes['href'] = $href;
		}

		return Mvc::getView()->getHtml()->tag('div', $attributes, Mvc::getView()->getHtml()->checkbox($name, $value, ($value === 'y')));
	}

	/**
	 * 获取Glyphicons图标按钮和工具提示
	 * @param array $params
	 * @return string
	 */
	public static function getGlyphicon(array $params = array())
	{
		$type      = isset($params['type'])      ? $params['type']      : '';
		$url       = isset($params['url'])       ? $params['url']       : '';
		$jsfunc    = isset($params['jsfunc'])    ? $params['jsfunc']    : '';
		$title     = isset($params['title'])     ? $params['title']     : '';
		$placement = isset($params['placement']) ? $params['placement'] : 'left';
		$style     = isset($params['style'])     ? $params['style'] : '';

		$click = $jsfunc . '(\'' . $url . '\')';
		$attributes = array(
			'class'               => 'glyphicon glyphicon-' . $type,
			'data-toggle'         => 'tooltip',
			'data-placement'      => $placement,
			'data-original-title' => $title,
			'onclick'             => 'return ' . $click . ';'
		);

		if ($style !== '') {
			$attributes['style'] = $style;
		}

		return Mvc::getView()->getHtml()->tag('span', $attributes, '');
	}
}
