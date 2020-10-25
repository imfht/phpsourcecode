<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组
	$isPublic = GUESTBOOKAUDITING?'isPublic=1 AND':'';
	$sql="SELECT * FROM ".TB_PREFIX."guestbook WHERE ".$isPublic." channelId=".$params['id'];
	$sb = new sqlbuilder('mdt',$sql,'id DESC',$db,guestbookCount,true,URLREWRITE ? '/' : './');
	if(!empty($sb->results)){
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
	global $tag;	// 标签数组
	$isPublic = GUESTBOOKAUDITING?'isPublic=1 AND':'';
	$sql="SELECT * FROM ".TB_PREFIX."guestbook WHERE ".$isPublic." id=".$params['args'];
	$guestbook = $db->get_row($sql);
	$tag['data.row']=(array)$guestbook;
	unset($guestbook);
}
function create()
{
	global $db,$request;
	if ($_SESSION['verifycode'] != $request['checkcode'])
	{
		echo '<script>alert("请正确填写验证码！");location.href="javascript:history.go(-1)";</script>';
		exit;
	}
	
	foreach ($request as $k=>$v)
	{
		$request[$k]=RemoveXSS($v);
	}
	
	require(ABSPATH.'/admini/models/guestbook.php');
	$guestbook = new guestbook();
	$guestbook->addnew($request);
	$guestbook->custom=@implode('<|@|>',$request['custom']);
	$guestbook->dtTime=date('Y-m-d H:i:s');
	$guestbook->channelId=$request['p'];
	$guestbook->ip=$_SERVER['REMOTE_ADDR'];
	$guestbook->uid=$_SESSION[TB_PREFIX.'user_ID'];

	if($guestbook->save())
	{
		if(guestbookISON)
		{
			sys_mail(' 留言提醒','最新留言提醒：您的网站：<a href="http://'.WEBURL.'">'.WEBURL.'</a> 有最新留言，请及时前往审核回复！');
		}
		echo '<script>alert("恭喜，您的留言已提交成功，工作人员会及时回复！");window.location.href="'.sys_href($request['p']).'";</script>';
		exit;
	}
	else
	{
		echo '<script>alert("对不起，系统错误，您的留言未能及时提交，请电话与我们联系。");window.location.href="'.sys_href($request['p']).'";</script>';
		exit;
	}
}
?>