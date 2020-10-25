<?php 
function index()
{
	global $picture,$db,$request;
	if($_POST)
	{	
		$_SESSION[TB_PREFIX.'keyword'] = $request['keyword'];
		!checkSqlStr($request['keyword'])? $request['keyword'] = $request['keyword'] : exit('非法字符');
		$sql = 'SELECT * FROM `'.TB_PREFIX.'picture` WHERE title LIKE "%'.$request['keyword'].'%" AND channelId='.$request['p'];
	}
	else if($_SESSION[TB_PREFIX.'keyword'] && $request['mdtp'])
	{
		$request['keyword'] = $_SESSION[TB_PREFIX.'keyword'];		
		$sql = 'SELECT * FROM `'.TB_PREFIX.'picture` WHERE title LIKE "%'.$request['keyword'].'%" AND channelId='.$request['p'];
	}
	else
	{
		$_SESSION[TB_PREFIX.'keyword'] = '';
		$sql = 'SELECT * FROM `'.TB_PREFIX.'picture` WHERE channelId='.$request['p'];
	}	
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,12);
	
	$picture = new DataTable($sb,'图片列表页面');
	$picture->add_col('编号','id','db',40,'"$rs[id]"');
	$picture->add_col('主题','title','db',0,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');
	$picture->add_col('预览','preview','text',40,'"<a target=\"_blank\" href=\"../index.php?p=$rs[channelId]&a=view&r=$rs[id]\">预览</a>"');
	$picture->add_col('时间','dtTime','db',140,'');
	$picture->add_col('操作','edit','text',140,'"<a href=\"./index.php?a=destroy&p=$rs[channelId]&n=$rs[id]\" onclick=\"return confirm(\'您确认要删除该图片?一旦删除，将不可恢复。\');\">[删除]</a>|<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">[修改]</a>"');
	$picture->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');
}
function create()
{
	global $request;
	if($_POST)
	{
		$picture = new picture();
		$picture->addnew();
		$picture->get_request($request);
		$picture->dtTime = date("Y-m-d H:i:s");
		$picture->channelId = $request['p'];
			
		$tempOriginal = array();
		$tempSmall    = array();
		$tempMiddle   = array();
		$tempIndex    = array();	
        for($i=0;$i<count($_FILES['uploadfile']['name']);$i++)
		{   /*获取表单传递过来的数组数据，循环遍历数组中元素*/		
		    if(strlen($_FILES['uploadfile']['name'][$i])>0)
			{
				/*当前元素节点的图片执行上传*/
				$upload = new Upload();
				$fileName = $upload->SaveFile('uploadfile',true,$i);
				if(empty($fileName))echo $upload->showError();
				
				/*生成新的图片地址数据  将地址存入当前元素节点*/
				$tempOriginal[$i] =  UPLOADPATH.$fileName;
				$paint  = new Paint($tempOriginal[$i]);
				$tempSmall[$i]  = $paint->Resize(pictureSmallPicWidth,pictureSmallPicHight,'s_');
				$tempMiddle[$i] = $paint->Resize(pictureMiddlePicWidth,pictureMiddlePicHight,'m_');
				$tempIndex[$i]  = $paint->Resize(pictureWidth,pictureHight,'i_');
			}
			else
			{   /*当前数组元素节点没有图片上传数据  不作图片上传处理  直接将图片地址存入数据库*/
				if(empty($request['originalPic'][$i]))
				{ /*图片地址为空  清除当前元素节点的图片数据  且删除原图片*/
					$tempOriginal[$i] = '';
					$tempSmall[$i]    = '';
					$tempMiddle[$i]   = '';
					$tempIndex[$i]    = '';
				}
				else
				{  /*图片地址不为空  将地址存入当前元素节点*/
					$tempPic = explode('/',$request['originalPic'][$i]); //分割图片地址信息
					if($tempPic[0]=='http:')
					{  /*图片地址为远程地址  直接将数据不做处理  存入当前元素节点*/
						$tempOriginal[$i] = $request['originalPic'][$i];
						$tempSmall[$i]    = $request['originalPic'][$i];
						$tempMiddle[$i]   = $request['originalPic'][$i];
						$tempIndex[$i]    = $request['originalPic'][$i];
					}
					else
					{  /*图片地址不为远程地址  将数据处理后  存入当前元素节点*/
						$tempOriginal[$i] = '/'.$tempPic[1].'/'.$tempPic[2].'/'.$tempPic[3];
						$tempMiddle[$i]   = '/'.$tempPic[1].'/'.$tempPic[2].'/'.'m_'.$tempPic[3];
						$tempSmall[$i]    = '/'.$tempPic[1].'/'.$tempPic[2].'/'.'s_'.$tempPic[3];
						$tempIndex[$i]    = '/'.$tempPic[1].'/'.$tempPic[2].'/'.'i_'.$tempPic[3];
					}	
				}
			}
		}
		
		$picture->originalPic  = @implode('|',array_filter($tempOriginal));
		$picture->middlePic    = @implode('|',array_filter($tempMiddle));
		$picture->smallPic     = @implode('|',array_filter($tempSmall));
		$picture->indexPic = @implode('|',array_filter($tempIndex));
		
		$picture->hassplitpages=(strpos($request['content'],'{#page#}')!==false)?'1':'0';
		if($picture->save()!==false)
		{
			//数据更新后提交到搜索引擎
			docPing($request['p'],mysql_insert_id());
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
	global $db,$request,$picture;
	$id = $request['n'];
	if(!$_POST)
	{
		$sql = "SELECT * FROM ".TB_PREFIX."picture WHERE id='$id'";
		$picture = $db->get_row($sql);
	}
	else
	{
	    $id = $request['n'];
		$sql = "SELECT * FROM ".TB_PREFIX."picture WHERE id='$id'";
		$row = $db->get_row($sql);			
		if($row)
		{  /*获取数据库中的图片原始数据  分解成图片数据数组*/
			$tempOriginal = explode('|',$row->originalPic);
			$tempMiddle	  = explode('|',$row->middlePic);
			$tempSmall 	  = explode('|',$row->smallPic);
			$tempIndex 	  = explode('|',$row->indexPic);
		}	
		
		$picture = new picture();
		$picture->get_request($request);
		$picture->id=$id;
		$picture->channelId = $request['p'];
		$picture->dtTime = date("Y-m-d H:i:s");	
		for($i=0;$i<count($_FILES['uploadfile']['name']);$i++)
		{   /*获取表单传递过来的数组数据，循环遍历数组中元素*/

			if(strlen($_FILES['uploadfile']['name'][$i])>0)
			{  /*判断当前数组元素节点 是否有图片上传的数据*/
				if(is_file(ABSPATH.$tempOriginal[$i]))
				{   /*删除当前元素节点 的原始图片*/
					@unlink(ABSPATH.$tempOriginal[$i]);
					@unlink(ABSPATH.$tempMiddle[$i]);
					@unlink(ABSPATH.$tempSmall[$i]);
					@unlink(ABSPATH.$tempIndex[$i]);
				}
				/*当前元素节点的图片执行上传*/
				$upload = new Upload();
				$fileName = $upload->SaveFile('uploadfile',true,$i);
				if(empty($fileName))echo $upload->showError();
				
				/*生成新的图片地址数据  将地址存入当前元素节点*/
				$tempOriginal[$i] =  UPLOADPATH.$fileName;
				$paint  = new Paint($tempOriginal[$i]);

				$tempSmall[$i]  = $paint->Resize(pictureSmallPicWidth,pictureSmallPicHight,'s_');
				$tempMiddle[$i] = $paint->Resize(pictureMiddlePicWidth,pictureMiddlePicHight,'m_');
				$tempIndex[$i]  = $paint->Resize(pictureWidth,pictureHight,'i_');
			}
			else
			{   /*当前数组元素节点没有图片上传数据  不作图片上传处理  直接将图片地址存入数据库*/
				if(empty($request['originalPic'][$i]))
				{ /*图片地址为空  清除当前元素节点的图片数据  且删除原图片*/
				
					if(is_file(ABSPATH.$tempOriginal[$i]))
					{   /*删除当前元素节点 的原始图片*/
						@unlink(ABSPATH.$tempOriginal[$i]);
						@unlink(ABSPATH.$tempMiddle[$i]);
						@unlink(ABSPATH.$tempSmall[$i]);
						@unlink(ABSPATH.$tempIndex[$i]);
					}
					$tempOriginal[$i] = '';
					$tempSmall[$i]    = '';
					$tempMiddle[$i]   = '';
					$tempIndex[$i]    = '';					
				}
				else
				{  /*图片地址不为空  将地址存入当前元素节点*/
					$tempPic = explode('/',$request['originalPic'][$i]); //分割图片地址信息
					if($tempPic[0]=='http:')
					{  /*图片地址为远程地址  直接将数据不做处理  存入当前元素节点*/
						$tempOriginal[$i] = $request['originalPic'][$i];
						$tempSmall[$i]    = $request['originalPic'][$i];
						$tempMiddle[$i]   = $request['originalPic'][$i];
						$tempIndex[$i]    = $request['originalPic'][$i];
					}
					else
					{  /*图片地址不为远程地址  将数据处理后  存入当前元素节点*/
						$tempOriginal[$i] = '/'.$tempPic[1].'/'.$tempPic[2].'/'.$tempPic[3];
						$tempMiddle[$i]   = '/'.$tempPic[1].'/'.$tempPic[2].'/'.'m_'.$tempPic[3];
						$tempSmall[$i]    = '/'.$tempPic[1].'/'.$tempPic[2].'/'.'s_'.$tempPic[3];
						$tempIndex[$i]    = '/'.$tempPic[1].'/'.$tempPic[2].'/'.'i_'.$tempPic[3];
					}	
				}
			}
		}			
		
		$picture->originalPic  = implode('|',array_filter($tempOriginal));
		$picture->middlePic    = implode('|',array_filter($tempMiddle));
		$picture->smallPic     = implode('|',array_filter($tempSmall));
		$picture->indexPic = implode('|',array_filter($tempIndex));
		$picture->hassplitpages=(strpos($request['content'],'{#page#}')!==false)?'1':'0';
		if($picture->save())
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
		//先删除图片，再删除数据库路径
		$id = $request['n'];
		$sql = "SELECT * FROM ".TB_PREFIX."picture WHERE id='$id' LIMIT 1";
		$row = $db->get_row($sql);
		if($row)
		{
			$originalPic  = explode('|',$row->originalPic);
			$middlePic    = explode('|',$row->middlePic);
			$smallPic     = explode('|',$row->smallPic);
			$indexPic     = explode('|',$row->indexPic);
			for($i=0;$i<count($originalPic);$i++)
			{
				if(is_file(ABSPATH.$originalPic[$i]))
				{
					@unlink(ABSPATH.$originalPic[$i]);
					@unlink(ABSPATH.$middlePic[$i]);
					@unlink(ABSPATH.$smallPic[$i]);
					@unlink(ABSPATH.$indexPic[$i]);
				}
			}
		}
		//在此删除路径
		$sql='DELETE FROM '.TB_PREFIX.'picture WHERE id='.$request['n'].' LIMIT 1';
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
		$sql ='UPDATE '.TB_PREFIX.'picture SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}
?>