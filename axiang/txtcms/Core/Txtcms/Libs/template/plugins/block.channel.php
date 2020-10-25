<?php
/*
* 栏目标签
* $params	支持参数 row='调用数量' as='别名' id='上级栏目id' pid='同左边'
*/
function smarty_block_channel($params, $content, &$smarty, &$repeat){
	if(empty($params)){
		$params['pid']=$params['id']=0;
	}
	$params=array_map('trim',$params);
	$dataname = md5(__FUNCTION__ . md5(serialize($params)));
	$dataname = substr($dataname,0,16);
	extract($params);
	$type=(isset($type)) ? $type : '';
	if($type=='top') $id=0;
	$as=(!isset($as) || $as=="") ? 'vo' : $as;
	if(!isset($row) || !is_numeric($row)) $row = 999;
	if (!isset($id)) $id = isset($_GET['cid'])?intval($_GET['cid']):'0';
	//兼容PID
	if(isset($pid) && !isset($id)) $id=$pid;
	$table='arctype';
	if(!isset($smarty->block_data)) $smarty->block_data=array();
	//填充数据
	if(!isset($smarty->block_data[$dataname])){
		$where=array('pid='.$id,'and','isshow=1');
		$data=DB($table)->where($where)->limit($row)->order('order DESC')->select();
		// 如果没有下级分类则获取同级分类
		if (!$data && $type == 'self') {
			$result=DB($table)->where('id='.$id)->find();
			$pid = $result['pid'];
			$where = array('pid='.$pid, 'and', 'isshow=1');
			$data=DB($table)->where($where)->limit($row)->order('order DESC')->select();
		}
		$reclass='article';
		foreach($data as $k=>$vo){
			$data[$k]['url']=get_list_url($data[$k]['id']);
		}
		$smarty->block_data[$dataname]=$data;
	}
	if(!empty($smarty->block_data[$dataname])){ 
		$repeat = true;
		$item = array_shift($smarty->block_data[$dataname]);
		$smarty->assign($as,$item);
	}else{
		$repeat = false;
	}
	if(!$item){
		unset($smarty->block_data[$dataname]);
		$repeat = false;
	}
	return $content;
}
?>