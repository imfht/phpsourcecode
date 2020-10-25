<?php
namespace blog\libs\template;
use \sy\base\Router;
use \blog\libs\Common;
use \blog\libs\template\ItemArticle;
class ListArticle extends \blog\libs\template\ListBase {
	public $num;
	public $nowPage;
	public $find;
	/**
	 * 构造函数
	 * @access public
	 * @param array $list
	 */
	public function __construct($list, $num, $find) {
		$this->num = $num;
		$this->find = $find;
		$this->list = $list;
		reset($this->list);
	}
	/**
	 * 移到下一个
	 * @access public
	 * @return array
	 */
	public function next() {
		$r = current($this->list);
		next($this->list);
		return $r === FALSE ? FALSE : new ItemArticle($r);
	}
	/**
	 * 获得页码总数
	 * @access public
	 * @return int
	 */
	public function getMaxPage() {
		$pagesize = intval(Common::option('pagesize'));
		$max = intval(ceil($this->num / $pagesize));
		if ($max === 0) {
			$max = 1;
		}
		return $max;
	}
	/**
	 * 设置页码
	 * @access public
	 */
	public function setPage($page) {
		$this->nowPage = intval($page);
	}
	/**
	 * 输出页码导航
	 * @access public
	 * @param string $box
	 * @param string $current
	 */
	public function pageNav($box = 'li', $itemClass = 'item', $currentClass = 'active', $prev = '上一页', $next = '下一页', $aClass = '') {
		$maxPage = $this->getMaxPage();
		$nowPage = $this->nowPage;
		$current = ($nowPage>=3?$nowPage-2:1);
		$type = isset($this->find['find'])?$this->find['find']:'all';
		$val = isset($this->find[$type])?$this->find[$type]:'0';
		$aClassText = empty($aClass)?'':' class="' . $aClass . '"';
		$itemClassText = empty($itemClass)?'':' class="' . $itemClass . '"';
		if (!empty($prev) && $nowPage !== 1) {
			echo '<', $box, $itemClassText, '><a href="', Router::createUrl(['index/article/list', 'type' => $type, 'val' => $val, 'page' => $nowPage - 1]);
			echo '"', $aClassText, '>', $prev, '</a></', $box, '>';
		}
		for ($i = 1;($i <= 5 && $current <= $maxPage);) {
			echo '<', $box;
			if ($current === $nowPage) {
				if (!empty($itemClass)) {
					echo ' class="', $currentClass, ' ', $itemClass, '"';
				} else {
					echo ' class="', $currentClass, '"';
				}
			} else {
				if (!empty($itemClass)) {
					echo ' class="', $itemClass, '"';
				}
			}
			echo '><a';
			if ($current !== $nowPage) {
				echo ' href="', Router::createUrl(['index/article/list', 'type' => $type, 'val' => $val, 'page' => $current]), '"';
			}
			echo $aClassText, '>', $current, '</a></', $box, '>';
			$i++;
			$current++;
		}
		if (!empty($next) && $nowPage !== $maxPage) {
			echo '<', $box, $itemClassText, '><a href="', Router::createUrl(['index/article/list', 'type' => $type, 'val' => $val, 'page' => $nowPage + 1]);
			echo '"', $aClassText, '>', $next, '</a></', $box, '>';
		}
	}
}