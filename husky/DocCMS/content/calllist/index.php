<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组

	$calllist = $db->get_row('SELECT * FROM `'.TB_PREFIX.'calllist` WHERE channelId='.$params['id'],ARRAY_A);
	if(!empty($calllist['callId']))
	{
		$catcheChannelInfo=array();
		$sql = "SELECT id,title, menuName,level,isHidden FROM ".TB_PREFIX."menu WHERE id IN (".$calllist['callId'].")";
		$rs= $db->get_results($sql);
		if(!empty($rs))
		{
			foreach($rs as $k=>$v)
			{
				$catcheChannelInfo[$v->id]=$v;
			}
		}
		unset($rs);
		$calllists=explode(',',$calllist['callId']);
		foreach ($calllists as $k=>$v)
		{
			$sql='SELECT * FROM `'.TB_PREFIX.'list` WHERE channelId='.$v.' ORDER BY ordering DESC,id DESC LIMIT '.calllistCount;
			$results=$db->get_results($sql, ARRAY_A);
			if(!empty($results))
			{
				$tag['data.results'][$v]['channelId'] = $v;
				$tag['data.results'][$v]['channel']   = $catcheChannelInfo[$v]->title;
				$tag['data.results'][$v]['menuName']  = $catcheChannelInfo[$v]->menuName;
				$tag['data.results'][$v]['results']   = $results;
			}
		}	
	}
	unset($calllist);
	unset($catcheChannelInfo);
	unset($results);
}
?>