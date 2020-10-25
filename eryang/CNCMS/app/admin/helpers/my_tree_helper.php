<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 分类树数据
 * @author      二　阳°(QQ:707069100)
 * @link        http://weibo.com/513778937?topnav=1&wvr=5
 * @access   public
 * @param    array    查询的所有数据
 * @param    array    实现分类树的必要字段，默认为 id=当前分类编号字段，pid=当前分类上级编号字段
 * @return   array    处理后形成的一维数据数组
 *
 * @add   last_one     同类中的最后一个，可通过该值实现数据的层次结构
 * @add   have_child   该类拥有子类，可通过该值实现切换子类的显示或隐藏
 * @add   level        代表的是该类的级别，可通过该值实现树状数据显示
 */
function classify_tree($datas, $requisite_field = array('id', 'pid')) {
	if (!$datas) {
		return array();
	}
	$new_datas = array();
	foreach ($datas as $v) {
		$new_datas[$v[$requisite_field[1]]][$v[$requisite_field[0]]] = $v;
	}
	return classify_tree_datas($new_datas, 0);
}

function classify_tree_datas($new_datas, $pid) {
	$CI = &get_instance();
	if (!isset($CI -> cate_list)) {
		$CI -> cate_list = array();
	}
	if (!isset($CI -> level)) {
		$CI -> level = 0;
	}
	foreach ($new_datas[$pid] as $k => $v) {
		$array_keys = array_keys($new_datas[$pid]);
		foreach ($array_keys as $n => $_k) {
			if ($_k == $k) {
				$next_key = isset($array_keys[$n + 1]) ? $array_keys[$n + 1] : '';
				break;
			}
		}
		if (!isset($new_datas[$pid][$next_key])) {
			$v['last_one'] = TRUE;
		}
		if (isset($new_datas[$k])) {
			$v['have_child'] = TRUE;
		}
		$v['level'] = $CI -> level;
		$CI -> cate_list[] = $v;
		if (isset($new_datas[$k])) {
			$CI -> level++;
			classify_tree_datas($new_datas, $k);
			$CI -> level--;
		}
	}
	return $CI -> cate_list;
}
// ------------------------------------------------------------------------
/* End of file my_tree_helper.php */
/* Location: ../app/admin/helpers/my_tree_helper.php */
