<?php
checkme(9);
function main_menu()
{
	global $db,$request;
	$sql='SELECT * FROM '.TB_PREFIX.'menu WHERE deep=0 and dtLanguage ="'.$request['l'].'" order by ordering';
	$menus=$db->get_results($sql);
	$request['cid'] = intval($request['cid']);
	if(!empty($menus))
	{
		foreach($menus as $menu)
		{
			if($request['cid'] == $menu->id)
			echo '<a href="?m=system&s=managechannel&cid='.$menu->id.'" class="nthover">'.$menu->title.'</a> ';
			else
			echo '<a href="?m=system&s=managechannel&cid='.$menu->id.'" class="nta">'.$menu->title.'</a>';
		}
	}	
	else
	{
		echo '<a href="?m=system&s=managechannel&cid=0&a=create">请添加网站主导航菜单</a>';
	}
}
function get_allmenus(){
	global $db;
	$sql="SELECT *,(SELECT COUNT(id) FROM `".TB_PREFIX."menu` b WHERE b.parentId=a.id ) hassub FROM `".TB_PREFIX."menu` a ORDER BY ordering ASC";
	return $db->get_results($sql,ARRAY_A);
}
function trace_sub_nodes($id){
	global $db;
	$all=get_allmenus();
	$temp=array($id);
	foreach($all as $v){
		foreach($temp as $o){
			foreach($all as $vv){
				if($o==$vv[parentId]){
					$arr[]=$vv;
				}
			}
		}
		foreach($temp as $o){
			array_shift($temp);
			foreach($all as $vv){
				if($o==$vv[parentId]){
					if($vv[hassub]){
						$temp[]=$vv[id];
					}
				}
			}
		}
	}
	//print_r($arr);
	return $arr;
}
function getFristChannelId()
{
	global $db,$request;
	$sql='SELECT id FROM '.TB_PREFIX.'menu WHERE deep=0 and dtLanguage ="'.$request['l'].'" order by ordering LIMIT 1';
	return $db->get_var($sql);
}

