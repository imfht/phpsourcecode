<link href="/admini/views/webmap/css/tree.css" type="text/css" rel="stylesheet">
<style type="text/css">
#ltreemenudemo {border: #ccc 1px solid; padding: 3px;  margin: 3px; width:500px;float:left;}
#infoBox {	border: #ccc 1px solid; padding: 10px;   width: 400px; line-height: 150%;  position: absolute; top: 40px; left: 450px;}
.admintb p {	margin: 0px; padding:5px 0 5px 18px; width:98%; float:left;}
#ltreemenudemo p a {color: #00f;}
#ltreemenudemo p a:visited {color: #00f;}
</style>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td width="50%"><h3>频道及栏目管理：</h3></td>
    <td width="50%">
	</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	 <p><input onclick=lTree.setAll(0); type="button" value="全部关闭" class="creatsb">&nbsp;<input onclick=lTree.setAll(1); type="button" value="全部展开" class="creatsb"></p>
<!--ltreemenu Start:-->
<div class="ltreemenu" id="ltreemenudemo">
	<?php 
$sql="SELECT *,(SELECT count(id) FROM `".TB_PREFIX."menu` b WHERE b.parentId=a.id ) hassub  FROM ".TB_PREFIX."menu a WHERE a.isHidden=0 order by  hassub  asc ";
$tempmenus=$db->get_results($sql);
if(!empty($tempmenus))
{
	//print_r($tempmenus);
	echo '<dl>'."\r\n";
	echo "\t".'<dd><a href="#">webmap</a>'."\r\n";
	echo "\t".'<dl>'."\r\n";
	$menuRoot = get_root_menus($tempmenus,0);
	//print_r($menuRoot);
	if(!empty($menuRoot))
	{
		foreach($menuRoot as $o)
		{
			if(!findChilds($o,$tempmenus))
			echo "\t\t".'<dt><a href="#">'.$o->title.'</a></dt>'."\r\n";
		}
	}
	echo "\t".'</dl>'."\r\n";
	echo "\t".'</dd>'."\r\n";
	echo '</dl>'."\r\n";
}

function get_root_menus($menuArr,$deep)
{
	if(!empty($menuArr))
	{
		$tempArr = array();
		foreach($menuArr as $key=>$o)
		{
			if($o->deep == $deep)
			{
				$tempArr[] = $o;
			}
		}
		return $tempArr;
	}	
}
function findChilds($inputMenu,$menuArr)
{
	$tempArr=array();
	if(!empty($menuArr))
	{
		foreach($menuArr as $o)
		{
			if($o->parentId == $inputMenu->id)
			{
				$tempArr[]=$o;
			}
		}
	}
	if(count($tempArr)>0)
	{
		if(URLREWRITE){
			if(intval($inputMenu->deep)==0)
			{
				echo "\t\t".outputspaces($inputMenu->deep).'<dd><a href="/'.$inputMenu->menuName.'/">'.$inputMenu->title.'</a>'."\r\n"; 
				echo "\t\t".outputspaces($inputMenu->deep).'<dl>'."\r\n";
			}
			foreach($tempArr as $o)
			{
				if(intval($o->hassub)>0)
				{
					echo "\t\t".outputspaces($o->deep).'<dd><a href="/'.$o->menuName.'/">'.$o->title.'</a>'."\r\n"; 
					echo "\t\t".outputspaces($o->deep).'<dl>'."\r\n";
					findChilds($o,$menuArr);
					echo "\t\t".outputspaces(intval($o->deep)).'</dl>'."\r\n";
					echo "\t\t".outputspaces(intval($o->deep)).'</dd>'."\r\n";
				}
				else
				echo "\t\t".outputspaces($o->deep).'<dt><a href="/'.$o->menuName.'/">'.$o->title.'</a></dt>'."\r\n";
				
			}
			if(intval($inputMenu->deep)==0)
			{
				echo "\t\t".outputspaces(intval($inputMenu->deep)-1).'</dl>'."\r\n";
				echo "\t\t".outputspaces(intval($inputMenu->deep)-1).'</dd>'."\r\n";
			}
			
		}else{
			if(intval($inputMenu->deep)==0)
			{
				echo "\t\t".outputspaces($inputMenu->deep).'<dd><a href="/?p='.$inputMenu->id.'">'.$inputMenu->title.'</a>'."\r\n"; 
				echo "\t\t".outputspaces($inputMenu->deep).'<dl>'."\r\n";
			}
			foreach($tempArr as $o)
			{
				if(intval($o->hassub)>0)
				{
					echo "\t\t".outputspaces($o->deep).'<dd><a href="/?p='.$o->id.'">'.$o->title.'</a>'."\r\n"; 
					echo "\t\t".outputspaces($o->deep).'<dl>'."\r\n";
					findChilds($o,$menuArr);
					echo "\t\t".outputspaces(intval($o->deep)).'</dl>'."\r\n";
					echo "\t\t".outputspaces(intval($o->deep)).'</dd>'."\r\n";
				}
				else
				echo "\t\t".outputspaces($o->deep).'<dt><a href="/?p='.$o->id.'">'.$o->title.'</a></dt>'."\r\n";
			}
			if(intval($inputMenu->deep)==0)
			{
				echo "\t\t".outputspaces(intval($inputMenu->deep)-1).'</dl>'."\r\n";
				echo "\t\t".outputspaces(intval($inputMenu->deep)-1).'</dd>'."\r\n";
			}
		}
		return true;
	}
	else
	{
		return false;
	}
}
function outputspaces($deep)
{
	$tempStr="";
	for($i=-1;$i<$deep;$i++)
	{
		if($i==($deep-1))
		$tempStr.="";
		else
		$tempStr.="\t";
	}
	return $tempStr;
}
	?>
</div>
<!--ltreemenuDEMO End-->
<script src="/admini/views/webmap/js/tree.packed.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
var lTree = new lTREE();
var t=new Date();
lTree.init({
	id		: "ltreemenudemo",
	path	: "dl dd",
	classClosed: "closed",
	openAll	: false
});
-->
</script>
			
	  </td>
  </tr>
</table>