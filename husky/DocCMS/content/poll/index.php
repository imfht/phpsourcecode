<?php 
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组
	$sql="SELECT * FROM ".TB_PREFIX."poll_category WHERE channelId=".$params['id'];
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,productCount,true,URLREWRITE?'/':'./');
	if(!empty($sb->results))
	{
		$tag['data.results']=$sb->results;
		foreach($sb->results as $k=>$v)
		{
			$sql="SELECT * FROM ".TB_PREFIX."poll WHERE categoryId=".$v['id']." ORDER BY ordering DESC";
			$sb_children=$db->get_results($sql,ARRAY_A);
			if($sb_children)
			{
				$tag['data.results'][$k]['children']=$sb_children;	// 挂载对应子数据数组
			}
		}
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
}
function send()
{
	global $db;
	global $request;
	global $params;
	global $tag;	// 标签数组
	if(!empty($request['choice']))
	{
		$sql="SELECT * FROM ".TB_PREFIX."poll_category WHERE id=".$params['args'];
		$poll_client=$db->get_row($sql);

		$cur_ip=getip();
		if(empty($poll_client->client_ip))
		{
			$insert_ip=$cur_ip;
		}
		else
		{
			$checkIP=explode(';',$poll_client->client_ip);
			if(in_array($cur_ip,$checkIP))
			{
				echo "<script language='javascript'>alert('您已经投过票了！');window.history.go(-1);</script>";
				exit;
			}
			array_push($checkIP,$cur_ip);
			$insert_ip=implode(';',$checkIP);
		}

		if ($poll_client->choice=='a')
		{	
			$db->query("UPDATE ".TB_PREFIX."poll SET num=num+1 WHERE id=".$request['choice']);
			
			$db->query("UPDATE ".TB_PREFIX."poll_category SET client_ip='".$insert_ip."' WHERE id=".$params['args']);
			
			echo '<script>alert("投票成功！");window.location.href="'.sys_href($params['id'],'poll',$params['args']).'";</script>';
		}
		elseif ($poll_client->choice=='b')
		{
			for ($i=0;$i<count($request['choice']);$i++)
			{
			 $db->query("UPDATE ".TB_PREFIX."poll SET num=num+1 WHERE id=".$request['choice'][$i]);
			}
			$db->query("UPDATE ".TB_PREFIX."poll_category SET client_ip='".$insert_ip."' WHERE id=".$params['args']);
			
			echo '<script>alert("投票成功！");window.location.href="'.sys_href($params['id'],'poll',$params['args']).'";</script>';
		}
	}
	else if(empty($request['choice']))
	{
		echo "<script language='javascript'>alert('您没有添加选项！');window.history.go(-1);</script>";
		exit;
	}
}
function view()
{
	global $db;
	global $params;
	global $tag;
	$sql='SELECT * FROM '.TB_PREFIX.'poll_category WHERE id='.$params['args'];
	$tag['data.row'] = $db->get_row($sql,ARRAY_A);
	$sql = "SELECT * FROM ".TB_PREFIX."poll WHERE categoryId=".$params['args'].' ORDER BY ordering DESC';
	$tag['data.results']=$db->get_results($sql,ARRAY_A);
	//特殊处理
	$tag['seo.title']		    = $tag['data.row']['title'].$tag['title'];
	$tag['seo.keywords'] 	    = $tag['data.row']['title'].$tag['keywords'];
	$tag['seo.description']     = $tag['data.row']['title'].$tag['description'];
}
?>