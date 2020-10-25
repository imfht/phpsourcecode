<?php
/*
* 列表标签
* $params	支持参数 table='表名' row='调用数量'	as='别名'	id='上级栏目id' orderby='排序' flag='' noflag='' image='' keyword='' nop=''
*/
function smarty_function_list($params, &$smarty) {
	if (empty($params)) $params['cid'] = 0;
	$params=array_map('trim',$params);	//过滤前后空白
	extract($params);
	$limit = isset($row)?$row:20;	//调用条数
	$p = isset($_GET['p'])?intval($_GET['p']):'1';	//分页
	$as = isset($as)?$as:'list';	//别名
	$table ='article';	//表名
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
	//获取栏目ID
	$cid = !isset($id) ? intval($_GET['id']) : $id;
	//初始化$where，为下面的and合并做准备
	$where = array('isshow=1');
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
	if($cid) {
		$where[] = 'and';
		$arctype_rows=DB('arctype')->order('order desc')->select();
		$cids=array($cid);
		import('Class/tree');
		$tree=new Tree($arctype_rows);
		$arr=$tree->leaf($cid);
		if($arr){
			$cids=array_merge($cids,getSonCid($arr));
			$where[] = "cid IN ".implode(',',$cids);
		}else{
			$where[] = "cid=".$cid;
		}
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
	//判断是否随分页而改动
	$startp = isset($nop) ? 0 : ($p-1) * $limit;
	//查询数据库
	$data=DB($table)->where($where)->order($orderby)->select();
	if($data){
		$guolv = count($data);
		//随机就打乱
		if($orderByRand) shuffle($data);
		//先打乱后删减
		$data = array_slice($data, $startp, $limit);
		//循环处理开始
		foreach($data as $k => $vo) {
			$arctype=DB('arctype')->where('id='.$data[$k]['cid'])->find();
			$data[$k]['curl']=get_list_url($data[$k]['cid']);
			$data[$k]['cname']=$arctype['cname'];
			//处理url
			$data[$k]['url']=get_show_url($data[$k]['id']);
			//这里主要用于搜索页 ishight=高亮关键词
			if (isset($keyword) && isset($ishight) && $ishight == 1) {
				$data[$k]['title'] = str_ireplace($keyword, '<span class=scolor>' . $keyword . '</span>', $data[$k]['title']);
			}
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
		//计算分页
		$totalpages = ceil($guolv / $limit);
		if ($p>$totalpages) {
			$p=$totalpages;
		}
		if (isset($keyword)) {
			$pages = get_page_css($p, $totalpages, 4, url('Article/lists?q='.$keyword.'&p=!page!'), false);
		} else {
			$pages = get_page_css($p, $totalpages, 4, get_list_url($cid,'!page!'), false);
		}
		$smarty -> assign('pages', $pages);
		$smarty -> assign('count', $guolv);
		$smarty -> assign($as, $data);
	}
}