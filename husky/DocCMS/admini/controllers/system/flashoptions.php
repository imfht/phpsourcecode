<?php
checkme(7);
function index()
{
	global $db,$focus_group;
	$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'flash_group` WHERE `type` = "focus"','id DESC',$db,12);
	$focus_group = new DataTable($sb,'焦点图列表');
	$focus_group->add_col('ID','id','db',0,'"$rs[id]"');
	$focus_group->add_col('焦点图名称','title','db',0,'"<a href=\"./index.php?m=system&s=flashoptions&a=edit_group&group_id=$rs[id]\">$rs[title]</a>"');
	$focus_group->add_col('盒子ID(前台调用不可重复)','boxId','db',300,'"$rs[boxId]"');
	$focus_group->add_col('风格应用名称','pattern','db',0,'"$rs[pattern]"');
	$focus_group->add_col('添加时间','dtTime','db',180,'"$rs[dtTime]"');

	$focus_group->add_col('操作','edit','text',140,'"<a href=\"./index.php?m=system&s=flashoptions&a=destroy_group&group_id=$rs[id]\" onclick=\"return confirm(\'您确认要删除?\');\">[删除]</a>|<a href=\"./index.php?m=system&s=flashoptions&a=edit_group&group_id=$rs[id]\">[编辑]</a>"');

}

