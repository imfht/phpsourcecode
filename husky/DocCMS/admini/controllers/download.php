<?php
require(ABSPATH.'/inc/class.upload.php');
function index()
{
	global $download,$db,$request;
	$sql = 'SELECT * FROM `'.TB_PREFIX.'download` WHERE channelId='.$request['p'];
	$sb = new sqlbuilder('zdt',$sql,'ordering DESC,id DESC',$db,20);
	$download = new DataTable($sb,'下载列表');
	$download->add_col('编号','id','db',40,'"$rs[id]"');
	$download->add_col('软件名称','title','db',0,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');
	$download->add_col('软件大小','fileSize','db',150,'"$rs[fileSize]"');
	$download->add_col('更新时间','dtTime','db',180,'"$rs[dtTime]"');
	$download->add_col('处理','handling','text',100,'"<a href=\"./index.php?a=destroy&p=$rs[channelId]&n=$rs[id]\" onclick=\"return confirm(\'您确认要删除该下载?一旦删除，将不可恢复。\');\">[删除]</a>|<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">[修改]</a> "');
	$download->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
}
function create()
{
	global $result,$db,$request;
	if($_POST)
	{
		if(!empty($_FILES['uploadfile'])&&empty($request['filePath']))
		{
			$download_form = new download();
			$download_form->addnew();
			$download_form->get_request($request);
			$download_form->channelId =$request['p'];
			$download_form->dtTime    =date('Y-m-d H:i:s');
			$download_form->filePath  =empty($request['filePath'])?upload_software('uploadfile'):$request['filePath'];
			$download_form->fileSize = DisplayFileSize(@filesize(ABSPATH.$download_form->filePath));
		}
		else
		{
			$download_form = new download();
			$download_form->addnew();
			$download_form->get_request($request);
			$download_form->channelId =$request['p'];
			$download_form->dtTime    =date('Y-m-d H:i:s');
		}
		if($download_form->save())
		{
			redirect_to($request['p'],'index');
		}
		else
		{
			echo '添加失败！';
		}
	}
}
function edit()
{
	global $download_item,$db,$request;
	$sql='SELECT * FROM '.TB_PREFIX.'download WHERE id='.$request['n'];
	$download_item = $db->get_row($sql);
	if($_POST)
	{
		if(!empty($_FILES['uploadfile'])&&empty($request['filePath']))
		{
			$download_form = new download();
			$download_form->id=$request['n'];
			$download_form->get_request($request);
			$download_form->channelId =$request['p'];
			$download_form->dtTime=date('Y-m-d H:i:s');

			$download_form->filePath  =empty($request['filePath'])?upload_software('uploadfile',$download_item->filePath):$request['filePath'];
			$download_form->fileSize = DisplayFileSize(@filesize(ABSPATH.$download_form->filePath));
		}
		else
		{
			$download_form = new download();
			$download_form->id=$request['n'];
			$download_form->get_request($request);
			$download_form->channelId =$request['p'];
			$download_form->dtTime    =date('Y-m-d H:i:s');
		}
		if($download_form->save())
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
		$sql='SELECT filePath FROM '.TB_PREFIX.'download WHERE id='.$request['n'].' LIMIT 1';
		$filepath = $db->get_var($sql);
		if(!empty($filepath) && $filepath != '/upload/')
		del_old_file($db->get_var($sql));
		$sql='DELETE FROM '.TB_PREFIX.'download WHERE id='.$request['n'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else {
			echo '删除失败！';
		}
	}

}
function upload_software($fileName,$oldFile='')
{
	$upload = new Upload();
	del_old_file($oldFile);
	if(!empty($_FILES[$fileName]))
	{
		$upload->AllowExt='rar|zip|txt|exe|zip|doc|docx|ppt|pptx|xls|xlsx|mp3|mpg|mpeg|avi|rm|rmvb|wmv|wav|wma';
		$fileName = $upload->SaveFile($fileName);
		if(empty($fileName))echo $upload->showError();
		return UPLOADPATH.$fileName;
	}
}
function del_old_file($oldFile)
{
	if(!empty($oldFile))
	{
		if(is_file(ABSPATH.$oldFile))
		{
			@unlink(ABSPATH.$oldFile);
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
		$sql ='UPDATE '.TB_PREFIX.'download SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
?>