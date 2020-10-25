<?php
function index()
{
	global $video,$db,$request;
	$sql='SELECT * FROM `'.TB_PREFIX.'video` WHERE channelId='.$request['p'];
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,20);
	$video = new DataTable($sb,'视频列表');
	$video->add_col('编号','id','db',40,'"$rs[id]"');
	$video->add_col('主题','title','db',0,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');
	$video->add_col('预览','preview','text',40,'"<a target=\"_blank\" href=\"../index.php?p=$rs[channelId]&a=view&r=$rs[id]\">预览</a>"');
	$video->add_col('大小','counts','db',100,'"$rs[fileSize]"');
	$video->add_col('标签','keywords','db',160,'"$rs[keywords]"');
	$video->add_col('上传时间','dtTime','db',150,'"$rs[dtTime]"');
	$video->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroy&p=$rs[channelId]&n=$rs[id]\">[删除]</a>|<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">[修改]</a>"');
	$video->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
}
function create()
{
	global $result,$db,$request;
	if($_POST)
	{
		$video_form = new video();
		$video_form->addnew();
		$video_form->get_request($request);
		$video_form->channelId =$request['p'];
		$video_form->dtTime    =date('Y-m-d H:i:s');
		$video_form->filePath  =empty($request['filePath'])?upload_video('uploadfile'):$request['filePath'];
		$video_form->fileSize = DisplayFileSize(@filesize(ABSPATH.$video_form->filePath));
		$video_form->picture   =empty($request['picture'])?process_picture('uploadfile2'):$request['picture'];
		$video_form->save();
		//数据更新后提交到搜索引擎
		docPing($request['p'],mysql_insert_id());
		redirect_to($request['p'],'index');
	}
}
function edit()
{
	global $video_item,$db,$request;
	$sql='SELECT * FROM '.TB_PREFIX.'video WHERE id='.$request['n'];
	$video_item = $db->get_row($sql);
	if($_POST)
	{
		$video_form = new video();
		$video_form->get_request($request);
		$video_form->id=$request['n'];
		$video_form->channelId=$request['p'];
		$video_form->dtTime=date('Y-m-d H:i:s');
		$video_form->filePath  =empty($request['filePath'])?upload_video('uploadfile',$video_item->filePath):$request['filePath'];
		$video_form->fileSize = DisplayFileSize(@filesize(ABSPATH.$video_form->filePath));
		$video_form->picture   =empty($request['picture'])?process_picture('uploadfile2',$video_item->picture):$request['picture'];
		$video_form->save();
		redirect_to($request['p'],'index');
	}
}
function destroy()
{
	global $db,$request;
	if(!empty($request['n']))
	{
		$sql='SELECT filePath,picture FROM '.TB_PREFIX.'video WHERE id='.$request['n'].' LIMIT 1';
		$tempfile=$db->get_row($sql);
		del_old_file($tempfile->filePath);
		del_old_file($tempfile->picture);
	
		$sql='DELETE FROM '.TB_PREFIX.'video WHERE id='.$request['n'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else {
			echo '删除失败！';
		}
	}
}
function upload_video($fileName,$oldFile='')
{
		$upload = new Upload();
		del_old_file($oldFile);
		if(!empty($_FILES[$fileName]))
		{
			$upload->AllowExt='rm|rmvb|mp3|mp4|wav|mid|midi|avi|mpg|mpeg|wma|wmv|flv|swf';
			$fileName = $upload->SaveFile($fileName);
			if(empty($fileName))echo $upload->showError();
			return UPLOADPATH.$fileName;
		}
}
function process_picture($fileName,$oldFile='')
{
		$upload = new Upload();
		del_old_file($oldFile);
		if(!empty($_FILES[$fileName]))
		{
			$upload->AllowExt='jpg|jpeg|gif|bmp|png';
			$fileName = $upload->SaveFile($fileName);
			if(empty($fileName))echo $upload->showError();
			$paint = new Paint(UPLOADPATH.$fileName);
			$newname=$paint->Resize(videoWidth,videoHight,'s_');
			@unlink(ABSPATH.UPLOADPATH.$fileName);
			return $newname;
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
		$sql ='UPDATE '.TB_PREFIX.'video SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
?>