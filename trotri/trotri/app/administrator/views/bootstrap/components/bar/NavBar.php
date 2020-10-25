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
use tfc\mvc\Mvc;
use tfc\saf\Cfg;
use tfc\saf\Text;
use tfc\auth\Identity;
use views\bootstrap\components\ComponentsConstant;

/**
 * NavBar class file
 * 页面顶端导航
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: NavBar.php 1 2013-04-20 17:11:06Z huan.song $
 * @package views.bootstrap.components.bar
 * @since 1.0
 */
class NavBar extends Widget
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$output = '';

		$html = $this->getHtml();
		$config = Cfg::getApp('navbar');
		foreach ($config as $menus) {
			$main = array_shift($menus);
			if (!is_array($main)) {
				continue;
			}

			// 主菜单
			if (!$menus) {
				$output .= $html->tag('li', $this->getAttributes($main, false), $this->a($main)) . "\n";
				continue;
			}

			// 主菜单外开始标签
			$output .= $html->openTag('li', $this->getAttributes($main, true)) . "\n";
			$output .= $this->a($main, true) . "\n";

			// 下拉子菜单外开始标签
			$output .= $html->openTag('ul', array('class' => 'dropdown-menu')) . "\n";

			// 下拉子菜单列表
			$total = count($menus);
			$curr = 0;
			foreach ($menus as $menu) {
				$output .= $html->tag('li', array(), $this->a($menu)) . "\n";
				if (++$curr < $total) {
					$output .= $html->tag('li', array('class' => 'divider'), '') . "\n";
				}
			}

			// 下拉子菜单外结束标签
			$output .= $html->closeTag('ul') . "\n";

			// 主菜单外结束标签
			$output .= $html->closeTag('li') . "\n";
		}

		$this->assign('is_login',   Identity::isLogin());
		$this->assign('user_id',    Identity::getUserId());
		$this->assign('login_name', Identity::getLoginName());
		$this->assign('user_name',  Identity::getNickname());
		$this->assign('app_names',  Identity::getAppNames());

		$this->assign('menus',  $output);
		$this->assign('logout', $this->getView()->CFG_SYSTEM_GLOBAL_LOGOUT);
		$this->display();
	}

	/**
	 * 通过配置获取A标签
	 * @param array $config
	 * @param boolean $isDropdown
	 * @return string
	 */
	public function a(array $config, $isDropdown = false)
	{
		$html = $this->getHtml();
		$url = $this->getUrl($config);
		$label = isset($config['label']) ? $config['label'] : '';
		$content = Text::_($label);
		if (isset($config['icon']) && is_array($config['icon']) && $config['icon'] !== array()) {
			$content .= $this->getIcon($config['icon']);
		}
		if ($isDropdown) {
			return $html->a(
				$content . ' ' . $html->tag('b', array('class' => 'caret'), ''),
				$url,
				array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown')
			);
		}

		return $html->a($content, $url);
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
	 * 通过配置获取LI标签的属性
	 * @param array $config
	 * @param boolean $isDropdown
	 * @return array
	 */
	public function getAttributes(array $config, $isDropdown = false)
	{
		$isActive = $this->isActive($config);
		$className = ($isDropdown ? 'dropdown ' : '') . ($isActive ? 'active' : '');
		if (($className = trim($className)) !== '') {
			return array('class' => $className);
		}

		return array();
	}

	/**
	 * 通过配置判断当前导航是否是选中状态
	 * @param array $config
	 * @return boolean
	 */
	public function isActive(array $config)
	{
		$mod = isset($config['m']) ? $config['m'] : '';
		return ($mod === Mvc::$module);
	}

	/**
	 * 通过配置获取链接
	 * @param array $config
	 * @return string
	 */
	public function getUrl(array $config)
	{
		if (isset($config['url'])) {
			return $config['url'];
		}

		$mod    = isset($config['m'])      ? $config['m'] : '';
		$ctrl   = isset($config['c'])      ? $config['c'] : '';
		$act    = isset($config['a'])      ? $config['a'] : '';
		$params = isset($config['params']) ? (array) $config['params'] : array();

		return $this->getUrlManager()->getUrl($act, $ctrl, $mod, $params);
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::getWidgetDirectory()
	 */
	public function getWidgetDirectory()
	{
		if ($this->_widgetDirectory === null) {
			$this->_widgetDirectory = dirname(__FILE__) . DS . 'navbar';
		}

		return $this->_widgetDirectory;
	}
}
