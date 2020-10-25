<?php
/**
 * @author：grysoft QQ:767912290
 * @copyright DOCCMS
 */
function product_index_category($id=0){
  global $menus,$subs;  

  if(!isset($subs[$id])) return ; //没有子类,返回空;
  		   
   foreach($subs[$id] as $sid){  
	  $result.=','.$menus[$sid]['id'].product_index_category($sid);  //递归	
   } 
   return $result;
}
/**
 * @copyright DOCCMS
 */
function shl_product($channelId, $fromcount=0, $n=0, $ordering='id',
$opts=array('hastag'=>true,'hasstyle'=>array('tbxstyle'=>'product','style'=>0),'fun'=>'doc_product','isintercepted'=>array('strcount'=>0,'strcount1'=>0,'strcount2'=>0,'isellipsis'=>true)
	,'sqlotherwhere'=>''))
{
	$opts['fun'] = isset($opts['fun']) ? $opts['fun'] : 'doc_product';
	/********************参数验证 start*******************/
	$return_check = label_check($checned = array('channelId' =>$channelId,
								 'n'		 =>$n,
								 'style'     =>$opts['hasstyle']['style'],
								 'strcount'  =>$opts['isintercepted']['strcount'],
								 'strcount1' =>$opts['isintercepted']['strcount1'],
								 'strcount2' =>$opts['isintercepted']['strcount2'],
								 'isellipsis'=>$opts['isintercepted']['isellipsis'],
								 'hastag'	 =>$opts['hastag'],
								 'ordering'  =>$ordering,
								 'fromcount' =>$fromcount,
								 'tbxstyle'  =>$opts['hasstyle']['tbxstyle'],
								 ));
	if($return_check){echo $return_check;return;}
			
	$channelId=explode('|',$channelId);
	for($i=0;$i<count($channelId);$i++)
	{
		$categoryId .= $channelId[$i].product_index_category($channelId[$i]).',';
	}
    $categoryId=chop($categoryId,',');
	
	//过滤模块类型非指定的调用id
	$channelId=explode(',',$categoryId);
	for($i=0;$i<count($channelId);$i++)
	{
		$channelId[$i] = sys_menu_info('type',false,$channelId[$i])==$opts['hasstyle']['tbxstyle']?$channelId[$i]:'';
	}
	$channelId=chop(implode(',',array_filter($channelId)),',');
	if(!$channelId){echo 'parameters $channelId is not allowed in '.$opts['fun'].'()!' ;return;}
	/*********************参数验证 end*******************/

	global $db,$tag;
	
	if(intval($fromcount)>=1)$fromcount = intval($fromcount)-1;else $fromcount = 0;
	$sql="SELECT * FROM `".TB_PREFIX."product` WHERE `channelId` IN(".$categoryId.") ".$opts['sqlotherwhere']." ORDER BY ".$ordering." DESC";
	if($n)$sql.=" LIMIT ".$fromcount.",".$n;

	$results = $db->get_results($sql);
	if($results)
	{
		$hastag = isset($opts['hastag']) ? $opts['hastag'] : true;
		foreach($results as $o)
		{
			$o->indexPic = ispic($o->indexPic);
			$o->originalPic  = ispic($o->originalPic);
			$o->middlePic    = ispic($o->middlePic);
			$o->smallPic     = ispic($o->smallPic);
			
			if(!$opts['hastag'])$o->content=trimTags($o->content);
			if(isset($opts['isintercepted']))
			{
				$strcount = isset($opts['isintercepted']['strcount']) ? $opts['isintercepted']['strcount'] : 0;
				$strcount1 = isset($opts['isintercepted']['strcount1']) ? $opts['isintercepted']['strcount1'] : 0;
				$strcount2 = isset($opts['isintercepted']['strcount2']) ? $opts['isintercepted']['strcount2'] : 0;
				$isellipsis = isset($opts['isintercepted']['isellipsis']) ? $opts['isintercepted']['isellipsis'] : true;
				
				$o->title		= sys_substr($o->title,$strcount,$isellipsis);
				$o->description	= sys_substr($o->description,$strcount1,$isellipsis);
				$o->content		= sys_substr($o->content,$strcount2,$isellipsis);
			}
		}
		if(isset($opts['hasstyle']))
		{
			$tbxstyle = isset($opts['hasstyle']['tbxstyle']) ? $opts['hasstyle']['tbxstyle'] : 'product';
			$style 	  = isset($opts['hasstyle']['style']) ? $opts['hasstyle']['style'] : 0;	
			$i=0;
			foreach($results as $o)
			{
				$data=(array)$o;
				require(get_style_file($tbxstyle,$tbxstyle,$style));
				$i++;
			}
		}
		else
		return $results;
	}else{ echo '暂无数据！';}
}

function doc_product($channelId=0,$n=0,$style=0,$strcount=0,$strcount1=0,$strcount2=0,$isellipsis=true,$hastag=false,$ordering='id',$fromcount=0)
{
	$opts=array(
		'hastag'=>$hastag,
		'hasstyle'=>array('tbxstyle'=>'product','style'=>$style),
		'fun'=>'doc_product',
		'isintercepted'=>array('strcount'=>$strcount,'strcount1'=>$strcount1,'strcount2'=>$strcount2,'isellipsis'=>$isellipsis),
		'sqlotherwhere'=>''
	);
	shl_product($channelId, $fromcount, $n, $ordering, $opts);
}