<?php
function index()
{
	global $db;
	global $params;
	global $tag;
	$sql="SELECT * FROM `".TB_PREFIX."linkers` WHERE channelId=".$params['id'];
	$tag['data.results']= get_linker_results(new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,100));
}
function text()
{
	global $db;
	global $params;
	global $tag;
	$sql="SELECT * FROM `".TB_PREFIX."linkers` WHERE links=1 AND channelId=".$params['id'];
	$tag['data.results']= get_linker_results(new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,100));
}
function images()
{
	global $db;
	global $params;
	global $tag;
	$sql="SELECT * FROM `".TB_PREFIX."linkers` WHERE links=0 AND channelId=".$params['id'];
	$tag['data.results']= get_linker_results(new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,100));
}
function view()
{
	global $db,$params;
	if(!empty($params['args']))
	{
		$sql="SELECT * FROM ".TB_PREFIX."linkers WHERE id=".$params['args'];
		$link = $db->get_row($sql);
		redirect($link->linkAddress);
	}else{
		exit;
	}
}
/*辅助函数*/
function get_linker_results($sb)
{
	global $tag;
	if(!is_object($sb))return 'Forbidden';
	$temp=array(); 
	if(!empty($sb->results))
	{
		foreach($sb->results  as $k =>$v)
		{
		    $sb->results[$k]['originalPic'] = ispic($v['originalPic']);
			$sb->results[$k]['smallPic']	= ispic($v['smallPic']);
		} 
		$temp=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
	return $temp;
}
?>