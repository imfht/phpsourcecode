<?php
function index()
{
	global $list,$db,$request;
	if($_POST)
	{
		$_SESSION[TB_PREFIX.'keyword'] = $request['keyword'];
		!checkSqlStr($request['keyword'])? $request['keyword'] = $request['keyword'] : exit('非法字符');	
		$sql = 'SELECT * FROM `'.TB_PREFIX.'list` WHERE title LIKE "%'.$request['keyword'].'%" AND channelId='.$request['p'];
	}
	else if($_SESSION[TB_PREFIX.'keyword'] && $request['mdtp'])
	{
		$request['keyword'] = $_SESSION[TB_PREFIX.'keyword'];		
		$sql = 'SELECT * FROM `'.TB_PREFIX.'list` WHERE title LIKE "%'.$request['keyword'].'%" AND channelId='.$request['p'];
	}
	else
	{
		$_SESSION[TB_PREFIX.'keyword'] = '';
		$sql = 'SELECT * FROM `'.TB_PREFIX.'list` WHERE channelId='.$request['p'];
	}
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,20);
	
	$url=array('delete'=>'./index.php?p='.$request['p'].'&a=deleteAll','move'=>'./index.php?p='.$request['p'].'&a=move');
	$sql = "SELECT * FROM `".TB_PREFIX."menu` WHERE type='list' AND id!=".$request['p'];
	$move_options = $db->get_results($sql);
	$list = new DataTable($sb,'新闻列表页面',true,$url,$move_options);
	$list->add_col('编号','id','db',40,'"$rs[id]"');
	$list->add_col('主题','title','db',0,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');
	$list->add_col('作者','author','db',0,'"$rs[author]"');
	$list->add_col('预览','preview','text',40,'"<a target=\"_blank\" href=\"../index.php?p=$rs[channelId]&a=view&r=$rs[id]\">预览</a>"');
	$list->add_col('时间','dtTime','db',140,'');
	$list->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroy&p=$rs[channelId]&n=$rs[id]\" onclick=\"return confirm(\'您确认要删除该新闻?一旦删除，将不可恢复。\');\">[删除]</a>|<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">[修改]</a>"');
	$list->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
}
function edit()
{
	global $list_item,$db,$request;
	if(empty($request['title']))
	{
		$sql='SELECT * FROM '.TB_PREFIX.'list WHERE id='.$request['n'];
		$list_item = $db->get_row($sql);
	}
	else
	{
		$list = new doc_list();
		$list->get_request($request);
		
		if(!empty($_FILES['uploadfile'])&&empty($request['originalPic']))
		{
			$sql = "SELECT * FROM ".TB_PREFIX."list WHERE id=".$request['n'];
			$row = $db->get_row($sql);
			if($row)
			{
				if(is_file(ABSPATH.$row->originalPic))
				{
					@unlink(ABSPATH.$row->originalPic);
					@unlink(ABSPATH.$row->indexPic);
				}
			}
			$upload = new Upload();
			$fileName = $upload->SaveFile('uploadfile');
			if(empty($fileName))echo $upload->showError();
			$list->originalPic = UPLOADPATH.$fileName;
			$paint = new Paint($list->originalPic);
			$list-> indexPic= $paint->Resize(listWidth,listHight,'i_');
		
		}

		$list->style = $request['sytle_color'].'@'.$request['sytle_font_b'].'@'.$request['sytle_font_i'].'@';
		if(!empty($request['dtTime']))
			$list->dtTime = date("Y-m-d H:i:s",strtotime($request['dtTime']));
		else
			$list->dtTime = date("Y-m-d H:i:s");
		$list->id=$request['n'];
		$list->channelId=$request['p'];
		$list->content	= $request['content'];
		$list->hassplitpages=(strpos($request['content'],'{#page#}')!==false)?'1':'0';		
		$list->save();
		redirect_to($request['p'],'index');
	}
}
function create()
{
	global $result,$db,$request;
	if($_POST)
	{		
		$list = new doc_list();
		$list->addnew();
		$list->get_request($request);
		
		if(!empty($_FILES['uploadfile'])&&empty($request['originalPic']))
		{
			$upload = new Upload();
			$fileName = $upload->SaveFile('uploadfile');
			if(empty($fileName))echo $upload->showError();
			$list->originalPic = UPLOADPATH.$fileName;
			$paint = new Paint($list->originalPic);
			$list-> indexPic= $paint->Resize(listWidth,listHight,'i_');
		}
		
		$list->style = $request['sytle_color'].'@'.$request['sytle_font_b'].'@'.$request['sytle_font_i'].'@';
		if(!empty($request['dtTime']))
		$list->dtTime = date("Y-m-d H:i:s",strtotime($request['dtTime']));
		else
		$list->dtTime = date("Y-m-d H:i:s");
		
		$list->channelId=$request['p'];
		$list->content	=$request['content'];
		$list->hassplitpages=(strpos($request['content'],'{#page#}')!==false)?'1':'0';				
		$list->save();
		//数据更新后提交到搜索引擎
		docPing($request['p'],mysql_insert_id());
		redirect_to($request['p'],'index');
	}
}
function destroy()
{
	global $db,$request;
	if(!empty($request['n']))
	{
		$sql = 'SELECT * FROM '.TB_PREFIX.'list WHERE id ='.$request['n'];
		$row = $db->get_row($sql);
		if(!empty($row->indexPic))
		{
				@unlink(ABSPATH.$row->originalPic);
				@unlink(ABSPATH.$row->indexPic);
		}
		
		$sql='DELETE FROM '.TB_PREFIX.'list WHERE id='.$request['n'].' LIMIT 1';

		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else {
			echo '删除失败！';
		}
	}
}
function ordering()
{
	global $db,$request;
	$ordering = $request['ordering'];
	foreach($ordering as $key=>$value)
	{
		if(empty($value))$value=0;
		$sql ='UPDATE '.TB_PREFIX.'list SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
function deleteAll()
{
	global $db,$request;
	$delete_date = explode(",",$request['ids']);
	foreach($delete_date as $value)
	{
		$sql="SELECT guid FROM ".TB_PREFIX."list WHERE id=$value LIMIT 1";
		$guid = $db->get_var($sql);
		$sql="DELETE FROM ".TB_PREFIX."list WHERE id=$value LIMIT 1";
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
function move()
{
	global $db,$request;
	$move_cate=$request['move_to'];
	$delete_date = explode(",",$request['ids']);
	foreach($delete_date as $value)
	{
		$sql = "UPDATE ".TB_PREFIX."list SET channelId=".$move_cate." WHERE id=$value LIMIT 1";
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
?>