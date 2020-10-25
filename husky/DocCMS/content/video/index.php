<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组
	$sql="SELECT * FROM `".TB_PREFIX."video` WHERE channelId=".$params['id'];
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,videoCount,true,URLREWRITE?'/':'./');
	if(!empty($sb->results))
	{
		foreach($sb->results as $k =>$v)
	    {
		    if(!empty($v['filePath'])) $sb->results[$k]['filePath']=$tag['path.root'].$v['filePath'];
			$sb->results[$k]['picture']	= ispic($v['picture']);
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
function view()
{
	global $db;
	global $params;
	global $tag;
	$sql='SELECT * FROM '.TB_PREFIX.'video WHERE id='.$params['args'];
	$video = $db->get_row($sql);
	if(!empty($video->filePath)) $video->filePath=$tag['path.root'].$video->filePath;
	$video->picture =ispic($video->picture);
	
	$tag['data.row']=(array)$video;
	unset($video);
}
?>