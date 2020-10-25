<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp;

//定义

function int_config(){
	return [
		//定义用户基础信息  [type=>['表名','主键'，'getfield','field','searchwhere']]
		'int_user'=>['user'=>['user','id','username','id as id,username as username','username'],'role'=>['role','id','name','id as id,name as username','name']],
		'int_db_prefix'=> 'wf_',//定义数据表前缀
		'int_user_name'=> 'username',//定义用户名称
		'int_user_id'=> 'uid',//定义用户id
		'int_user_role'=> 'role',//定义用户角色
		'black_table'=>['sfdp_design','sfdp_design_ver','sfdp_function','sfdp_script'],//黑名单表，防止重复
	];
}

function tab($step = 1, $string = ' ', $size = 4)
{
    return str_repeat($string, $size * $step);
}