function create()
{
	global $db,$request;
	if($_POST)
	{
		$flash = new flash();
		$flash->addnew($request);
		$flash->get_request($request);
		$flash->dtTime=date("Y-m-d H:i:s");
		if(!empty($_FILES['uploadfile']))
		{
			$upload = new Upload();
			$fileName = $upload->SaveFile('uploadfile');
			$flash->picpath =UPLOADPATH.$fileName;
		}
		if($flash->save())
		{
			if($request['type']=='focus')
				redirect('?m=system&s=flashoptions&a=edit_group&group_id='.$request['group_id']);
			else
				redirect('?m=system&s=flashoptions&a=edit_group&group_id='.$request['group_id']);
		}
	}
}
function edit()
{
	global $db,$request;
	if(empty($request['title']))
	{
		global $edit_item;
		$sql='SELECT * FROM `'.TB_PREFIX.'flash` WHERE id='.intval($request['n']);
		$edit_item = $db->get_row($sql);
	}
	else
	{
		$id=intval($request['n']);
		$flash = new flash();
		$flash->id=$id;
		$flash->get_request($request);
		$flash->dtTime=date("Y-m-d H:i:s");
		if(!empty($_FILES['uploadfile']))
		{
			//删除遗留图片
			$sql = 'SELECT picpath FROM `'.TB_PREFIX.'flash` WHERE id='.$id;
			$picpath = $db->get_var($sql);
			if(!empty($picpath)){
				if(is_file(ABSPATH.$picpath))@unlink(ABSPATH.$picpath);
			}
			
			if($_FILES['uploadfile']['size']>0 && $_FILES['uploadfile']['size']<500000){
				$upload = new Upload();
				$fileName = $upload->SaveFile('uploadfile');
				$flash->picpath =UPLOADPATH.$fileName;
			}else{
				$flash->picpath ='';
			}
		}
		$flash->save();
		if($request['type']=='focus')
			redirect('?m=system&s=flashoptions&a=edit_group&group_id='.$request['group_id']);
		else
			redirect('?m=system&s=flashoptions&a=edit_group&group_id='.$request['group_id']);

	}	
}
function destroy()
{
	global $db,$request;
	$id=intval($request['n']);
	//删除遗留图片
	$sql = 'SELECT picpath FROM `'.TB_PREFIX.'flash` WHERE id='.$id;
	$picpath = $db->get_var($sql);
	if(!empty($picpath)){
		if(is_file(ABSPATH.$picpath))@unlink(ABSPATH.$picpath);
	}	
	
	$sql = 'DELETE FROM `'.TB_PREFIX.'flash` WHERE id='.$id;
	if($db->query($sql))
	{
		if($request['type']=='focus')
			redirect('?m=system&s=flashoptions&a=edit_group&group_id='.$request['group_id']);
		else
			redirect('?m=system&s=flashoptions&a=edit_group&group_id='.$request['group_id']);
	}
	else
	{
		echo '删除失败！';
	}
}
function create_group()
{
	global $db,$request;
	if($_POST)
	{
		$request['title'] = trim($request['title']);
		if(empty($request['title']))
		{
			echo "<script language='javascript'>window.alert('焦点图名称不能为空!');window.history.go(-1);</script>";
			exit;
		}
		$flash_group = new flash_group();
		$flash_group->addnew($request);
		$flash_group->type = "focus";
		$flash_group->dtTime = date("Y-m-d H:i:s");
		
		$fromUrl = ABSPATH."inc/js/myfocus/pattern/".$request['pattern'];
		$toUrl = get_abs_skin_root()."/res/plug-in/myfocus/";
		
		if(!is_dir($toUrl.'pattern/img/'))
		{
			mkdir($toUrl.'pattern/img/',0777,true);			
		}
		if(!is_file($toUrl.'pattern/'.$request['pattern'].'.css'))
		{
			copy(ABSPATH."inc/js/myfocus/myfocus-2.0.4.min.js",$toUrl."myfocus-2.0.4.min.js");
			copy(ABSPATH."inc/js/myfocus/pattern/".$request['pattern'].'.css',$toUrl."pattern/".$request['pattern'].".css");
			copy(ABSPATH."inc/js/myfocus/pattern/".$request['pattern'].'.js',$toUrl."pattern/".$request['pattern'].".js");
			if(is_dir(ABSPATH."inc/js/myfocus/pattern/img/".$request['pattern']))
			{
				copy(ABSPATH."inc/js/myfocus/pattern/img/loading.gif",$toUrl."img/loading.gif");
				dir_copy(ABSPATH."inc/js/myfocus/pattern/img/".$request['pattern'],$toUrl."pattern/img/".$request['pattern']);
			}
		}
		if($flash_group->save())
		redirect('?m=system&s=flashoptions');
		else
		echo '创建失败';
	}
	
}
function edit_group()
{
	global $db,$request,$edit_group_item,$flash_group;
	$sql = "SELECT * FROM ".TB_PREFIX."flash_group WHERE id=".intval($request['group_id']);
	$edit_group_item = $db->get_row($sql);
	
	if(!$_POST)
	{
		$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'flash` WHERE group_id='.intval($request['group_id']),'ordering DESC,id DESC',$db,12);
		
		$flash_group = new DataTable($sb,'图片列表 | <a class="deleteall" href="?m=system&amp;s=flashoptions&amp;a=create&group_id='.$request['group_id'].'&type=focus">添加一张图片</a>');
		$flash_group->add_col('顺序','id','db',40,'"$rs[id]"');
		$flash_group->add_col('标题','title','db',0,'"$rs[title]"');
		$flash_group->add_col('添加时间','dtTime','db',180,'"$rs[dtTime]"');
		$flash_group->add_col('操作','edit','text',140,'"<a href=\"./index.php?m=system&s=flashoptions&a=destroy&group_id=$rs[group_id]&n=$rs[id]&type=focus\" onclick=\"return confirm(\'您确认要删除?\');\">[删除]</a>|<a href=\"./index.php?m=system&s=flashoptions&a=edit&group_id=$rs[group_id]&n=$rs[id]&type=focus\">[编辑]</a>"');
		$flash_group->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
	}
	else
	{	
		$request['title'] = trim($request['title']);
		if(empty($request['title']))
		{
			echo "<script language='javascript'>window.alert('焦点图名称不能为空!');window.history.go(-1);</script>";
			exit;
		}	
		$flash_group = new flash_group();
		$flash_group->id=$request['group_id'];
		$flash_group->get_request($request);
		$flash_group->type = "focus";
		$flash_group->dtTime = date("Y-m-d H:i:s");
		
		$fromUrl = ABSPATH."inc/js/myfocus/pattern/".$request['pattern'];
		$toUrl = get_abs_skin_root()."res/plug-in/myfocus/";
		
		if(!is_dir($toUrl.'pattern/img/'))
		{
			mkdir($toUrl.'pattern/img/',0666,true);			
		}
		if(!is_file($toUrl.'pattern/'.$request['pattern'].'.css'))
		{
			copy(ABSPATH."inc/js/myfocus/myfocus-2.0.4.min.js",$toUrl."myfocus-2.0.4.min.js");
			copy(ABSPATH."inc/js/myfocus/pattern/".$request['pattern'].'.css',$toUrl."pattern/".$request['pattern'].".css");
			copy(ABSPATH."inc/js/myfocus/pattern/".$request['pattern'].'.js',$toUrl."pattern/".$request['pattern'].".js");
			if(is_dir(ABSPATH."inc/js/myfocus/pattern/img/".$request['pattern']))
			{
				copy(ABSPATH."inc/js/myfocus/pattern/img/loading.gif",$toUrl."pattern/img/loading.gif");
				dir_copy(ABSPATH."inc/js/myfocus/pattern/img/".$request['pattern'],$toUrl."pattern/img/".$request['pattern']);
			}
		}
		
		$flash_group->save();
		if(!empty($request['ordering']))
		{
			foreach($request['ordering'] as $id=>$o)
			{
				if(empty($o))$o=0;
				$sql ='UPDATE '.TB_PREFIX.'flash SET ordering='.$o.' WHERE id='.$id;
				$db->query($sql);
			}
		}
		redirect('?m=system&s=flashoptions&a=edit_group&group_id='.$request['group_id']);
	}
}
function destroy_group()
{
	global $db,$request;
	$sql = 'SELECT count(*) FROM `'.TB_PREFIX.'flash` WHERE group_id='.intval($request['group_id']);
	$count = $db->get_var($sql);
	if($count>0){
		exit('请先删除此flash 下的数据');
	}
	
	$sql = 'DELETE FROM `'.TB_PREFIX.'flash_group` WHERE id='.intval($request['group_id']);
	if($db->query($sql))
	redirect('?m=system&s=flashoptions');
	else
	{
		echo '删除失败！';
	}
}
function user_flash()
{
	global $db,$request;	
	$group_id=intval($request['group_id']);
	
	$sql ='UPDATE '.TB_PREFIX.'flash_group SET sign=1 WHERE id='.$group_id;
	$db->query($sql);
	$sql ='UPDATE '.TB_PREFIX.'flash_group SET sign=0 WHERE id not in('.$group_id.')';
	$db->query($sql);
	redirect('?m=system&s=flashoptions');
}
?>