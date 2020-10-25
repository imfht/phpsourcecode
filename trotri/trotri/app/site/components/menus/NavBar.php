<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\menus;

use libapp\Component;
use components\menus\helpers\Menus AS Helper;

/**
 * NavBar class file
 * 页面顶端导航
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: NavBar.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.menus
 * @since 1.0
 */
class NavBar extends Component
{
	/**
	 * @var string 主导航类型Key
	 */
	const TYPE_KEY = 'mainnav';

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$output = '';
		$typeKey = self::TYPE_KEY;

		$html = $this->getHtml();
		$menus = Helper::findRows($typeKey);
		$divider = $html->tag('li', array('class' => 'divider'), '');

		if ($menus && is_array($menus)) {
			foreach ($menus as $rows) {
				if ($rows['data'] && is_array($rows['data'])) {
					$output .= $html->openTag('li', array('class' => 'dropdown'));
					$output .= $this->a($rows, true);
					$output .= $html->openTag('ul', array('class' => 'dropdown-menu'));
					foreach ($rows['data'] as $_) {
						$output .= $html->tag('li', array(), $this->a($_));
						$output .= $divider;
					}

					$output = substr($output, 0, -strlen($divider));
					$output .= $html->closeTag('ul');
				}
				else {
					$output .= $html->openTag('li');
					$output .= $this->a($rows);
				}

				$output .= $html->closeTag('li');
			}
		}

		$this->assign('menus',  $output);
		$this->display();
	}

	/**
	 * 获取A标签
	 * @param array $data
	 * @param boolean $isDropdown
	 * @return string
	 */
	public function a(array &$data, $isDropdown = false)
	{
		$html    = $this->getHtml();
		$url     = isset($data['menu_url'])  ? $data['menu_url']  : '#';
		$content = isset($data['menu_name']) ? $data['menu_name'] : '';

		if ($isDropdown) {
			$content .= ' ' . $html->tag('b', array('class' => 'caret'), '');
		}

		return $html->a($content, $url, $this->getAttributes($data, $isDropdown));
	}

	/**
	 * 获取A标签的属性
	 * @param array $data
	 * @param boolean $isDropdown
	 * @return array
	 */
	public function getAttributes(array &$data, $isDropdown = false)
	{
		$attributes = Helper::getAttributes($data);
		if ($isDropdown) {
			$attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' dropdown-toggle' : 'dropdown-toggle';
			$attributes['data-toggle'] = 'dropdown';
		}

		return $attributes;
	}
}
