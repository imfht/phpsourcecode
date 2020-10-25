<?php
function index()
{
	global $index,$news_list_content,$contents,$db;
	$index = "欢迎您的到来！";
	$news_list_content='';
	$contents='';
	$news_list=$db->get_results("SELECT * FROM ".TB_PREFIX."list order by id desc LIMIT 7");
	if($news_list)
	{
		foreach ($news_list as $o)
		{
			if(cnStrLen($o->title)>100)
			$o->title = cnSubStr($o->title,0,100).'..';
			$news_list_content.='<li><span>'.date('Y-m-d',strtotime($o->dtTime)).'</span><a href="./index.php?a=edit&p='.$o->channelId.'&n='.$o->id.'">'.$o->title.'</a></li>';
		}
	}
	else
	{
		$news_list_content.='<li><a herf="#">暂无新闻</a></li>';
	}
		
	$content=$db->get_results("SELECT * FROM ".TB_PREFIX."comment order by id desc LIMIT 7");
	if($content)
	{
		foreach ($content as $o)
		{
			if(cnStrLen($o->content)>20)
			$o->content = cnSubStr($o->content,0,80).'..';
			$contents.='<li><span>'.date('Y-m-d',strtotime($o->dtTime)).'</span><a href="./index.php?a=edit&p='.$o->channelId.'&n='.$o->recordId.'">'.$o->content.'</a></li>';
		}
	}
	else
	{
		$contents.='<li><a herf="#">暂无评论</a></li>';
	}
}

function readDirSize()
{
	if(!WEBSIZECOUNTS || $_GET['type']=='retry')
	{
		$path=ABSPATH;
		$ar=getDirSize($path);  
		$size = intval($ar['size']/(1024*1024));
		
		$tempStr = file2String(ABSPATH.'/config/doc-config.php');
		$tempStr = preg_replace("/'WEBSIZECOUNTS','.*?'/i","'WEBSIZECOUNTS','".$size."'",$tempStr);		
		string2file($tempStr,ABSPATH.'/config/doc-config.php');
		@chmod(ABSPATH.'/config/doc-config.php', 0666);
		
		echo $size;
		exit;
	}
	else
	{
		echo WEBSIZECOUNTS;
		exit;
	}
}
?>