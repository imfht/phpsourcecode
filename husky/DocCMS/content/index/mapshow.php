<?php
function shl_mapshow(  $channelId, 
$opts=array( 'hastag'=>true,'hasstyle'=>array('tbxstyle'=>'mapshow','style'=>0),'fun'=>'doc_mapshow','isintercepted'=>array('strcount'=>0,'strcount1'=>0,'isellipsis'=>true) ) )
{
	$opts['fun'] = isset($opts['fun']) ? $opts['fun'] : 'doc_mapshow';
	/********************参数验证 start*******************/
	$return_check = label_check($checned = array('channelId' =>$channelId,
								 'style'     =>$opts['hasstyle']['style'],
								 'strcount'  =>$opts['isintercepted']['strcount'],
								 'strcount1' =>$opts['isintercepted']['strcount1'],
								 'isellipsis'=>$opts['isintercepted']['isellipsis'],
								 'hastag'	 =>$opts['hastag'],
								 'tbxstyle'  =>$opts['hasstyle']['tbxstyle'],
								 ));
	if($return_check){echo $return_check;return;}
			
	$channelId=explode('|',$channelId);
	for($i=0;$i<count($channelId);$i++)
	{
		$channelId[$i] = sys_menu_info('type',false,$channelId[$i])==$opts['hasstyle']['tbxstyle']?$channelId[$i]:'';
	}
	$channelId=chop(implode(',',array_filter($channelId)),',');
	if(!$channelId){echo 'parameters $channelId is not allowed in '.$opts['fun'].'()!' ;return;}
	/*********************参数验证 end*******************/
	global $db,$tag;
	$sql = "SELECT * FROM `".TB_PREFIX."mapshow` WHERE channelId IN (".$channelId.")  LIMIT 1";
	$results=$db->get_results($sql);
	if($results)
	{
		foreach($results as $o)
		{
			$o->originalPic = ispic($o->originalPic);
			$o->smallPic    = ispic($o->smallPic);
			
			if(!$opts['hastag'])$o->content=trimTags($o->content);
			if(isset($opts['isintercepted']))
			{
				$strcount = isset($opts['isintercepted']['strcount']) ? $opts['isintercepted']['strcount'] : 0;
				$strcount1 = isset($opts['isintercepted']['strcount1']) ? $opts['isintercepted']['strcount1'] : 0;
				$isellipsis = isset($opts['isintercepted']['isellipsis']) ? $opts['isintercepted']['isellipsis'] : true;
				
				$o->title	    = sys_substr($o->title,$strcount,$isellipsis);
				$o->content		= sys_substr($o->content,$strcount1,$isellipsis);
			}
		}
		if(isset($opts['hasstyle']))
		{
			$tbxstyle = isset($opts['hasstyle']['tbxstyle']) ? $opts['hasstyle']['tbxstyle'] : 'article';
			$style 	  = isset($opts['hasstyle']['style']) ? $opts['hasstyle']['style'] : 0;	
			$i=0;
			foreach($results as $o)
			{
				$data=(array)$o;
				require(get_style_file($tbxstyle,$tbxstyle,$style));
				$i++;
			}
		}else
			return 	$o;	
	}else{ echo '暂无数据！';}
}
function doc_mapshow($channelId=0,$style=0,$strcount=0,$strcount1=0,$isellipsis=true,$hastag=false)
{
	$opts=array( 
		'hastag'=>$hastag,
		'hasstyle'=>array('tbxstyle'=>'mapshow','style'=>$style),
		'fun'=>'doc_mapshow',
		'isintercepted'=>array('strcount'=>$strcount,'strcount1'=>$strcount1,'isellipsis'=>$isellipsis),
		'sqlotherwhere'=>''
	);
	$o=shl_mapshow(  $channelId, $opts );
}