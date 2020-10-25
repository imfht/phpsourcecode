<?php
/**
	产品模块
*/
if(strpos($_SERVER['REQUEST_URI'],"page")){
	$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"page")-1);
}
if(empty($_GET['sid']) && empty($_GET['cat'])){	?>
<ul style="margin:0;padding:0;display:table;width:100%;">
<?php
$sql="select * from ".DB_PREFIX."product_sub order by sort desc";
$query=$db->query($sql);
$num_all=$query->rowCount();
$page_size=3;
$page_count=ceil($num_all/$page_size);
$offset=$page_size*intval(!empty($_GET['page'])?($_GET['page']-1):0);
$sql="select * from ".DB_PREFIX."product_sub order by sort desc limit ".$offset." , ".$page_size;
$query=$db->query($sql);
$num=$query->rowCount();
if($num>0){
	while($arr=$query->fetch()){
?>
	<li style="width:200px;height:150px;float:left;list-style-type:none;text-align:center;margin-left:7px;margin-bottom:20px;">
		<a href="
			<?php
			echo $_SERVER['REQUEST_URI']."/sid/".$arr['id'];
			?>
			">
			<img style="width:160px;" src="<?php echo URL; ?>/admin/<?php  echo $arr['pic'];?>" border="0">
			<div style="text-align:center;height:20px;"><?php echo $arr['name']; ?></div>
		</a>
	</li>
<?php		
	}
}
?>
</ul>
<div style="text-align:center"><a href="<?php
	if(intval($_GET['page'])>1){
	echo $_SERVER['REQUEST_URI']."/page/".($_GET['page']-1);} ?>
	">上一页</a>&nbsp;<a href="<?php
		if((intval($_GET['page'])<$page_count) && ($num_all>$page_size)){
	echo $_SERVER['REQUEST_URI']."/page/".((empty($_GET['page'])?1:$_GET['page'])+1);} ?>
		">下一页</a>，<?php if(empty($page_count)){echo '0';}else{echo empty($_GET['page'])?1:intval($_GET['page']);} ?> / <?php echo $page_count; ?></div>
<?php	}elseif(!empty($_GET['sid'])){
$sql="select * from ".DB_PREFIX."product_sub where id=".intval($_GET['sid']);
$query=$db->query($sql);
$num=$query->rowCount();
if($num>0){
	$arr=$query->fetch();
	if (ini_get('magic_quotes_gpc')){
    		$pro_desc=stripslashes(htmlspecialchars_decode($arr['description']));
    	}else{
    		$pro_desc=htmlspecialchars_decode($arr['description']);
    	}
	echo "<p>".$pro_desc."</p>";
	}
}elseif(!empty($_GET['cat'])){
?>
<ul style="margin:0;padding:0;display:table;">
<?php
$sql="select * from ".DB_PREFIX."product_sub where category=".intval($_GET['cat'])." order by sort desc";
$query=$db->query($sql);
$num_all=$query->rowCount();
$page_size=3;
$page_count=ceil($num_all/$page_size);
$offset=$page_size*intval(!empty($_GET['page'])?($_GET['page']-1):0);
$sql="select * from ".DB_PREFIX."product_sub where category=".intval($_GET['cat'])." order by sort desc limit ".$offset." , ".$page_size;
$query=$db->query($sql);
$num=$query->rowCount();
if($num>0){
	while($arr=$query->fetch()){
?>
	<li style="width:200px;height:150px;float:left;list-style-type:none;text-align:center;margin-left:7px;margin-bottom:20px;">
		<a href="
			<?php
			echo $_SERVER['REQUEST_URI']."/sid/".$arr['id'];
			?>
			">
			<img style="width:160px;" src="<?php echo URL; ?>/admin/<?php  echo $arr['pic'];?>" border="0">
			<div style="text-align:center;height:20px;"><?php echo $arr['name']; ?></div>
		</a>
	</li>
<?php		
	}
  }	?>
 </ul>
 <div style="text-align:center"><a href="<?php
	if(intval($_GET['page'])>1){
	echo $_SERVER['REQUEST_URI']."/page/".($_GET['page']-1);}?>
	">上一页</a>&nbsp;<a href="<?php
		if((intval($_GET['page'])<$page_count) && ($num_all>$page_size)){
	echo $_SERVER['REQUEST_URI']."/page/".((empty($_GET['page'])?1:$_GET['page'])+1);}?>
		">下一页</a>，<?php if(empty($page_count)){echo '0';}else{echo empty($_GET['page'])?1:intval($_GET['page']);} ?> / <?php echo $page_count; ?></div>
<?php }else{}	?>