function  index()
{
	global $db,$request,$tempstr;
	$request['cid']=intval($request['cid']);
	$request['cid']=!$request['cid']?getFristChannelId():$request['cid'];
	if(empty($request['cid'])){
		string2file('<li><a href="?m=system&s=managechannel&cid=0&a=create">暂无网站频道，请添加网站频道</a></li>',ABSPATH.'/admini/'.$request['l'].'_nav.php');
	}else{
		string2file(admin_menu(),ABSPATH.'/admini/'.$request['l'].'_nav.php');
	
		$tempmenus=trace_sub_nodes($request['cid']);
		if(!empty($tempmenus))
		{
			foreach($tempmenus as $menu){
				$menus[$menu['id']]=$menu;
			}
			$stack = array($request['cid']);
			$n=0;
			while($stack) {
			  	$parentid = array_pop($stack);
			  	if($parentid!=$request['cid']) {
			  		$tempstr.='<li>'."\r\n";
			  		$tempstr.='<span class="tree"><span class="prefix">'.$menus[$parentid]['prefix'].'</span><span class="title">'.$menus[$parentid]['title'].'</span></span>'."\r\n";
			  		$tempstr.='<span class="menuid">'.$menus[$parentid]['id'].'</span>'."\r\n";
			  		$tempstr.='<span class="menuname">'.(!$menus[$parentid]['isExternalLinks']?$menus[$parentid]['menuName']:'无').'</span>'."\r\n";
			  		$tempstr.='<span class="type">'.(!$menus[$parentid]['isExternalLinks']?$menus[$parentid]['type']:'外链').'</span>'."\r\n";
			  		$tempstr.='<span  class="mod"><a href="./index.php?m=system&s=managechannel&a=edit&pid='.$request['cid'].'&cid='.$menus[$parentid]['id'].'&deep='.$menus[$parentid]['deep'].'">修改</a></span>'."\r\n";
			  		$tempstr.='<span  class="del"><a href="./index.php?m=system&s=managechannel&a=destroy&pid='.$request['cid'].'&cid='.$menus[$parentid]['id'].'" onclick="return confirm(\'您确认要删除此栏目菜单?一旦删除，此栏目将不可恢复。\')">删除</a></span>'."\r\n";
			  		if(!$menus[$parentid]['isExternalLinks']){
			  		$tempstr.='<span  class="create"><a href="./index.php?m=system&s=managechannel&a=create&pid='.$request['cid'].'&cid='.$menus[$parentid]['id'].'&deep='.$menus[$parentid]['deep'].'" style="color:#ff6600";>添加下级子菜单</a></span>'."\r\n";
			  		}else{
			  			$tempstr.='<span  class="create">&nbsp;</span>'."\r\n";
			  		}
			  		//留言、招聘、订单、网站地图
			  		$noComment=array('guestbook','jobs','order','webmap','user','rss');
			  		if(!in_array($menus[$parentid]['type'],$noComment)){
				  		if($menus[$parentid]['isComment']){
				  			$tempstr.='<span  class="commentoff"><a href="./index.php?m=system&s=managechannel&a=destroycomment&pid='.$request['cid'].'&cid='.$menus[$parentid]['id'].'" style="color:#ff6600;"> 关闭评论</a></span>'."\r\n";
				  		}else{
				  			$tempstr.='<span  class="commenton"><a href="./index.php?m=system&s=managechannel&a=createcomment&pid='.$request['cid'].'&cid='.$menus[$parentid]['id'].'" style="color:green;">开启评论</a></span>'."\r\n";
				  		}
			  		}else{
			  			$tempstr.='<span  class="comment">&nbsp;&nbsp;&nbsp;&nbsp;</span>'."\r\n";
			  		}
			  		$tempstr.='<span  class="ordering"><input type="text" size="2" class="pxtxt" value="'.$menus[$parentid]['ordering'].'" onkeypress="return checkNumber(event)" name="ordering['.$menus[$parentid]['id'].']"></span>'."\r\n";
			  		$tempstr.='</li>'."\r\n";
			  	}
			  	$substack = array();      
			  	foreach($menus as $id=>$menu) {            
			  		if($menu['parentId']==$parentid) {                 
			  			$menus[$id]['prefix'] = $menus[$parentid]['prefix'].'&nbsp;&nbsp;&nbsp;&nbsp;';                        
			  			array_push($substack,$id);       
			  		}      
			  	}      
			  	$substack = array_reverse($substack);      
			  	$stack = array_merge($stack,$substack);
			}
		}
	}
}
function create()
{
	global $db,$request;
	if($_POST)
	{
		$request['title'] = trim($request['title']);
		if(empty($request['title']))
		{
			echo "<script language='javascript'>window.alert('导航菜单名不能为空!');window.history.go(-1);</script>";
			exit;
		}
		else
		{
			if(empty($request['menuName'])&&intval($request['isExternalLinks'])==0)
			{
					echo "<script language='javascript'>window.alert('为了帮助您进行优化，请填写对应英文名!');window.history.go(-1);</script>";
					exit;
			}
			elseif(menuNameIsExist($request['menuName']) && intval($request['isExternalLinks'])==0)
			{
					echo "<script language='javascript'>window.alert('您填写的英文名已经被其它导航栏目占用。请更换其它英文名!');window.history.go(-1);</script>";
					exit;
			}else{
					$menu = new menu();
					$menu->addnew();
					$menu->get_request($request);
					$menu->parentId=empty($request['cid'])?0:$request['cid'];
					if(is_numeric($request['deep'])){
						$request['deep']+=1;
					}else{
						$request['deep']=0;
					}
					$menu->deep=$request['deep'];
					$menu->dtLanguage=$request['l'];
					if(intval($request['isTarget'])===0)$menu->isTarget='0';
					if(intval($request['isHidden'])===0)$menu->isHidden='0';
					if(intval($request['isComment'])===0)$menu->isComment='0';
					if(intval($request['level'])===0)$menu->level='0';
					if(intval($request['isExternalLinks'])===0)$menu->isExternalLinks='0';
					if(!empty($_FILES['uploadfile'])&& empty($request['originalPic']))
					{
						//删除旧缩略图
						$sql = "SELECT originalPic,smallPic FROM ".TB_PREFIX."menu WHERE id=".$request['cid'];
						$row = $db->get_row($sql);
						if(!empty($row->originalPic)){
							if(is_file(ABSPATH.$row->originalPic))
							{
								@unlink(ABSPATH.$row->originalPic);
								@unlink(ABSPATH.$row->smallPic);
							}
						}
						
						if($_FILES['uploadfile']['size']>0 && $_FILES['uploadfile']['size']<100000){
							$upload = new Upload();
							$fileName = $upload->SaveFile('uploadfile');
							$menu->originalPic = UPLOADPATH.$fileName;
							$paint = new Paint($menu->originalPic);
							$menu-> smallPic= $paint->Resize($request['width'],$request['hight'],'s_'); 
						}else{
							$menu->originalPic = '';
							$menu-> smallPic= '';
						}
					}
					$menu->save();
					if(!$request['pid']){
						redirect('index.php?m=system&s=managechannel&cid='.$menu->insert_id);
					}else{
						redirect('index.php?m=system&s=managechannel&cid='.$request['pid']);
					}
			}
		}
	}else{
		$sql='SELECT * FROM '.TB_PREFIX.'menu WHERE id='.$request['cid'];
		$menu_item = $db->get_row($sql);
		if($menu_item->isExternalLinks){
			echo "<script language='javascript'>window.alert('外链导航下不能再创建子导航菜单!');window.history.go(-1);</script>";
			exit;
		}
	}
}
function edit()
{
	global $db,$request,$menu_item;
	if(!$_POST)
	{
		$sql='SELECT * FROM '.TB_PREFIX.'menu WHERE id='.$request['cid'];
		$menu_item = $db->get_row($sql);
	}else{
		if(empty($request['title']))
		{
			echo "<script language='javascript'>window.alert('导航菜单名不能为空!');window.history.go(-1);</script>";
			exit;
		}
		else if(empty($request['type']))
		{
			echo "<script language='javascript'>window.alert('请选择栏目属性!');window.history.go(-1);</script>";
			exit;
		}
		else
		{
			if(empty($request['menuName'])&& intval($request['isExternalLinks'])==0)
			{
					echo "<script language='javascript'>window.alert('为了帮助您进行优化，请填写对应英文名!');window.history.go(-1);</script>";
					exit;
			}
			elseif(menuNameIsExist($request['menuName'],$request['cid']) && intval($request['isExternalLinks'])==0)
			{
					echo "<script language='javascript'>window.alert('您填写的英文名已经被其它导航栏目占用。请更换其它英文名!');window.history.go(-1);</script>";
					exit;
			}
			else
			{
				$menu = new menu();
				$menu->id=$request['cid'];
				if(intval($request['isTarget'])===0)$menu->isTarget='0';
				if(intval($request['isHidden'])===0)$menu->isHidden='0';
				if(intval($request['isComment'])===0)$menu->isComment='0';
				if(intval($request['level'])===0)$menu->level='0';
				if(intval($request['isExternalLinks'])===0)$menu->isExternalLinks='0';
				if(intval($request['ordering'])===0)$menu->ordering='0';				
				$menu->get_request($request);
				
				
				if(!empty($_FILES['uploadfile'])&& empty($request['originalPic']))
				{
					//删除旧缩略图
					$sql = "SELECT originalPic,smallPic FROM ".TB_PREFIX."menu WHERE id=".$request['cid'];
					$row = $db->get_row($sql);
					if(!empty($row->originalPic)){
						if(is_file(ABSPATH.$row->originalPic))
						{
							@unlink(ABSPATH.$row->originalPic);
							@unlink(ABSPATH.$row->smallPic);
						}
					}
					if($_FILES['uploadfile']['size']>0 && $_FILES['uploadfile']['size']<800000){
						$upload = new Upload();
						$fileName = $upload->SaveFile('uploadfile');
						$menu->originalPic = UPLOADPATH.$fileName;
						$paint = new Paint($menu->originalPic);
						$menu-> smallPic= $paint->Resize($request['width'],$request['hight'],'s_'); 
					}else{
						$menu->originalPic = '';
						$menu->smallPic= '';
					}
				}
				$menu->save();
				redirect('index.php?m=system&s=managechannel&cid='.$request['pid']);
			}
		}
	}
}
function destroy()
{
	global $db,$request;
	if(!empty($request['cid']))
	{
		$sql='SELECT count(*) FROM '.TB_PREFIX.'menu WHERE parentId='.$request['cid'];
		if($db->get_var($sql)>0){
			exit('请先删除其下附属子栏目菜单');
		}
		
		//删除旧缩略图
		$sql = "SELECT originalPic,smallPic FROM ".TB_PREFIX."menu WHERE id=".$request['cid'];
		$row = $db->get_row($sql);
		if(!empty($row->originalPic)){
			if(is_file(ABSPATH.$row->originalPic))
			{
				@unlink(ABSPATH.$row->originalPic);
				@unlink(ABSPATH.$row->smallPic);
			}
		}
		
	 	$sql='DELETE FROM '.TB_PREFIX.'menu WHERE id='.$request['cid'];
		if($db->query($sql)){
			if(!$request['pid'])
				redirect('index.php?m=system&s=managechannel');
			else 
				redirect('index.php?m=system&s=managechannel&cid='.$request['pid']);
		}else{
			echo '删除失败！';
		}
	}
}
function createcomment()
{
	global $db,$request;
	if(!empty($request['cid']))
	{
		$menu = new menu();
		$sql ='UPDATE '.TB_PREFIX.'menu SET isComment=1  WHERE id='.$request['cid'];
		if($db->query($sql))
		{
			redirect('index.php?m=system&s=managechannel&cid='.$request['pid']);
		}else {
			echo '评论添加失败！';
		}
	}
}
function destroycomment()
{
	global $db,$request;
	if(!empty($request['cid']))
	{
		$menu = new menu();
		$sql ='UPDATE '.TB_PREFIX.'menu SET isComment=0  WHERE id='.$request['cid'];
		if($db->query($sql))
		{
			redirect('index.php?m=system&s=managechannel&cid='.$request['pid']);
		}
		else {
			echo '评论删除失败！';
		}
	}
}
//-------------------------------------------
function admin_menu()
{
	global $db,$menus,$substr;

	if(!empty($menus))
	{	
		foreach ($menus as $menu)
		{
			if(!$menu['deep'])
			{
				$substr ='';
				admin_sub_menu($menu['id']);
				$tempstr.= "<li><a href='./index.php?p=".$menu['id']."'>".$menu['title']."</a>".$substr."</li>\r\n";
			}
		}
		return $tempstr;
	}
}
function admin_sub_menu($id=0)
{	
	global $menus,$subs,$substr;    			
	if(!isset($subs[$id])) return; //没有子类,返回空;

	$substr .= '<ul>';
	foreach($subs[$id] as $sid){
		$substr .="<li><a href='./index.php?p=".$menus[$sid]["id"]."'>".$menus[$sid]["title"].'</a>';
		admin_sub_menu($sid);
		$substr .="</li>\r\n";
	}
	$substr .= '</ul>';
}
function menuNameIsExist($menuName,$id=0)
{
	global $db;
	$sql="SELECT count(*) FROM ".TB_PREFIX."menu WHERE menuName='".$menuName."' AND id!={$id}";
	if(intval($db->get_var($sql)) > 0)
	return true;
	else
	return false;
}
function ordering()
{
	global $db,$request;
	$ordering = $request['ordering'];
	foreach($ordering as $key=>$value)
	{
		if(empty($value))$value=0;
		$sql ='UPDATE '.TB_PREFIX.'menu SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect('index.php?m=system&s=managechannel&cid='.$request['pid']);
}
function model_radio_group($name,$select='article')
{
	global $db;
	$temp_arr=$db->get_results('SELECT * FROM `'.TB_PREFIX.'models_reg` order by id');	
	foreach ($temp_arr as $o)
	{
		$selected=($select==$o->type)?' class="slected"':'';
		?>
        <label <?php echo $selected ?> value="<?php echo $o->$name ?>"><?php echo $o->model_name ?></label>
		<?php
	}
}
function is_commit_at($isComment,$n,$type,$isExternalLinks)
{
	global $noComment;
	if($isComment)
	{
		if(!in_array($type,$noComment) && intval($isExternalLinks)==0)
		{
			return "|<a href=\"./index.php?m=system&s=managechannel&a=destroycomment&cid=$n\"><span style=\"color:#ff6600\">[关闭评论模块]</span></a>";
		}
	}
	else
	{
		if(!in_array($type,$noComment) && intval($isExternalLinks)==0)
		{
			return "|<a href=\"./index.php?m=system&s=managechannel&a=createcomment&cid=$n\"><span style=\"color:green\">[开启评论模块]</span></a>";
		}
	}
}

/*
*菜单中心 action 
*by  grysoft(狗头巫师) 
*
*/
function center()
{
}

/*******栏目读取**菜单树堆栈生成*****/
function menutree()
{
	global $db,$request;

	$sql="SELECT * FROM ".TB_PREFIX."menu WHERE dtLanguage ='".$request['l']."'  ORDER BY  ordering  ASC";	
	$tempmenus=$db->get_results($sql,ARRAY_A);
	if(!empty($tempmenus))
	{
		foreach($tempmenus as $menu){
			$menus[$menu['id']]=$menu;
		}
		$stack = array(0);
		while($stack) {
			$parentid = array_pop($stack); 
			if($parentid) {
				$v=(object)$menus[$parentid];	
				$temp=new menu();					
				$class = !$v->deep?' class="c"':'';
				echo '<li'.$class.'>';
				echo $temp->menu_list_select($v->id,$v->id,$tempmenus);				
			    echo $temp->menu_power_list_select('related_common['.$v->id.']',$v->related_common);	
				echo $temp->menu_list_radio($v->id,$v->isHidden);									
				echo '<font>'.$v->id.'</font>
				<input type="text" class="order" value="'.$v->ordering.'" name="ordering['.$v->id.']">'.$menus[$parentid]['prefix'].'
				<span class="yl"><input type="text" value="'.$v->title.'" name="menu['.$v->id.']"> </span>
				</li>';	
											
			}
			$substack = array();      
			foreach($menus as $id=>$menu) {            
				if($menu['parentId']==$parentid) {                 
				  	$menus[$id]['prefix'] = $menus[$parentid]['prefix'].'&nbsp;&nbsp;&nbsp;&nbsp;';                    array_push($substack,$id);   
				}      
			}      
			$substack = array_reverse($substack);      
			$stack = array_merge($stack,$substack);
		}
	}
}
/*******栏目操作**数据跟新*****/
function update_menu()
{
	global $db,$request;
	$okey = array_keys($request['ordering']);
	$mkey = array_keys($request['menu']);
	$rkey = array_keys($request['related_common']);
	$dkey = array_keys($request['radio']);

	for($i=0;$i<count($request['menu']);$i++)
	{
		if(empty($request['ordering'][$i]))$request['ordering'][$i]='0';
		
		$sql ='UPDATE '.TB_PREFIX.'menu SET ordering='.$request['ordering'][$okey[$i]].' , title ="'.$request['menu'][$mkey[$i]].'" , related_common ="'.$request['related_common'][$rkey[$i]].'" , isHidden ="'.$request['radio'][$dkey[$i]].'" WHERE id='.$okey[$i];
		
		$db->query($sql);
	}
	redirect('index.php?m=system&s=managechannel&a=center');
}

/*******栏目操作**移动*****/
function move()
{
	global $db,$request;
	if($request['move_to'])
	{
		$deep_to = $db->get_row('SELECT * FROM '.TB_PREFIX.'menu WHERE id = '.$request['move_to']);
	}
	else
	{
		$deep_to->deep = -1;
	}
	$sql = "UPDATE ".TB_PREFIX."menu SET parentId=".$request['move_to']." , deep =".($deep_to->deep+1)." WHERE id=".$request['id'];
	$db->query($sql);
	submenumove($request['id'],$deep_to->deep+1);	
	redirect('index.php?m=system&s=managechannel&a=center');
}

/*******子栏目操作**递归移动*****/
function submenumove($id=0,$deep=0){
    global $db,$menus,$subs;   
    if(!isset($subs[$id])) return; //没有子类,返回空;
      
     foreach($subs[$id] as $sid){
	    $sql = "UPDATE ".TB_PREFIX."menu SET  deep =".($deep+1)." WHERE id=".$menus[$sid]['id'];
	    $db->query($sql);
        submenumove($sid,$deep+1);//递归  
     }      
}
?>