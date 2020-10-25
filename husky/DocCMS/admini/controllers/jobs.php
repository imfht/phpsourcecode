<?php 
function index()
{
	global $jobs,$db,$request;
	$sql  = 'SELECT * FROM `'.TB_PREFIX.'jobs` WHERE channelId='.$request['p'];
	$sb   = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,12);
	$jobs = new DataTable($sb,'招聘信息页面');
	$jobs->add_col('编号','id','db',40,'"$rs[id]"');
	$jobs->add_col('职位名称','title','db',0,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');
	$jobs->add_col('发布时间','dtTime','db',140,'');
	$jobs->add_col('截至时间','lastTime','db',140,'');
	$jobs->add_col('预览','preview','text',40,'"<a target=\"_blank\" href=\"../index.php?p=$rs[channelId]\">预览</a>"');
	$jobs->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroy&p=$rs[channelId]&n=$rs[id]\">[删除]</a>|<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">[修改]</a>"');
	$jobs->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
}
function create()
{
	global $result,$db,$request;
	if($_POST)
	{
		$jobs = new jobs();
		$jobs->addnew();
		$jobs->get_request($request);
		$jobs->dtTime = date("Y-m-d H:i:s");
		$jobs->channelId=$request['p'];
		if($jobs->save())
		{
			//数据更新后提交到搜索引擎
			docPing($request['p'],mysql_insert_id());
			redirect_to($request['p'],'index');
		}
		else
		{
			echo "添加失败！";
		}
	}
}
function edit()
{
	global $jobs_item,$db,$request;
	if(empty($request['title']))
	{
		$sql='SELECT * FROM '.TB_PREFIX.'jobs WHERE id='.$request['n'];
		$jobs_item = $db->get_row($sql);
	}
	else
	{
		$jobs_item = new jobs();
		$jobs_item->get_request($request);
		$jobs_item->id = $request['n'];
		$jobs_item->dtTime = date("Y-m-d H:i:s");
		$jobs_item->channelId = $request['p'];
		if($jobs_item->save())
		redirect_to($request['p'],'index');
		else
		echo '修改失败！';
	}
}

function destroy()
{
	global $db,$request;
	if(!empty($request['n']))
	{
		$sql='DELETE FROM '.TB_PREFIX.'jobs WHERE id=\''.$request['n'].'\' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else {
			echo '删除失败！';
		}
	}
}
function viewresumes()
{
	global $db,$request,$resumes,$jobs;
	$jobs = $db->get_results('SELECT * FROM `'.TB_PREFIX.'jobs` WHERE lastTime>='.date('Y-m-d'));
	if(empty($request['j']))
	$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'jobs` as a left join `'.TB_PREFIX.'jobs_resume` as b on b.parentId = a.id WHERE b.channelId='.$request['p'],'b.id',$db,12);
	else
	$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'jobs` as a left join `'.TB_PREFIX.'jobs_resume` as b  on b.parentId = a.id  WHERE b.channelId='.$request['p'].'  AND b.parentId='.$request['j'],'b.id',$db,12);	
	$resumes = new DataTable($sb,'应聘者简历信息页面');
	$resumes->add_col('编号','id','db',40,'"$rs[id]"');
	$resumes->add_col('应聘者名称','name','db',150,'"<a href=\"./index.php?a=viewresume&p=$rs[channelId]&n=$rs[id]\">$rs[name]</a>"');
	$resumes->add_col('应聘职位名称','title','db',0,'"<a href=\"./index.php?a=viewresume&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');	
	$resumes->add_col('简历添加时间','dtTime','db',140,'');
	if(empty($request['j']))
	$resumes->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroyresume&p=$rs[channelId]&n=$rs[id]\">[删除]</a><a href=\"./index.php?a=viewresume&p=$rs[channelId]&n=$rs[id]\">[查看]</a>"');
	else
	$resumes->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroyresume&p=$rs[channelId]&j='.$request['j'].'&n=$rs[id]\">[删除]</a><a href=\"./index.php?a=viewresume&p=$rs[channelId]&n=$rs[id]\">[查看]</a>"');
}
function viewresume()
{
	global $db,$request,$resume;
	$sql = 'SELECT * FROM `'.TB_PREFIX.'jobs_resume` WHERE id='.$request['n'];
	$resume = $db->get_row($sql);
}
function destroyresume()
{
	global $db,$request;
	$sql = 'DELETE FROM `'.TB_PREFIX.'jobs_resume` WHERE id='.$request['n'];
	if($db->query($sql))
	{
		if(empty($request['j']))
		redirect_to($request['p'],'index','a=viewresumes');
		else 
		redirect_to($request['p'],'index','j='.$request['j'].'&a=viewresumes');
	}
	else
	{
		echo '删除失败！';
	}
}
function ordering()
{
	global $db,$request;
	$ordering = $request['ordering'];
	foreach($ordering as $key=>$value)
	{
		if(empty($value))$value=0;
		$sql ='UPDATE '.TB_PREFIX.'jobs SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
?>