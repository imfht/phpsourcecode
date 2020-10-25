<?php
function get_cate($id, $field = null){
        static $list;

        /* 非法分类ID */
        if(empty($id) || !is_numeric($id)){
            return '';
        }

        /* 读取缓存数据 */
        if(empty($list)){
            $list = S('sys_cate_list');
        }

        /* 获取分类名称 */
        if(!isset($list[$id])){
            $cate = M('Cate')->find($id);
            if(!$cate || 1 != $cate['status']){ //不存在分类，或分类被禁用
                return '';
            }
            $list[$id] = $cate;
            S('sys_cate_list', $list); //更新缓存
        }
        return is_null($field) ? $list[$id] : $list[$id][$field];
}
function get_cate_nameByid($id){
	
	$map['id']=$id;
	$name = M('Cate')->where($map)->getField('name');
	return $name;
}
function get_cate_typeByid($id){

	$map['id']=$id;
	$type = M('Cate')->where($map)->getField('type');
	return $type;
}

function getcpid($id){
	
	
	$map['id']=$id;
	$pid = M('Cate')->where($map)->getField('pid');
	if($id==0){
		$pid=0;
	}
	
	return $pid;
}
function getcidparent($cid){
	
	$map['id']=$cid;
	$spid = M('Cate')->where($map)->getField('spid');
	
	$arr=explode('|', $spid);
	if($arr[0]==0){
		return $cid;
	}
   if($cid==0){
   	
   	return 0;
   }
	return $arr[0];
}