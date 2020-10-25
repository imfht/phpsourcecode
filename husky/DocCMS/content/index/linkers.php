<?php
/**
 * @copyright DOCCMS
 */
function shl_linkers($channelId, $fromcount=0, $n=0,$ordering='id',
$opts=array('hastag'=>true,'hasstyle'=>array('tbxstyle'=>'linkers','style'=>0),'fun'=>'doc_linkers','isintercepted'=>array('strcount'=>0,'strcount1'=>0,'isellipsis'=>true)
	,'sqlotherwhere'=>''))
{
	$opts['fun'] = isset($opts['fun']) ? $opts['fun'] : 'doc_linkers';
	/********************参数验证 start*******************/
	$return_check = label_check($checned = array('channelId' =>$channelId,
								 'n'		 =>$n,
								 'style'     =>$opts['hasstyle']['style'],
								 'strcount'  =>$opts['isintercepted']['strcount'],
								 'strcount1' =>$opts['isintercepted']['strcount1'],
								 'isellipsis'=>$opts['isintercepted']['isellipsis'],
								 'ordering'  =>$ordering,
								 'fromcount' =>$fromcount,
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
	if(intval($fromcount)>=1)$fromcount = intval($fromcount)-1;else $fromcount = 0;
	$sql="SELECT * FROM `".TB_PREFIX."linkers` WHERE channelId IN(".$channelId.") ".$opts['sqlotherwhere']." ORDER BY ".$ordering." DESC";
	if($n)$sql.=" LIMIT ".$fromcount.",".$n;
	$results = $db->get_results($sql);
	if($results)
	{
		$hastag = isset($opts['hastag']) ? $opts['hastag'] : true;
		foreach($results as $o)
		{
			$o->originalPic = ispic($o->originalPic);
			$o->smallPic    = ispic($o->smallPic);
			if(!$hastag)$o->description=trimTags($o->description);
			if(isset($opts['isintercepted']))
			{
				$strcount = isset($opts['isintercepted']['strcount']) ? $opts['isintercepted']['strcount'] : 0;
				$strcount1 = isset($opts['isintercepted']['strcount1']) ? $opts['isintercepted']['strcount1'] : 0;
				$isellipsis = isset($opts['isintercepted']['isellipsis']) ? $opts['isintercepted']['isellipsis'] : true;
				
				$o->title	    = sys_substr($o->title,$strcount,$isellipsis);
				$o->description	= sys_substr($o->description,$strcount1,$isellipsis);
			}
		}
		if(isset($opts['hasstyle']))
		{
			$tbxstyle = isset($opts['hasstyle']['tbxstyle']) ? $opts['hasstyle']['tbxstyle'] : 'linkers';
			$style 	  = isset($opts['hasstyle']['style']) ? $opts['hasstyle']['style'] : 0;	
			$i=0;
			foreach($results as $o)
			{
				$data=(array)$o;
				require(get_style_file($tbxstyle,$tbxstyle,$style));
				$i++;
			}
		}else
		return $results;
	}else{ echo '暂无数据！';}
}
function doc_linkers($channelId=0,$n=0,$style=1,$strcount=0,$strcount1=0,$linktype=1,$isellipsis=false,$ordering='id',$fromcount=0)
{
	$linktype = $linktype?1:0;
	$opts=array(
		'hastag'=>false,
		'hasstyle'=>array('tbxstyle'=>'linkers','style'=>$style),
		'fun'=>'doc_image_linkers',
		'isintercepted'=>array('strcount'=>$strcount,'strcount1'=>$strcount1,'isellipsis'=>$isellipsis,'hastag'=>$hastag),
		'sqlotherwhere'=>' AND links= '.$linktype
	);
	shl_linkers($channelId, $fromcount, $n, $ordering, $opts);
}