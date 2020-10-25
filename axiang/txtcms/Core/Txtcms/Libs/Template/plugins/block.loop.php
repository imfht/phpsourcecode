<?php
/*
* 万能标签
* $params	
支持参数 ：
table='表名'					必填
row='调用数量' 
float='包含属性' 
nofloat='不包含属性'
as='别名'
id='栏目id'
cid='同左边'
orderby='排序'
image='包含图片'
*/
function smarty_block_loop($params, $content, &$smarty, &$repeat){
	if(empty($params)){
		$params['cid']=$params['id']=0;
	}
	$params=array_map('trim',$params);
	$dataname = md5(__FUNCTION__ . md5(serialize($params)));
	$dataname = substr($dataname,0,16);
	extract($params);
	$as=(!isset($as) || $as=="") ? 'vo' : $as;
	if(!isset($row) || !is_numeric($row)) $row = 20;
	if (!isset($id)) $id=isset($cid)?intval($cid):'0';

	if(!$table){
		if(!$repeat){
			return '未指定或不存在table';
		}else{
			return false;
		}
	}
	//创建一个orderby的临时变量
	$temp_order_arr = $where = array();
	//初始化随机调用
	$orderByRand = false;
	//处理orderby排序
	if (isset($orderby)) {
		//是否随机
		if ($orderby== 'rand') {
			$orderByRand = true;
		}
	} 
	if(!isset($smarty->block_data)) $smarty->block_data=array();
	//填充数据
	if(!isset($smarty->block_data[$dataname])){
		//初始化$where，为下面的and合并做准备
		if($table=='article') $where = array('isshow=1');
		//支持关键词
		if(isset($keyword)) {
			$where[] = 'and';
			$where[] = "title =~ %{$keyword}%";
		}
		//判断包含属性值，支持多个属性，使用逗号,隔开
		if (isset($flag)) {
			$flag_arr=explode(',',$flag);
			foreach($flag_arr as $k=>$vo){
				if($vo=='') continue;
				$where[] = 'and';
				$where[] = "flag =~ %{$vo}%";
			}
		}
		//判断不包含属性值，支持多个属性，使用逗号,隔开
		if (isset($noflag)) {
			$noflag_arr=explode(',',$noflag);
			foreach($noflag_arr as $k=>$vo){
				if($vo=='') continue;
				$where[] = 'and';
				$where[] = "flag !~ %{$vo}%";
			}	
		}
		//栏目
		if($id) {
			$where[] = 'and';
			$where[] = "cid={$id}";
		}
		//判断是否有图
		if($image<>'' ){
			$image=intval($image);
			$where[] = 'and';
			if($image){
				$where[]='!empty(litpic)';
			}else{
				$where[]='empty(litpic)';
			}
		}
		//查询数据库
		$data=DB($table)->where($where)->order($orderby)->select();
		if(!$data) return false;
		$guolv = count($data);
		//随机就打乱
		if($orderByRand) shuffle($data);
		//先打乱后删减
		$data = array_slice($data,0,$row);
		foreach($data as $k=>$vo){
			if($table=='article'){
				$arctype=DB('arctype')->where('id='.$data[$k]['cid'])->find();
				$data[$k]['curl']=get_list_url($data[$k]['cid']);
				$data[$k]['cname']=$arctype['cname'];
				$data[$k]['url']=get_show_url($data[$k]['id']);
				if($data[$k]['flag']<>''){
					$flags=explode(',',$data[$k]['flag']);
					$flag=array();
					foreach($flags as $kk=>$vv){
						$flagresult=DB('arcflag')->where('en='.$vv)->find();
						$flag[]=array('cn'=>$flagresult['cn'],'en'=>$vv);
					}
					$data[$k]['flag']=$flag;
				}
			}
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