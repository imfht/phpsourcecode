<?php
/*
* 上下篇标签，支持多条调用
* $params	支持参数：
* table=''
* cid='栏目id'
* aid='id'
* type='pre,next'
*/
function smarty_block_prenext($params, $content, &$smarty, &$repeat){
	if(empty($params)){
		return false;
	}
	$params=array_map('trim',$params);
	extract($params);
	if(!isset($row) || !is_numeric($row)) $row=1;
	$as=(!isset($as) || $as=="") ? 'vo' : $as;
	$table=($table=="") ? 'article' : $table;
	if(!isset($aid)) $aid=isset($_GET['aid'])?intval($_GET['aid']):'';
	if(isset($id) && !isset($aid)) $aid=$id;

	if(empty($cid) || empty($aid) || empty($type)){
		if(!$repeat){
			return '缺少参数 cid 或 aid、type';
		}else{
			return false;
		}
	}
	$dataname = md5(__FUNCTION__ . md5(serialize($params)));
	$dataname = substr($dataname,0,16);

	if(!isset($smarty->block_data)) $smarty->block_data=array();
	//填充数据
	if(!isset($smarty->block_data[$dataname])){
		if($type=='pre'){
			$op='<';
			$orderby=array("id","DESC");
		}
		if($type=='next'){
			$op='>';
			$orderby=array("id","ASC");
		}
		$where=array("id{$op}{$aid}",'and','isshow=1');
		$data=DB($table)->where($where)->limit($row)->order($orderby)->select();
		$data = array_slice($data,0,$row);
		if($data){
			foreach($data as $k=>$vo){
				$data[$k]['url']=get_show_url($data[$k]['id']);
			}
		}else{
			if($repeat) echo '没有了';
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