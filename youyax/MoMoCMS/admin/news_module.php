<?php
/**
	新闻模块
*/
if(strpos($_SERVER['REQUEST_URI'],"page")){
	$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"page")-1);
}
if(empty($_GET['nid']) && empty($_GET['ncat'])){	?>
<ul class="news_ul" style="margin:0;padding:0;display:table;width:100%;">
<?php
$sql="select * from ".DB_PREFIX."pages where isNews=1 order by sort desc";
$query=$db->query($sql);
$num_all=$query->rowCount();
$page_size=3;
$page_count=ceil($num_all/$page_size);
$offset=$page_size*intval(!empty($_GET['page'])?($_GET['page']-1):0);
$sql="select * from ".DB_PREFIX."pages where isNews=1 order by time desc,sort desc limit ".$offset." , ".$page_size;
$query=$db->query($sql);
$num=$query->rowCount();
if($num>0){
	while($arr=$query->fetch()){
		$nurl= $_SERVER['REQUEST_URI']."/nid/".$arr['id'];
		echo '<li style="list-style-type:none;width:100%;height:30px;line-height:30px;"><div style="width:60%;float:left;text-align:left;"><a onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" href="'.$nurl.'">'.$arr['title'].'</a></div><div style="width:30%;float:right;text-align:right;color:#666;">'.date('Y-m-d H:i:s',$arr['time']).'</div></li>';	
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
<?php
}else if(empty($_GET['nid']) && !empty($_GET['ncat'])){	?>
<ul class="news_ul" style="margin:0;padding:0;display:table;width:100%;">
<?php
$sql="select * from ".DB_PREFIX."pages where isNews=1 and news_cat='".intval($_GET['ncat'])."' order by sort desc";
$query=$db->query($sql);
$num_all=$query->rowCount();
$page_size=3;
$page_count=ceil($num_all/$page_size);
$offset=$page_size*intval(!empty($_GET['page'])?($_GET['page']-1):0);
$sql="select * from ".DB_PREFIX."pages where isNews=1 and news_cat='".intval($_GET['ncat'])."' order by time desc,sort desc limit ".$offset." , ".$page_size;
$query=$db->query($sql);
$num=$query->rowCount();
if($num>0){
	while($arr=$query->fetch()){
		$nurl= $_SERVER['REQUEST_URI']."/nid/".$arr['id'];
		echo '<li style="list-style-type:none;width:100%;height:30px;line-height:30px;"><div style="width:60%;float:left;text-align:left;"><a onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" href="'.$nurl.'">'.$arr['title'].'</a></div><div style="width:30%;float:right;text-align:right;color:#666;">'.date('Y-m-d H:i:s',$arr['time']).'</div></li>';	
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
<?php
}else{
	$sql="select * from ".DB_PREFIX."pages where id=".intval($_GET['nid']);
	$query=$db->query($sql);
	$num=$query->rowCount();
	if($num>0){
	$arr=$query->fetch();
	echo "<div style='text-align:center'><p><span style='font-weight:bold;'>".$arr['author']."</span><span style='font-weight:normal;margin:0 10px;'>发表于</span><span style='font-weight:bold;'>".date('Y-m-d H:i:s',$arr['time'])."</span></p></div>";
	if (ini_get('magic_quotes_gpc')){
    		$content_news=stripslashes(htmlspecialchars_decode($arr['content']));
    	}else{
    		$content_news=htmlspecialchars_decode($arr['content']);
    	}
	echo "<p>".$content_news."</p>";
	}
}
?>