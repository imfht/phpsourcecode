<?php
require(ABSPATH.'/inc/class.upload.php');
require(ABSPATH.'/inc/class.paint.php');	
function index()
{
	global $linkers,$db,$request;
	$sql     = 'SELECT * FROM `'.TB_PREFIX.'linkers` WHERE channelId='.$request['p'];
	$sb      = new sqlbuilder('zdt',$sql,'ordering DESC,id DESC',$db,20);
	$linkers = new DataTable($sb,'友情链接列表');
	$linkers->add_col('编号','id','db',80,'"$rs[id]"');
	$linkers->add_col('案例名称','title','db',0,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');
	$linkers->add_col('链接地址','linkAddress','db',300,'"$rs[linkAddress]"');
	$linkers->add_col('处理','handling','text',200,'"<a href=\"./index.php?a=destroy&p=$rs[channelId]&n=$rs[id]\" onclick=\"return confirm(\'您确认要删除该链接?一旦删除，将不可恢复。\');\">[删除]</a>|<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">[修改]</a>"');
	$linkers->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
}
function create()
{
	global $result,$db,$request;
	if($_POST)
	{	
		if(!empty($_FILES['uploadfile'])&&empty($request['originalPic']))
		{
			$linkers_form = new linkers();
			$linkers_form->addnew();
			$linkers_form->get_request($request);
			$linkers_form->channelId=$request['p'];
			$linkers_form->dtTime = date("Y-m-d H:i:s");

			$upload = new Upload();
			if(!empty($_FILES['uploadfile']))
			{
				$fileName = $upload->SaveFile('uploadfile');
				if(empty($fileName))echo $upload->showError();
				$linkers_form-> originalPic = UPLOADPATH.$fileName;

				$paint = new Paint($linkers_form-> originalPic);
				$linkers_form->smallPic= $paint->Resize(90,30,'s_');
			}
		}

		else	{
			$linkers_form = new linkers();
			$linkers_form->addnew();
			$linkers_form->get_request($request);
			$linkers_form->channelId=$request['p'];

			$linkers_form->dtTime = date("Y-m-d H:i:s");
		}
		if($linkers_form->save())
		{
			redirect_to($request['p'],'index');
		}
		else
		{
			echo '添加失败';
		}	

	}
}
function edit()
{
	global $linkers_item,$db,$request;
	$id = $request['n'];
	$sql='SELECT * FROM '.TB_PREFIX.'linkers WHERE id='.$id;
	$linkers_item = $db->get_row($sql);
	if($_POST)
	{
		if(!empty($_FILES['uploadfile'])&&empty($request['originalPic']))
		{

			$sql = "SELECT * FROM ".TB_PREFIX."linkers WHERE id='$id' LIMIT 1";
			$row = $db->get_row($sql);
			if($row->originalPic)
			{
				if(is_file(ABSPATH.$row->originalPic))
				{
					@unlink(ABSPATH.$row->originalPic);
					@unlink(ABSPATH.$row->smallPic);
				}
			}

			$linkers_form = new linkers();
			$linkers_form->get_request($request);
			$linkers_form->id=$id;
			$linkers_form->links=$request['links'];
			$linkers_form->channelId=$request['p'];

			$upload = new Upload();
			$fileName = $upload->SaveFile('uploadfile');
			if(empty($fileName))echo $upload->showError();
			$linkers_form-> originalPic = UPLOADPATH.$fileName;
			$paint = new Paint($linkers_form-> originalPic);
			$linkers_form-> smallPic= $paint->Resize(90,30,'s_');
		}
		else
		{
			$linkers_form = new linkers();
			$linkers_form->get_request($request);
			$linkers_form->id=$id;
			$linkers_form->links=$request['links'];
			$linkers_form->channelId=$request['p'];
		}
		if($linkers_form->save())
		{
			redirect_to($request['p'],'index');
		}
		else
		{
			echo '修改失败！';
		}
	}
	
}
function destroy()
{
	global $db,$request;
	if(!empty($request['n']))
	{
		$sql='SELECT * FROM '.TB_PREFIX.'linkers WHERE id='.$request['n'].' LIMIT 1';
		$row = $db->get_row($sql);
		if($row->originalPic)
		{
			if(is_file(ABSPATH.$row->originalPic))
			{
				@unlink(ABSPATH.$row->originalPic);
				@unlink(ABSPATH.$row->smallPic);
			}
		}
		$sql='DELETE FROM '.TB_PREFIX.'linkers WHERE id='.$request['n'].' LIMIT 1';
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
		$sql ='UPDATE '.TB_PREFIX.'linkers SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
?>