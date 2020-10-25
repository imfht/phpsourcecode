<?php
/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/**
 * 动态扩展左侧菜单,base.html里用到
 */
function extra_menu($extra_menu,&$base_menu){
    foreach ($extra_menu as $key=>$group){
        if( isset($base_menu['child'][$key]) ){
            $base_menu['child'][$key] = array_merge( $base_menu['child'][$key], $group);
        }else{
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 动态拼接sql查询条件
 */
function extra_where($column_list,$where){

	if ($where) {
		foreach ($column_list as $key => $value) {
			$sql.=" or ".$value['vname']." like '%".$where."%'";
		}
		return substr($sql,3);
	}
}
