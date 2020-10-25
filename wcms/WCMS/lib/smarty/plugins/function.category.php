<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {counter} function plugin
 *
 * Type:     function<br>
 * Name:     counter<br>
 * Purpose:  print out a counter value
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link http://www.smarty.net/manual/en/language.function.counter.php {counter}
 * (Smarty online manual)
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string|null
 */
function smarty_function_category($params, $template) {
	
	$limitNum = isset ( $params ['num'] ) ? $params ['num'] : 10;
	
	//获取制定分类的内容  fid是指他自己的 
	if (isset ( $params ['fid'] )) {
		$rs = CategoryModel::instance ()->getCategoryByFid ( $params ['fid'], $limitNum );
		
		if (empty ( $rs )) {
			$cate = CategoryModel::instance ()->getCateogryById ( $params ['fid'] );
			$rs = CategoryModel::instance ()->getCategoryByFid ( $cate ['fid'], $limitNum );
		
		}
		$template->assign ( $params ['assign'], $rs );
	}

}

?>