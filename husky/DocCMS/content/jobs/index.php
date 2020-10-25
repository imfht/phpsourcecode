<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组
	$sql="SELECT * FROM `".TB_PREFIX."jobs` WHERE lastTime>='".date("Y-m-d")."' AND channelId=".$params['id'];
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,jobsCount,true,URLREWRITE ? '/' : './');
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
	global $tag;
	$sql="SELECT * FROM ".TB_PREFIX."jobs WHERE id=".$params['args'];
	$tag['data.row'] = $db->get_row($sql,ARRAY_A);
}
function send()
{
	global $db;
	global $params,$request;
	global $tag;
	if(!empty($request['name']))
	{
		foreach ($request as $k=>$v)
		{
			$request[$k]=RemoveXSS($v);
		}
		require_once(ABSPATH.'/inc/models/jobs_resume.php');	
		$resume = new c_jobs_resume();	
		$resume->addnew($request);	
		$resume->parentId = $request['r'];
		$resume->channelId = $request['p'];
		$resume->dtTime = date("Y-m-d H:i:s");
		if($resume->save())
		{	
			if(URLREWRITE)
			{
				echo '<script>alert("您的个人简历已经提交成功，工作人员会及时回复并和你联系！");</script>';
				redirect('/'.$tag['channel.menuname'].'/');
			}
			else
			{
				echo '<script>alert("您的个人简历已经提交成功，工作人员会及时回复并和你联系！");</script>';
				redirect_to($request['p'],'index');
			}			
		}
		else
		{
			if(URLREWRITE)
			{
				echo '<script>alert("对不起，系统错误，您的个人简历并未提交成功，请电话与我们联系！");</script>';
				redirect('/'.$tag['channel.menuname'].'/');
			}
			else
			{
				echo '<script>alert("对不起，系统错误，您的个人简历并未提交成功，请电话与我们联系！");</script>';
				redirect_to($request['p'],'index');
			}	
		}
	}
}
?>