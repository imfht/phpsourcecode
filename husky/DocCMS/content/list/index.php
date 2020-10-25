<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组

	$sql="SELECT * FROM `".TB_PREFIX."list` WHERE channelId=".$params['id']."";
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,listCount,true,URLREWRITE?'/':'./');
	if(!empty($sb->results))
	{
		foreach($sb->results as $k =>$v)
	    {
	    	if(!empty($v['originalPic']))
	    	{
		    	$sb->results[$k]['originalPic'] = ispic($v['originalPic']);
				$sb->results[$k]['indexPic']	= ispic($v['indexPic']);
	    	}
			if(!empty($v['style']))
			{
				$style = explode('@',$v['style']);
				$sb->results[$k]['style'] ='style="color:#'.$style[0].';font-weight:'.$style[1].';font-style:'.$style[2].'"';
			}
	    }
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
}
function getcontent($p,$r,$page,$data){
   if($data->hassplitpages==1){
   	   if($page==0)$page=1;
	   $article=explode('{#page#}',$data->content);
	   $pagenum=count($article);
	   if($pagenum<$page){
	   	 $result['navbar']='';
         $result['content']='超出分页范围,非法请求！';
	   }else{
	       $result['content']=$article[$page-1];
	       $result['navbar']=pages_nav($pagenum,$p,$r,$page);	//分页导航
	   }
   }else{
        $result['navbar']='';
        $result['content']=$data->content;
   }
    return $result;
} 
function view()
{
	global $db;
	global $params;
	global $tag;
	$sql="UPDATE ".TB_PREFIX."list SET counts=counts+1 WHERE id=".$params['args'];
	$db->query($sql);
	$sql="SELECT * FROM ".TB_PREFIX."list WHERE id=".$params['args'];
	$list = $db->get_row($sql);
	if(!empty($list->originalPic))
	{	
		$list->originalPic=ispic($list->originalPic);
		$list->indexPic	  =ispic($list->indexPic);
	}
	
	$result=getcontent($params['id'],$params['args'],$params['cid'],$list);
	$list->content = $result['content']; 
	$list->navbar = $result['navbar'];

	if(!empty($list->description))$list->description = '<div class="details"><h2>新闻摘要:'.$list->description.'</h2></div>';
	if(empty($list->source))$list->source=SITENAME;
	if(empty($list->sourceUrl))$list->sourceUrl='http://'.$_SERVER['SERVER_NAME'];
	
	$tag['data.row']=(array)$list;
	unset($list);
	$tag['pager.data.up']=$db->get_row('SELECT * FROM '.TB_PREFIX.'list WHERE channelId='.$params['id'].' && id<'.$params['args'].' ORDER BY id DESC LIMIT 0,1',ARRAY_A);
	$tag['pager.data.down']=$db->get_row('SELECT * FROM '.TB_PREFIX.'list WHERE channelId='.$params['id'].' && id>'.$params['args'].' ORDER BY id ASC  LIMIT 0,1',ARRAY_A);
}
function pages_nav($pagenum,$p,$r,$cpage)
{
	global $tag;
	
	if($cpage==0)$cpage=1;
	
	if(URLREWRITE)
	{
		if($cpage==1)
		$navbar.='<span class="s1 s3">上一页</span>';
		else
		$navbar.='<a href="/'.$tag['channel.menuname'].'/n'.$r.'.html/'.(intval($cpage)-1).'" target="_self" class="s1">上一页</a>';
	}
	else
	{
		if($cpage==1)
		$navbar.='<span class="s1 s3">上一页</span>';
		else
		$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.(intval($cpage)-1).'" target="_self" class="s1">上一页</a>';
	}
	for($c=1;$c<=$pagenum;$c++)
	{
		if(URLREWRITE)
		{
			if($c == $cpage)
			$navbar.='<a href="/'.$tag['channel.menuname'].'/n'.$r.'.html/'.$c.'" target="_self" class="s2">'.$c.'</a>';
			else
			$navbar.='<a href="/'.$tag['channel.menuname'].'/n'.$r.'.html/'.$c.'">'.$c.'</a>';
		}
		 else
		{
			if($c == $cpage)
			$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.$c.'" target="_self" class="s2">'.$c.'</a>';
			else
			$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.$c.'">'.$c.'</a>';
		}
	}
	if(URLREWRITE)
	{
		if($cpage==$pagenum)
		$navbar.='<span class="s1 s3">下一页</span>';
		else
		$navbar.='<a href="/'.$tag['channel.menuname'].'/n'.$r.'.html/'.(intval($cpage)+1).'" target="_self" class="s1">下一页</a>';
	}
	else
	{
		if($cpage==$pagenum)
		$navbar.='<span class="s1 s3">下一页</span>';
		else
		$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.(intval($cpage)+1).'" target="_self" class="s1">下一页</a>';
	}
	return $navbar;
}
?>