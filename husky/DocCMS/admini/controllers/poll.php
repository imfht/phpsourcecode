<?php	
function index()
{
	require_once(ABSPATH.'/inc/class.categorytree.php');
	global $poll,$poll_category,$db,$request;
	
	$poll_category = $db->get_results('SELECT * FROM `'.TB_PREFIX.'poll_category` WHERE channelId='.$request['p']);	
	
	if($request['c']=='0')
	{
		$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'poll_category` WHERE channelId='.$request['p'],'ordering DESC,id DESC',$db,12);
		$poll = new DataTable($sb,'投票主题页面');
		$poll->add_col('投票主题','title','db',0,'"$rs[title]"');
		$poll->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroy_title&p='.$request['p'].'&c=$rs[id]\"  onclick=\"return confirm(\'您确认要删除该产品?一旦删除，将不可恢复。\');\">[删除]</a>|<a href=\"./index.php?a=edit_title&p='.$request['p'].'&c=$rs[id]\">[修改]</a>"');
		$poll->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
	}
	else
	{
		$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'poll` WHERE categoryId='.$request['c'],'ordering DESC,id DESC',$db,12);
		$poll = new DataTable($sb,'投票主题页面');
		$poll->add_col('选项内容','choice','db',0,'"$rs[choice]"');
		$poll->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroy_choice&p='.$request['p'].'&n=$rs[id]&c=$rs[categoryId]\"  onclick=\"return confirm(\'您确认要删除该产品?一旦删除，将不可恢复。\');\">[删除]</a>|<a href=\"./index.php?a=edit_choice&p='.$request['p'].'&n=$rs[id]&c=$rs[categoryId]\">[修改]</a>"');
		$poll->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
	}
}
function create_title()
{
	global $request;
	if($_POST)
	{
		$poll_category = new poll_category();
		$poll_category->addnew();
		$poll_category->get_request($request);
		$poll_category->dtTime = date("Y-m-d H:i:s");
		$poll_category->channelId=$request['p'];
		if($poll_category->save())
		{
			redirect_to($request['p'],'index');
		}
		else
		{
			echo '添加主题失败！';
		}
	}
}
function edit_title()
{
	global $db,$request,$poll_category;
	$id = $request['c'];
	if(empty($request['title']))
	{
		$sql = "SELECT * FROM ".TB_PREFIX."poll_category WHERE id='$id'";
		$poll_category = $db->get_row($sql);
	}
	else
	{
		$poll_category = new poll_category();
		$poll_category->id = $id;
		$poll_category->get_request($request);
		$poll_category->channelId = $request['p'];
		if($poll_category->save())
		{
			redirect_to($request['p'],'index');
		}
		else
		{
			echo '修改主题失败！';
		}
	}
}

function destroy_title()
{
	global $db,$request;
	if(!empty($request['c']))
	{
		$sql='DELETE FROM '.TB_PREFIX.'poll_category WHERE id='.$request['c'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else {
			echo '删除主题失败！';
		}
	}
}

function create_choice()
{
	global $db,$request,$poll_category;
	$sql = "SELECT * FROM ".TB_PREFIX."poll_category WHERE id=".$request['c'];
	$poll_category = $db->get_row($sql);
	if($_POST)
	{
		$poll = new poll();
		$poll->addnew();
		$poll->get_request($request);
		$poll->channelId=$request['p'];
		$poll->categoryId=$request['c'];
		if($poll->save())
		{
			redirect_to($request['p'],'index','c='.$request['c']);
		}
		else
		{
			echo '添加选项失败！';
		}
	}
}
function edit_choice()
{
	global $db,$request,$poll_category,$poll;
	$sql = "SELECT * FROM ".TB_PREFIX."poll_category WHERE id=".$request['c'];
	$poll_category = $db->get_row($sql);
	if(empty($request['choice']))
	{
		$sql = "SELECT * FROM ".TB_PREFIX."poll WHERE id=".$request['n'];
		$poll = $db->get_row($sql);
	}
	else
	{
		$poll = new poll();
		$poll->id = $request['n'];
		$poll->get_request($request);
		$poll->channelId = $request['p'];
		$poll->categoryId=$request['c'];
		if($poll->save())
		{
			redirect_to($request['p'],'index','c='.$request['c']);
		}
		else
		{
			echo '修改选项失败！';
		}
	}
}
function destroy_choice()
{
	global $db,$request;
	if(!empty($request['n']))
	{
		$sql='DELETE FROM '.TB_PREFIX.'poll WHERE id='.$request['n'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index','c='.$request['c']);
		}
		else {
			echo '删除选项失败！';
		}
	}
}
function ordering()
{
	global $db,$request;
	$ordering = $request['ordering'];
	foreach($ordering as $key=>$value)
	{
		$sql ='UPDATE '.TB_PREFIX.'poll SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index','c='.$request['c']);
}
function category_ordering()
{
	global $db,$request;
	$ordering = $request['ordering'];
	foreach($ordering as $key=>$value)
	{
		$sql ='UPDATE '.TB_PREFIX.'poll_category SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
?>