<?php
function index()
{
	global $db;
	global $params;
	global $tag;
	$sql="SELECT * FROM ".TB_PREFIX."mapshow WHERE channelId=".$params['id'];
	$sb = new sqlbuilder('mdt',$sql,'id ASC',$db,1,true,URLREWRITE?'/':'./');
	if(!empty($sb->results))
	{
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();	
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
}
?>