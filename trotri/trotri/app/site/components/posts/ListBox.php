<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\posts;

use libapp\Component;
use tfc\saf\Text;
use library\UrlHelper;
use components\posts\helpers\Posts AS Helper;
use components\posts\helpers\Categories;

/**
 * ListBox class file
 * 文档列表盒子
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ListBox.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.posts
 * @since 1.0
 */
class ListBox extends Component
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$findType = isset($this->find_type) ? trim($this->find_type) : '';
		if ($findType === '') {
			return ;
		}

		$limit = isset($this->limit) ? (int) $this->limit : 0;
		$offset = isset($this->offset) ? (int) $this->offset : 0;
		$title = isset($this->title) ? trim($this->title) : '';

		$catId = 0;
		if (($p = strpos($findType, '-')) !== false) {
			$catId = (int) substr($findType, $p + 1);
			$findType = substr($findType, 0, $p);
		}

		$findType = strtolower($findType);
		if (!in_array($findType, Helper::$findTypes)) {
			return ;
		}

		$title = '';
		$url = '';
		if ($findType === Helper::FIND_TYPE_CATID) {
			if ($catId <= 0) {
				return ;
			}

			$row = Categories::findByPk($catId);
			if (!$row || !is_array($row) || !isset($row['category_name'])) {
				return ;
			}

			$title = $row['category_name'];
			$url = UrlHelper::getInstance()->getPostIndex($row);
		}
		else {
			switch (true) {
				case $findType === Helper::FIND_TYPE_HEAD:
					$title = Text::_('MOD_POSTS_POSTS_HEAD');
					break;
				case $findType === Helper::FIND_TYPE_RECOMMEND:
					$title = Text::_('MOD_POSTS_POSTS_RECOMMEND');
					break;
				default:
					break;
			}
		}

		$rows = array();
		switch (true) {
			case $findType === Helper::FIND_TYPE_CATID:
				$rows = Helper::getRowsByCatId($catId, '', $limit, $offset);
				break;
			case $findType === Helper::FIND_TYPE_HEAD:
				$rows = Helper::getHeads($limit, $offset);
				break;
			case $findType === Helper::FIND_TYPE_RECOMMEND:
				$rows = Helper::getRecommends($limit, $offset);
				break;
			default:
				break;
		}

		$isShow = false;
		if ($title !== '' && $rows && is_array($rows)) {
			$isShow = true;
		}

		$this->assign('is_show', $isShow);
		$this->assign('title', $title);
		$this->assign('url', $url);
		$this->assign('rows', $rows);
		$this->display();
	}
}
