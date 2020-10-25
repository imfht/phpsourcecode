<?php
function shl_poll($cateaoryId,$style=0)
{
	$return_check = label_check($checned = array('channelId' =>$cateaoryId,
								 'style'     =>$style,
								 'tbxstyle'  =>'poll',
								 ));
	if($return_check){echo $return_check;return;}
	global $db,$tag;
	$poll_cateaory=$db->get_row("SELECT * FROM `".TB_PREFIX."poll_category` WHERE id=".$cateaoryId,ARRAY_A);
	if(!empty($poll_cateaory))
	{
		$results=$db->get_results("SELECT * FROM `".TB_PREFIX."poll` WHERE  categoryId=".$cateaoryId,ARRAY_A);
		require(get_style_file('poll','poll',$style));	
	}
	else
	{
		echo '您传送的投票ID错误';
	}
}
function doc_poll($cateaoryId=0,$style=0)
{
	shl_poll($cateaoryId,$style);
}
?>