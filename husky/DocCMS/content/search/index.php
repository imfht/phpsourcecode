<?php
//首页搜索，站内关键字搜索
function index()
{
	global $db;
	global $request;
	global $params;
	global $tag;	// 标签数组

	!checkSqlStr($request['keyword'])? $request['keyword'] = $request['keyword'] : exit('非法字符');
	$keyword = urldecode($request['keyword']);
	
	if(empty($keyword))
	{
		echo '<script>alert("请输入您要查询的内容！");window.history.go(-1);</script>';
	}
	else
	{
		$catcheChannelInfo=array();
		$sql = 'SELECT id, menuName,level,isHidden,type FROM '.TB_PREFIX.'menu ';//published  level
		$rs= $db->get_results($sql);
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$catcheChannelInfo[$v->type][$v->id]=$v;//缓存 menuNameinfo
			}
		}
		unset($rs);
		/*******自定义搜索*******/
		$modelName=array();
		if(isset($request['search_in_module']) && !empty($request['search_in_module'])){
			if($request['search_in_module']=='all'){
				$sql = "SELECT distinct type  FROM ".TB_PREFIX."menu";
				$modelName = $db->get_results($sql);
			}else{
				$search_in_module=explode('|',$request['search_in_module']);
				foreach($search_in_module as $k=>$v){
					$modelName[$k]->type=$v;
				}
			}
		}else{//默认
			$default_search_in_module=array('product','article','list');
			foreach($default_search_in_module as $k=>$v){
				$modelName[$k]->type=$v;
			}
		}
		
		foreach($modelName as $mname)
		{
			switch($mname->type)
			{
				case 'article':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'content','type'=>$mname->type));
					break;
				case 'list':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'content','type'=>$mname->type));
					break;
				case 'product':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'content','smallPic'=>'smallPic','type'=>$mname->type));
					break;
				case 'download':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'content','type'=>$mname->type));
					break;	
				case 'picture':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'description','type'=>$mname->type));
					break;	
				case 'video':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'description','type'=>$mname->type));
					break;	
				case 'jobs':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'summary','type'=>$mname->type));
					break;
				case 'poll':
					$tag['data.results']=get_search_results($tag['data.results'], $catcheChannelInfo[$mname->type], $format=array('title'=>'title','content'=>'title','type'=>$mname->type));
					break;
				default: break;			
			}
			unset($catcheChannelInfo[$mname->type]);
		}
		unset($modelName);
		unset($catcheChannelInfo);
		$tag['pager.cn']=getPager($params['i'],10);
	}
}
function get_search_result($modelName)
{
	global $db,$request;
	!checkSqlStr($request['keyword'])? $request['keyword'] = $request['keyword'] : exit('非法字符');
	$keyword = urldecode($request['keyword']);
	switch($modelName)
	{
		case 'article':
			$sql = "SELECT * FROM ".TB_PREFIX."article WHERE title LIKE '%".$keyword."%' OR content LIKE '%".$keyword."%' ORDER BY id DESC";
			break;
		case 'list':
			$sql="SELECT * FROM ".TB_PREFIX."list WHERE  title LIKE '%".$keyword."%' OR content LIKE '%".$keyword."%' ORDER BY id DESC";
			break;
		case 'product':
			$sql="SELECT * FROM ".TB_PREFIX."product  WHERE title LIKE '%".$keyword."%' OR content LIKE '%".$keyword."%' ORDER BY id DESC";
			break;
		case 'download':
			$sql="SELECT * FROM ".TB_PREFIX."download WHERE title LIKE '%".$keyword."%' OR content LIKE '%".$keyword."%' ORDER BY id DESC";
			break;
		case 'picture':
			$sql="SELECT * FROM ".TB_PREFIX."picture WHERE title LIKE '%".$keyword."%' OR description LIKE '%".$keyword."%' ORDER BY id DESC";
			break;
		case 'video':
			$sql="SELECT * FROM ".TB_PREFIX."video WHERE title LIKE '%".$keyword."%' OR description LIKE '%".$keyword."%' ORDER BY id DESC";
			break;
		case 'jobs':
			$sql="SELECT *  FROM ".TB_PREFIX."jobs WHERE  lastTime>='".date('Y-m-d')."' AND (title LIKE '%".$keyword."%' OR content LIKE '%".$keyword."%')  ORDER BY id DESC";
			break;
		case 'poll':
			$sql="SELECT * FROM ".TB_PREFIX."poll_category  WHERE  title LIKE '%".$keyword."%'  ORDER BY id DESC";
			break;
		default: break;
	}
	return $db->get_results($sql);
}
function get_search_results($results=array(),$nmk =array(),$format=array('title'=>'title','content'=>'content','type'=>'article'))
{
	$searchResults = get_search_result($format['type']);
	if(!empty($searchResults))
	{
		$catcheChannelId=array();//缓存
		foreach($searchResults as $k=>$o)
		{	
			if(!array_key_exists($o->channelId,$catcheChannelId))
			{
				$tempChannelInfo=$tempCatcheChannelInfo[$o->channelId];			
				if(checkInfo($tempChannelInfo)){
					$catcheChannelId[$o->channelId]=true;				
				}else{
					$catcheChannelId[$o->channelId]=false;
				}
			}else{
				$tempChannelInfo=$tempCatcheChannelInfo[$o->channelId];	
			}
			if($catcheChannelId[$o->channelId]){
				if(URLREWRITE){
					$menuName=$tempChannelInfo->menuName;
				}else{
					$menuName='';
				}
				$results[]=array(
						'id'=>$o->id,
						'p'=>$o->channelId,
						'menuName'=>$menuName,
						'smallPic'=>$o->smallPic,
						'title'=>trimTags($o->$format['title']),
						'content'=>trimTags($o->$format['content']),
						'type'=>$format['type'],
						'dtTime'=>$o->dtTime
					);
			}			
		}
	}
	unset($tempCatcheChannelInfo);
	unset($searchResults);
	unset($catcheChannelId);
	unset($tempChannelInfo);
	return $results;
}
function checkInfo($tempChannelInfo)
{
	if($tempChannelInfo->isHidden){//检查信息的合法性
		return false;
	}elseif($tempChannelInfo->level){
		if($_SESSION[TB_PREFIX.'user_roleId']>=$tempChannelInfo->level){
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
}
//分页
function getPager($i=1,$itemCounts)
{
	global $tag,$request;
	
	if(intval($i)==0)
	$i = 1;
	$count = count($tag['data.results']);
	$intCount = intval($count/$itemCounts);
	$resCount = intval($count%$itemCounts);
	if($resCount!=0)
	$intCount = $intCount + 1;
	$encode_keyword=urlencode($_REQUEST['keyword']);
	if($intCount <= 1)
	{
		return '<table><tr><td valgin="bottom">'.$i.'/'.$intCount.'页  共'.$count.'条 
			 <a href="'.sys_href_home($encode_keyword,1).'"><span style="font-size: 12px; color: #F30;">首页</span></a>  
			 <a href="'.sys_href_home($encode_keyword,$intCount).'"><span style="font-size: 12px; color: #F30;">尾页</span></a>  
			 跳转至'.redPager($encode_keyword,$intCount,$_REQUEST['i']).'页</td></tr></table>';
	}
	else
	{
		return '<table><tr><td valgin="bottom">'.$i.'/'.$intCount.'页  共'.$count.'条  
			<a href="'.sys_href_home($encode_keyword,1).'"><span style="font-size: 12px; color: #F30;">首页</span></a> 
			 <a href="'.sys_href_previous($encode_keyword,$i,$intCount).'"><span style="font-size: 12px; color: #F30;">前一页</span></a>  
			 <a href="'.sys_href_next($encode_keyword,$i,$intCount).'"><span style="font-size: 12px; color: #F30;">后一页</span></a>  
			 <a href="'.sys_href_home($encode_keyword,$intCount).'"><span style="font-size: 12px; color: #F30;">尾页</span></a>  
			 跳转至'.redPager($encode_keyword,$intCount,$_REQUEST['i']).'页</td></tr></table>';
	}
}
function redPager($encode_keyword,$pagerCounts,$i)
{
	
	$temStr = '<select onChange="window.location=this.value">';
	for($j=1;$j<=$pagerCounts;$j++)
	{
		if(URLREWRITE)
		{
			if($j == intval($i))
			$temStr .= '<option selected="true" value="/search_'.$encode_keyword.'_'.$j.'.html">'.$j.'</option>';
			else
			$temStr .= '<option value="/search_'.$encode_keyword.'_'.$j.'.html">'.$j.'</option>';
		}else
		{
			if($j == intval($i))
			$temStr .= '<option selected="true" value="'.$tag['path.root'].'/?m=search&keyword='.$encode_keyword.'&i='.$j.'">'.$j.'</option>';
			else
			$temStr .= '<option value="'.$tag['path.root'].'/?m=search&keyword='.$encode_keyword.'&i='.$j.'">'.$j.'</option>';
		}
	}
	$temStr .= '</select>';
	return $temStr;
}
function sys_href_home($encode_keyword,$i=1)
{
	if(URLREWRITE)
    return '/search_'.$encode_keyword.'_'.$i.'.html';
    else
    return $tag['path.root'].'/?m=search&keyword='.$encode_keyword.'&i='.$i;
}
function sys_href_next($encode_keyword,$i,$maxi)
{
	if(URLREWRITE)
	{
		if(intval($i) < intval($maxi))
		return '/search_'.$encode_keyword.'_'.strval(intval($i)+1).'.html';
		else
		return '/search_'.$encode_keyword.'_'.strval(intval($maxi)).'.html';
	}
	else
	{
		if(intval($i) < intval($maxi))
		return $tag['path.root'].'/?m=search&keyword='.$encode_keyword.'&i='.strval(intval($i)+1);
		else
		return $tag['path.root'].'/?m=search&keyword='.$encode_keyword.'&i='.strval(intval($maxi));
	}
}
function sys_href_previous($encode_keyword,$i,$maxi)
{
	if(URLREWRITE)
	{
		if(intval($i) < intval($maxi))
		return '/search_'.$encode_keyword.'_'.strval(intval($i)-1).'.html';
		else
		return '/search_'.$encode_keyword.'_1.html';
	}
	else
	{
		if(intval($i) <= intval($maxi) && intval($i) > 1 )
		return $tag['path.root'].'/?m=search&keyword='.$encode_keyword.'&i='.strval(intval($i)-1);
		else
		return $tag['path.root'].'/?m=search&keyword='.$encode_keyword.'&i=1';
	}
}
?>