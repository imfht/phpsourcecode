<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组
	
	$sql="SELECT * FROM ".TB_PREFIX."article WHERE channelId=".$params['id'];
	$sb = new sqlbuilder('mdt',$sql,'pageId ASC',$db,1,true,URLREWRITE ? '/' : './');
	if(!empty($sb->results))
	{
		foreach($sb->results as $k =>$v)
	    {
			$sb->results[$k]['originalPic'] = ispic($v['originalPic']);
			$sb->results[$k]['indexPic']	= ispic($v['indexPic']);
			if(!empty($sb->results[$k]['description']))$sb->results[$k]['description'] = '<div class="details"><h2>文章摘要:'.$sb->results[$k]['description'].'</h2></div>';
	    }
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	
    //页面点击统计
	if($sb->results[0]['id'])
	{
		$sql='UPDATE '.TB_PREFIX.'article SET counts=counts+1 WHERE id='.$sb->results[0]['id'];
		$db->query($sql);
	}
	$sb=null;
}
?>