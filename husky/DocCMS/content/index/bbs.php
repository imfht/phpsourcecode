<?php
//获取pw和dz论坛新帖
function doc_bbs_phpwind($n=20,$strcount=0,$categoryId=0,$disable_category='',$bbsRootUrl='',$db_tb_prefix='pw_',$db_user=DB_USER,$db_password=DB_PASSWORD,$db_dbname=DB_DBNAME,$db_hostname=DB_HOSTNAME,$style=0)
{
	if(!is_int($n))return ('$n is not integer!');
	if(!is_int($strcount))return ('$strcount is not integer!');
	if(!is_int($categoryId))return ('$categoryId is not integer!');
	
	$disable_category=get_str($disable_category);
	$bbsRootUrl=get_str($bbsRootUrl);
	$db_tb_prefix=get_str($db_tb_prefix);
	
	$newsStr = null;
	$mydb = new dtdb($db_user,$db_password,$db_dbname,$db_hostname);
	if(intval($categoryId)>0)
	$sql = "SELECT  * FROM ".$db_tb_prefix."threads WHERE fid=".$categoryId." ORDER BY tid DESC LIMIT ".$n;
	elseif(intval($categoryId)==0 && !empty($disable_category))
	$sql = "SELECT  * FROM ".$db_tb_prefix."threads WHERE fid NOT IN (".$disable_category.") ORDER BY tid DESC LIMIT ".$n;
	else
	$sql = "SELECT  * FROM ".$db_tb_prefix."threads ORDER BY tid DESC LIMIT ".$n;
	
	$news = $mydb->get_results($sql);
	if(!empty($news))
	{
		foreach ($news as $o)
		{
			if(intval($strcount)>0 && cnStrLen($o->subject)>$strcount)
			$o->subject = cnSubstr($o->subject,0,$strcount).'..';			
			$data=(array)$o;	
			require(get_style_file('bbs','bbs_phpwind',$style));
		}						
	}else{ echo '暂无数据！';}	
}
function doc_bbs_discuz($n=20,$strcount=0,$categoryId=0,$disable_category='',$bbsRootUrl='',$db_tb_prefix='cdb_',$db_user=DB_USER,$db_password=DB_PASSWORD,$db_dbname=DB_DBNAME,$db_hostname=DB_HOSTNAME,$style=0)
{
	if(!is_int($n))return ('$n is not integer!');
	if(!is_int($strcount))return ('$strcount is not integer!');
	if(!is_int($categoryId))return ('$categoryId is not integer!');
	
	$disable_category=get_str($disable_category);
	$bbsRootUrl=get_str($bbsRootUrl);
	$db_tb_prefix=get_str($db_tb_prefix);
	
	$newsStr = null;
	$mydb = new dtdb($db_user,$db_password,$db_dbname,$db_hostname);
	
	if(intval($categoryId)>0)
	$sql = "SELECT  * FROM ".$db_tb_prefix."forum_post WHERE fid=".$categoryId." AND first = 1 ORDER BY tid DESC LIMIT ".$n;
	elseif(intval($categoryId)==0 && !empty($disable_category))
	$sql = "SELECT  * FROM ".$db_tb_prefix."forum_post WHERE fid NOT IN (".$disable_category.") AND first = 1 ORDER BY tid DESC LIMIT ".$n;
	else
	$sql = "SELECT  * FROM ".$db_tb_prefix."forum_post WHERE first = 1 ORDER BY tid DESC LIMIT ".$n;
	
	$news = $mydb->get_results($sql);
	if(!empty($news))
	{
		foreach ($news as $o)
		{
			if(intval($strcount)>0 && cnStrLen($o->subject)>$strcount)
			$o->subject = cnSubstr($o->subject,0,$strcount).'..';
			$data=(array)$o;
			require(get_style_file('bbs','bbs_discuz',$style));
		}						
	}else{ echo '暂无数据！';}
}
?>