<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 无级分类下拉列表数据处理
 *
 * @copyright Copyright (c) 2013 - , lensic [mhy].
 *           
 * @access public
 * @param
 *        	array 参数数组
 * @return string json 字符串数据
 *        
 * @example $arg = array
 *          (
 *          'id_name' => 'id', 数据编号的字段名
 *          'pid_name' => 'pid', 上级数据编号的字段名
 *          'field_name' => 'type_name', 显示数据的字段名
 *          'table' => '', 查询数据的表
 *          'pid' => '', 当前数据的所属上级编号
 *          'id' => '', 当前数据的编号
 *          'display_type' => '' 是否显示当前数据，有值=是，空值=否
 *          );
 *         
 *          调用示例：
 *         
 *          从顶级分类 0 开始，显示无限级别分类
 *          stepless_classification('show_list_one', 'pid[]', '<?php echo base_url(), 'home/index/'; ?>', 0);
 *         
 *          从 id=11 数据开始，不显示 id=11 数据项，但显示它的同类和父类
 *          stepless_classification('show_list_two', 'pid[]', '<?php echo base_url(), 'home/index/'; ?>', 3, '/11');
 *         
 *          从 id=11 数据开始，显示 id=11 数据项并选中，同时显示它的同类和父类，但不显示子类
 *          stepless_classification('show_list_three', 'pid[]', '<?php echo base_url(), 'home/index/'; ?>', 3, '/11/', 1);
 *         
 *          从 id=11 数据开始，显示 id=11 数据项并选中，同时显示它的同类、父类和子类
 *          stepless_classification('show_list_four', 'pid[]', '<?php echo base_url(), 'home/index/'; ?>', 3, '/11/', 2);
 */
function ajax_stepless_classification($arg = array()) {
	$CI = &get_instance ();
	$default = array (
			'id_name' => 'id',
			'pid_name' => 'pid',
			'field_name' => 'type_name',
			'table' => '',
			'pid' => $CI->uri->segment ( 3 ),
			'id' => $CI->uri->segment ( 4 ),
			'display_type' => $CI->uri->segment ( 5 ) 
	);
	$new_arg = array_merge ( $default, $arg );
	$where = array (
			$new_arg ['pid_name'] => $new_arg ['pid'] 
	);
	if (! $new_arg ['display_type'] && $new_arg ['id']) {
		$where [$new_arg ['id_name'] . ' <> '] = $new_arg ['id'];
	}
	$datas = $CI->base_model->get_all ( $new_arg ['table'], $where, $new_arg ['id_name'] . ', ' . $new_arg ['field_name'] );
	$str = '';
	foreach ( $datas as $row ) {
		$str .= "sel.options[sel.options.length] = new Option('{$row[$new_arg['field_name']]}', '{$row[$new_arg['id_name']]}');";
	}
	$pp_data = $CI->base_model->get_one ( $new_arg ['table'], array (
			$new_arg ['id_name'] => $new_arg ['pid'] 
	), $new_arg ['pid_name'] );
	$ppid = '';
	if ($pp_data) {
		$ppid = $pp_data [$new_arg ['pid_name']];
	}
	$json = '({sels:"' . $str . '", ppid:"' . $ppid . '", pid:"' . $new_arg ['pid'] . '"})';
	
	return $json;
}
// ------------------------------------------------------------------------
/* End of file my_stepless_classification_helper.php */
/* Location: ./app/admin/helpers/my_stepless_classification_helper.php */