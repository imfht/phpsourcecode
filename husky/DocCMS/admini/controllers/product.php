<?php 
function temp_submenu($id=0){
  global $menus,$subs;  
  if(!isset($subs[$id])) return ; //没有子类,返回空;
  		   
   foreach($subs[$id] as $sid){  
	  $result.=','.$menus[$sid]['id'].temp_submenu($sid);  //递归	
   } 
   return $result; 
}	
function index()
{
	global $product,$db,$request,$menus,$subs,$customs;
	if($_POST)   //判断是否搜索
	{	  
		$_SESSION[TB_PREFIX.'keyword'] = $request['keyword'];
		!checkSqlStr($request['keyword'])? $request['keyword'] = $request['keyword'] : exit('非法字符');
		$sql = "SELECT * FROM `".TB_PREFIX."product` WHERE title LIKE '%".$request['keyword']."%' AND ( `channelId` IN(".$request['p'].temp_submenu($request['p']).") OR INSTR(REPLACE(CONCAT(\"'\",categoryId,\"'\"),\",\",\"','\"),\"'{$request['p']}'\")>0)";
	}
	else if($_SESSION[TB_PREFIX.'keyword'] && $request['mdtp']) //判断是否为分页搜索
	{
		$request['keyword'] = $_SESSION[TB_PREFIX.'keyword'];		
		$sql = "SELECT * FROM `".TB_PREFIX."product` WHERE title LIKE '%".$request['keyword']."%' AND (`channelId` IN(".$request['p'].temp_submenu($request['p']).") OR INSTR(REPLACE(CONCAT(\"'\",categoryId,\"'\"),\",\",\"','\"),\"'{$request['p']}'\")>0)";
	}
	else
	{
		$_SESSION[TB_PREFIX.'keyword'] = '';
		$sql = "SELECT * FROM `".TB_PREFIX."product` WHERE `channelId` IN(".$request['p'].temp_submenu($request['p']).") OR INSTR(REPLACE(CONCAT(\"'\",categoryId,\"'\"),\",\",\"','\"),\"'{$request['p']}'\")>0";
	}
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,12);
	
	$url=array('delete'=>'./index.php?p='.$request['p'].'&a=deleteAll','move'=>'./index.php?p='.$request['p'].'&a=move');
	$sql = "SELECT * FROM `".TB_PREFIX."menu` WHERE `type` = 'product' AND id <> ".$request['p'];
	$move_options = $db->get_results($sql);
	
	$custom = explode('@',$customs['field']);  //获取自定义字段
	
	$product = new DataTable($sb,'产品列表页面',true,$url,$move_options);
	$product->add_col('ID','id','db',50,'"$rs[id]"');
	$product->add_col('缩略图','smallPic','db',100,'get_pic($rs[smallPic])');
	$product->add_col('产品名称','title','db',200,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');

	$custom[0]?$product->add_col($custom[0],'spec','db',100,'expl($rs[spec])'):'';
	$product->add_col('市场价','sellingPrice','db',100,'"$rs[sellingPrice]"');
	$product->add_col('优惠价','preferPrice','db',100,'"$rs[preferPrice]"');
	$product->add_col('隶属类别','tip','text',200,'product_tip($rs[channelId])');
	$product->add_col('操作','edit','text',120,'product_nav($rs[channelId],$rs[id])');

	$product->add_col('排序[降序]','ordering','text',70,'"<input name=\"ordering[$rs[id]]\" onkeypress=\"return checkNumber(event)\" type=\"text\" value=\"$rs[ordering]\" class=\"txt\" size=\"2\" />"');	
}
function expl($v)
{
	$spec  = explode('<|@|>',$v);
	
	return $spec[0]?$spec[0]:' ';
}
function get_pic($pic)
{
	return '<img src="'.ispic($pic).'" width="50" height="35">';
}
function product_tip($channelId){
	global $menus;
	return $menus[$channelId]['title'];
}
function product_nav($p,$n){
	$title=!is_numeric($categoryId)?'此产品仍隶属其它分类,':'';
	if($c!='0')
	return '<a href="./index.php?a=destroy&p='.$p.'&n='.$n.'" title="'.$title.'" onclick="return confirm(\'您确认要删除该产品?'.$title.'一旦删除，将不可恢复。\');">[删除]</a>
			|<a href="./index.php?a=edit&p='.$p.'&n='.$n.'">[修改]</a>';
	else
	return '<a href="./index.php?a=destroy&p='.$p.'&n='.$n.'" title="'.$title.'" onclick="return confirm(\'您确认要删除该产品?'.$title.'一旦删除，将不可恢复。\');">[删除]</a>
			|<a href="./index.php?a=edit&p='.$p.'&n='.$n.'">[修改]</a>';
}
function create()
{
	global $request;
	if($_POST)
	{	
		$product = new product();
		$product->addnew();
		$product->get_request($request);
		$product->dtTime = date("Y-m-d H:i:s");
		$product->channelId = $request['p'];
		if(isset($request['category']))
		$product->categoryId=@implode(',',$request['category']);
		
		/***************产品自定义字段数据处理   Start******************/

		$product->spec = @implode('<|@|>',$request['spec']);
		
		$product->content = @implode('<|@|>',$request['content']);	
			
		/***************产品自定义字段数据处理   End******************/
			
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
				$tempSmall[$i]  = $paint->Resize(productSmallPicWidth,productSmallPicHight,'s_');
				$tempMiddle[$i] = $paint->Resize(productMiddlePicWidth,productMiddlePicHight,'m_');
				$tempIndex[$i]  = $paint->Resize(productWidth,productHight,'i_');
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
		
		$product->originalPic  = @implode('|',array_filter($tempOriginal));
		$product->middlePic    = @implode('|',array_filter($tempMiddle));
		$product->smallPic     = @implode('|',array_filter($tempSmall));
		$product->indexPic     = @implode('|',array_filter($tempIndex));
		
		$product->hassplitpages=(strpos($request['content'],'{#page#}')!==false)?'1':'0';
		
		if($product->save()!==false)
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
	global $db,$request,$product;
	$id = $request['n'];
	if(!$_POST)
	{
		$sql = "SELECT * FROM ".TB_PREFIX."product WHERE id='$id'";
		$product = $db->get_row($sql);
	}
	else
	{	
	    $id = $request['n'];
		$sql = "SELECT * FROM ".TB_PREFIX."product WHERE id='$id'";
		$row = $db->get_row($sql);			
		if($row)
		{  /*获取数据库中的图片原始数据  分解成图片数据数组*/
			$tempOriginal = @explode('|',$row->originalPic);
			$tempMiddle	  = @explode('|',$row->middlePic);
			$tempSmall 	  = @explode('|',$row->smallPic);
			$tempIndex 	  = @explode('|',$row->indexPic);
		}	
		
		$product = new product();
		$product->get_request($request);
		$product->id=$id;
		$product->channelId = $request['p'];
		$product->dtTime = date("Y-m-d H:i:s");
		if(isset($request['category']))
		$product->categoryId=@implode(',',$request['category']);	
		
		/***************产品自定义字段数据处理   Start******************/

		$product->spec = @implode('<|@|>',$request['spec']);
		
		$product->content = @implode('<|@|>',$request['content']);	
			
		/***************产品自定义字段数据处理   End******************/
		
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
				$tempSmall[$i]  = $paint->Resize(productSmallPicWidth,productSmallPicHight,'s_');
				$tempMiddle[$i] = $paint->Resize(productMiddlePicWidth,productMiddlePicHight,'m_');
				$tempIndex[$i]  = $paint->Resize(productWidth,productHight,'i_');
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
		$product->originalPic  = @implode('|',array_filter($tempOriginal));
		$product->middlePic    = @implode('|',array_filter($tempMiddle));
		$product->smallPic     = @implode('|',array_filter($tempSmall));
		$product->indexPic 	   = @implode('|',array_filter($tempIndex));
		
		$product->hassplitpages=(strpos($request['content'],'{#page#}')!==false)?'1':'0';
		
		if($product->save())
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
		$sql = "SELECT * FROM ".TB_PREFIX."product WHERE id='$id' LIMIT 1";
		$row = $db->get_row($sql);
		if($row)
		{
			$originalPic  = @explode('|',$row->originalPic);
			$middlePic    = @explode('|',$row->middlePic);
			$smallPic     = @explode('|',$row->smallPic);
			$indexPic = explode('|',$row->indexPic);
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
		$sql='DELETE FROM '.TB_PREFIX.'product WHERE id='.$request['n'].' LIMIT 1';
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
		$sql ='UPDATE '.TB_PREFIX.'product SET ordering='.intval($value).' WHERE id='.$key;
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
		$sql = "SELECT * FROM ".TB_PREFIX."product WHERE id=$value LIMIT 1";
		$row = $db->get_row($sql);
		if($row)
		{
			if(is_file(ABSPATH.$row->originalPic))
			{
				@unlink(ABSPATH.$row->originalPic);
				@unlink(ABSPATH.$row->middlePic);
				@unlink(ABSPATH.$row->smallPic);
				@unlink(ABSPATH.$row->indexPic);
			}
		}
		//在此删除路径
		$sql="DELETE FROM ".TB_PREFIX."product WHERE id=$value LIMIT 1";
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
		$sql = "UPDATE ".TB_PREFIX."product SET channelId=".$move_cate." , categoryId = ''  WHERE id=$value LIMIT 1";
		$db->query($sql);
	}
	redirect_to($request['p'],'index');
}

function manageorders(){
	global $db,$request,$product;
	$sb = new sqlbuilder('mdt',"SELECT * FROM `".TB_PREFIX."product_order` ",'id desc',$db,20,true);
	$product = new DataTable($sb,'订单列表页面',false);
	$product->add_col('序号','id','db',40,'"$rs[id]"');
	$product->add_col('订单号','orderId','db',200,'"$rs[orderId]"');
	$product->add_col('姓名','customer','db',70,'"$rs[customer]"');
	$product->add_col('联系电话','m_tel','db',120,'"$rs[m_tel]"');
	$product->add_col('日期','dtTime','db',200,'"$rs[dtTime]"');
	
	$product->add_col('地址','address','db',0,'"$rs[address]"');
	$product->add_col('付款状态','ispay','db',200,'ispay($rs[ispay])');
	$product->add_col('订单状态','edit','text',85,'!$rs[stauts]?"未处理":($rs[stauts]==1?"<strong>已处理</strong>":"已取消");');
	
	$product->add_col('详细','ordering','text',70,'"<a href=\"index.php?a=lookorder&p='.$request['p'].'&id=$rs[id]\">查看</a>"');
	
}
function ispay($p)
{
	if($p==1)
	return "已付款";
	else if($p==9)
	return "货到付款";
	else
	return "未付款";
}
function lookorder(){}
function cancelorder(){
	global $db,$request;
	$sql="UPDATE `".TB_PREFIX."product_order` SET `stauts` = '2' WHERE `id` =".intval($request['id'])." LIMIT 1 ;";
	$db->query($sql);
	redirect_to($request['p'],'manageorders');
}
function dealorder(){
	global $db,$request;
	$sql="UPDATE `".TB_PREFIX."product_order` SET `stauts` = '1' , `remark`='".$request['remark']."' WHERE `id` =".intval($request['id'])." LIMIT 1 ;";
	$db->query($sql);
	redirect_to($request['p'],'manageorders');
}
function delorder(){
	global $db,$request;
	$sql="DELETE FROM `".TB_PREFIX."product_order` WHERE `id` = ".intval($request['id'])." LIMIT 1";
	$db->query($sql);
	redirect_to($request['p'],'manageorders');
}
function index_category($id=0){
  global $menus,$subs,$request;  
  if(!isset($subs[$id])) return; //没有子类,返回空;	
				   
   foreach($subs[$id] as $sid){
	  for($i=0;$i<$menus[$sid]['deep']-1;$i++)$str.='&nbsp;&nbsp;&nbsp;&nbsp;';
	  
	  if($request['p'] == $menus[$sid]['id'])
	 	 echo '<tr style="background-color:#C5EAF5;height:20px;"><td><b><span>'.$str.'⊕<a href="./index.php?p='.$menus[$sid]['id'].'">'.$menus[$sid]['title'].'</a></span></b></td></tr>';
	  else
	 	 echo '<tr style="background-color:#f2f2f2;height:20px;"><td><b><span>'.$str.'⊕<a href="./index.php?p='.$menus[$sid]['id'].'">'.$menus[$sid]['title'].'</a></span></b></td></tr>';
		 
	  index_category($sid);  //递归
	  $str='';
   } 
}
function edit_category($id=0,$categoryId){
  global $menus,$subs,$request;  
  if(!isset($subs[$id])) return; //没有子类,返回空;	
				   
   foreach($subs[$id] as $sid){
	  if($menus[$sid]['type'] == "product")
	  {
		  $categoryAry = explode(',',$categoryId); 
		  for($i=0;$i<$menus[$sid]['deep']-1;$i++)$str.='&nbsp;&nbsp;&nbsp;&nbsp;';
		  
		  if(in_array($menus[$sid]['id'],$categoryAry) || $request['p'] == $menus[$sid]['id'])	
			 echo '<option value="'.$menus[$sid]['id'].'" selected="selected">'.$str.$menus[$sid]['title'].'</option>';
		  else
			 echo '<option value="'.$menus[$sid]['id'].'">'.$str.$menus[$sid]['title'].'</option>';
			 
		  edit_category($sid,$categoryId);  //递归
		  $str='';
	  }
   } 
}
?>