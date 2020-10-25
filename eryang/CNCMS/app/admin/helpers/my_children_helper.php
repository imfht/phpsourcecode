<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 获取子类的编号
 * @author
 * 
 * @access   public
 * @param    number    当前类的编号值
 * @param    string    查询表
 * @param    boolean   是否查询第一代子类
 * @param    array     查询的必要字段，默认为 id=当前类编号字段，pid=当前类代表上级的编号字段
 * @return   array     返回一维数组，值为当前类的子类编号值
 */
function children_ids($id, $table, $first_children = FALSE, $requisite_field = array('id', 'pid'))
{
	$CI = &get_instance();
	if(!isset($CI->children_ids))
	{
		$CI->children_ids = array();
	}
	$children_datas = $CI->base_model->get_all($table, array($requisite_field[1] => $id), $requisite_field[0]);
	foreach($children_datas as $data)
	{
		$CI->children_ids[] = $data[$requisite_field[0]];
		if(!$first_children)
		{
			children_ids($data[$requisite_field[0]], $table, TRUE, $requisite_field);
		}
	}
	return $CI->children_ids;
}
// ------------------------------------------------------------------------
/* End of file my_children_helper.php */
/* Location: ./app/admin/helpers/my_children_helper.php */