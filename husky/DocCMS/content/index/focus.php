<?php
/**
 * @copyright DOCCMS
 */
function shl_focus(  $group_id, $fromcount=0, $n=0, $ordering='id',
$opts=array( 'hastag'=>true,'hasstyle'=>array('tbxstyle'=>'focus','style'=>0),'fun'=>'doc_focus','isintercepted'=>array('strcount'=>0,'strcount1'=>0,'isellipsis'=>true) ) )
{
	$opts['fun'] = isset($opts['fun']) ? $opts['fun'] : 'doc_focus';
	/********************参数验证 start*******************/
	$return_check = label_check($checned = array('channelId' =>$group_id,
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
	/*********************参数验证 end*******************/
	global $db,$tag;
	$flash=array();
	if(intval($fromcount)>=1)$fromcount = intval($fromcount)-1;else $fromcount = 0;
	$sql='SELECT * FROM `'.TB_PREFIX.'flash_group` WHERE `type` = "focus" AND id='.$group_id;
	$flash['focus_group'] = $db->get_row($sql,ARRAY_A);
	if($flash['focus_group'])
	{
	    $sql="SELECT * FROM `".TB_PREFIX."flash` WHERE group_id =".$group_id." ORDER BY ".$ordering." DESC";
		if($n)$sql.=" LIMIT ".$fromcount.",".$n;
		
		$flash['results']= $db->get_results($sql);
	    if($flash['results'])
		{
			$hastag = isset($opts['hastag']) ? $opts['hastag'] : true;
			foreach($flash['results'] as $k=>$o)
			{
				$o->picpath = ispic($o->picpath);
				if(!$hastag)$o->description=trimTags($o->description);
				if(isset($opts['isintercepted']))
				{
					$strcount   = isset($opts['isintercepted']['strcount']) ? $opts['isintercepted']['strcount'] : 0;
					$strcount1  = isset($opts['isintercepted']['strcount1']) ? $opts['isintercepted']['strcount1'] : 0;
					$isellipsis = isset($opts['isintercepted']['isellipsis']) ? $opts['isintercepted']['isellipsis'] : true;
					$o->title	= sys_substr($o->title,$strcount,$isellipsis);
					$o->description	= sys_substr($o->description,$strcount1,$isellipsis);
				}
				//isfile($fileUrl)函数提供判断文件是否有效文件
				$data = $flash['focus_group'];
				$myfocus_js = isfile(get_abs_skin_root().'/res/plug-in/myfocus/myfocus-2.0.4.min.js' )
							? $tag['path.skin'].'res/plug-in/myfocus/myfocus-2.0.4.min.js' 
							: $tag['path.root'].'/inc/js/myfocus/myfocus-2.0.4.min.js';		
				$flash['results'][$k]=(array)$o;
			}
			if(isset($opts['hasstyle']))
			{
			    $tbxstyle = isset($opts['hasstyle']['tbxstyle']) ? $opts['hasstyle']['tbxstyle'] : 'focus';
				$style 	  = isset($opts['hasstyle']['style']) ? $opts['hasstyle']['style'] : 0;	
				require(get_style_file($tbxstyle,$tbxstyle,$style));
			}
			else
			return $flash;
		}else echo "<b style='color:#fff'>暂无图片。</b>";
	}else{ echo '暂无数据！';}
}
function doc_focus($group_id=0,$n=0,$style=0,$strcount=0,$strcount1=0,$isellipsis=true,$ordering='id',$fromcount=0)
{
	$opts=array(
		'hastag'=>false,
		'hasstyle'=>array('tbxstyle'=>'focus','style'=>$style),
		'fun'=>'doc_focus',
		'isintercepted'=>array('strcount'=>$strcount,'strcount1'=>$strcount1,'isellipsis'=>$isellipsis)
	);
	shl_focus($group_id, $fromcount, $n, $ordering, $opts);
}

function doc_flash($width='300',$height='300',$url='indexflash/main.swf',$style=0)
{
	global $db,$tag;
	require(get_style_file('flash','flash',$style));
}