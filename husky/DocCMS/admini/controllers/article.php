<?php
function index()
{
	global $article,$db,$request,$i,$count;

	$count = $db->get_var("SELECT count(*) FROM ".TB_PREFIX."article WHERE channelId=$request[p]");

	if($request[i])
	{
		$i = $request[i];
		$article = $db->get_row("SELECT * FROM ".TB_PREFIX."article WHERE channelId=$request[p] AND id=$request[i] order by pageId desc ");
	}
	else
	{

		$article = $db->get_row("SELECT * FROM ".TB_PREFIX."article WHERE channelId=$request[p] order by pageId");
	}

}
function update()
{
	global $request,$db;
	$article = new article();
	if(empty($request['i']))
	{
		if($article->get_var("SELECT count(*) FROM ".TB_PREFIX."article WHERE channelId=$request[p]")==0)
		$article->addnew();
		else
		{
			$art = $db->get_row("SELECT * FROM ".TB_PREFIX."article WHERE channelId=$request[p] ORDER BY pageId ASC");
			$article->id=$art->id;
		}
	}
	else
	$article->id		=$request['i'];
	$aid=$article->id;
	$article->channelId	=$request['p'];
	
	if(!empty($_FILES['uploadfile'])&&empty($request['originalPic']))
	{
		$sql = "SELECT * FROM ".TB_PREFIX."article WHERE channelId=$request[p] AND pageId=$i";
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
		$article->originalPic = UPLOADPATH.$fileName;
		$paint = new Paint($article->originalPic);
		$article-> indexPic= $paint->Resize(articleWidth,articleHight,'i_');
	}
	$article->title         = $request['title'];
	$article->keywords 	    = $request['keywords'];
	$article->description 	= $request['description'];
	$article->content    	= $request['content'];
	$article->dtTime 	    = date('Y-m-d H:i:s');
	$article->pageId 	    = intval($request['pageId']);
	$article->save();

	redirect("./index.php?p=$request[p]&i=$aid");

}
function addarticle()
{
}

function newarticle()
{
	global $request,$db;
	$count = $db->get_var("SELECT count(*) FROM ".TB_PREFIX."article WHERE channelId=$request[p]");
	$i = $count;
	$article = new article();
	$article->addnew();

	if(!empty($_FILES['uploadfile'])&&empty($request['originalPic']))
	{
		$sql = "SELECT * FROM ".TB_PREFIX."article WHERE channelId=$request[p] AND pageId=$i";
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
		$article->originalPic = UPLOADPATH.$fileName;
		$paint = new Paint($article->originalPic);
		$article-> indexPic= $paint->Resize(articleWidth,articleHight,'i_');
	}
	$article->channelId=$request['p'];
	$article->title      = $request['title'];
	$article->keywords 	        = $request['keywords'];
	$article->description 	= $request['description'];
	$article->content    	= $request['content'];
	$article->dtTime 	    = date('Y-m-d H:i:s');
	$article->pageId        = $i;
	$article->save();
	//数据更新后提交到搜索引擎
	docPing($request['p'],mysql_insert_id());
	
	redirect_to($request['p'],'index');
}
function destroy()
{
	global $db,$request;
	if(!empty($request['i']))
	{
		$sql = 'SELECT * FROM '.TB_PREFIX.'article WHERE  id ='.$request['i'];
		$row = $db->get_row($sql);
		if(!empty($row->originalPic))
		{
				@unlink(ABSPATH.$row->originalPic);
				@unlink(ABSPATH.$row->indexPic);
		}
		$sql='DELETE FROM '.TB_PREFIX.'article WHERE id ='.$request['i'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else {
			echo '删除失败';
		}
	}
	else {
		$sql='DELETE FROM '.TB_PREFIX.'article WHERE pageId =0 AND channelId ='.$request['p'].' LIMIT 1';
		$db->query($sql);
		redirect_to($request['p']);
	}
}

function get_article_page($channel_id)
{
	global $db;
	$sql = "SELECT * FROM ".TB_PREFIX."article WHERE channelId=$channel_id order by pageId";
	$art_page = $db->get_results($sql);
	if($art_page)
	{
		$i=1;
		foreach ($art_page as $o)
		{
		?>
		<li><a href="./index.php?a=destroy&p=<?php echo $channel_id ?>&i=<?php echo $o->id ?>" class="delete">删除</a><a href="./index.php?p=<?php echo $channel_id ?>&i=<?php echo $o->id ?>" class="artno"><?php echo '('.$i.')'?></a><a href="./index.php?p=<?php echo $channel_id ?>&i=<?php echo $o->id ?>" class="arttitle"><?php echo $o->title ?></a></li>
		<?php
		$i++;
		}
	}
}
?>