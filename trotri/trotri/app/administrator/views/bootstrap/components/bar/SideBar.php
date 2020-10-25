<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\components\bar;

use tfc\mvc\Widget;
use tfc\saf\Text;
use views\bootstrap\components\ComponentsConstant;

/**
 * SideBar class file
 * 页面左边导航
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: SideBar.php 1 2013-04-20 17:11:06Z huan.song $
 * @package views.bootstrap.components.bar
 * @since 1.0
 */
class SideBar extends Widget
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$config = isset($this->_tplVars['config']) ? $this->_tplVars['config'] : array();
		if (!is_array($config) || $config === array()) {
			return ;
		}

		$html = $this->getHtml();
		$output = '';

		// 菜单最外围开始标签
		$output .= $html->openTag('div', array('class' => 'list-group'));

		// 菜单列表
		foreach ($config as $value) {
			$label = isset($value['label']) ? $value['label'] : '';
			$icon = isset($value['icon']) ? $value['icon'] : array();

			$content = Text::_($label);
			if (is_array($icon) && $icon !== array()) {
				$content .= $this->getIcon($icon);
			}

			$output .= $html->a($content, $this->getUrl($value), $this->getAttributes($value));
		}

		// 菜单最外围结束标签
		$output .= $html->closeTag('div') . '<!--/.list-group-->' . "\n";
		echo $output;
	}

	/**
	 * 获取Icon标签
	 * @param array $config
	 * @return string
	 */
	public function getIcon(array $config)
	{
		$label = isset($config['label']) ? $config['label'] : '';
		return $this->getHtml()->tag('span', array(
			'class'               => 'glyphicon glyphicon-' . ComponentsConstant::GLYPHICON_CREATE . ' pull-right',
			'data-toggle'         => 'tooltip',
			'data-placement'      => 'left',
			'data-original-title' => Text::_($label),
			'onclick' => 'return ' . ComponentsConstant::JSFUNC_HREF . '(\'' . $this->getUrl($config) . '\')'
		), '');
	}

	/**
	 * 通过配置获取A标签
	 * @param array $config
	 * @param boolean $isDropdown
	 * @return array
	 */
	public function getAttributes(array $config, $isDropdown = false)
	{
		$isActive = $this->isActive($config);
		$className = 'list-group-item' . ($isActive ? ' active' : '');
		return array('class' => $className);
	}

	/**
	 * 通过配置判断当前导航是否是选中状态
	 * @param array $config
	 * @return boolean
	 */
	public function isActive(array $config)
	{
		$active = isset($config['active']) ? (boolean) $config['active'] : false;
		return $active;
	}

	/**
	 * 通过配置获取链接
	 * @param array $config
	 * @return string
	 */
	public function getUrl(array $config)
	{
		$mod    = isset($config['m'])      ? $config['m'] : '';
		$ctrl   = isset($config['c'])      ? $config['c'] : '';
		$act    = isset($config['a'])      ? $config['a'] : '';
		$params = isset($config['params']) ? (array) $config['params'] : array();

		return $this->getUrlManager()->getUrl($act, $ctrl, $mod, $params);
	}
}
