<?php
require("./database.php");
$sql_main="select * from ".DB_PREFIX."pages where id=".intval($_GET['id']);
$query_main=$db->query($sql_main);
$arr_main=$query_main->fetch();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />
<title><?php 
	if(!empty($_GET['nd'])){$tmp_title=$_GET['nd'];}
	if(!empty($_GET['nid'])){$tmp_title=$_GET['nid'];}
	if(!empty($_GET['ncat'])){$tmp_title=$_GET['ncat'];}
	$sql_title="select * from ".DB_PREFIX."pages where id=".intval($tmp_title);
	$query_title=$db->query($sql_title);
	$num_title=$query_title->rowCount();
	if($num_title>0){
		$arr_title=$query_title->fetch();
		echo $arr_title['title']." -- ";
	}else{
		echo $arr_main['title']." -- ";} ?>MoMoCMS -- 更好用的企业建站系统</title>
<meta name="keywords" content="<?php
if(!empty($arr_main['keywords'])){echo $arr_main['keywords'];}else{echo 'MoMoCMS';}?>">
<meta name="description" content="<?php
if(!empty($arr_main['depict'])){echo $arr_main['depict'];}else{echo 'MoMoCMS -- 更好用的企业建站系统';}?>">
<link rel="icon" href="<?php echo URL; ?>/resource/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo URL; ?>/resource/favicon.ico" type="image/x-icon">
<link rel="bookmark" href="<?php echo URL; ?>/resource/favicon.ico" type="image/x-icon">
<script src="<?php echo URL; ?>/resource/jquery-1.11.1.min.js"></script>
<script src="<?php echo URL_M; ?>/js/jquery.mmenu.min.js"></script>
<script type="text/javascript">
	$(function() {
		$('nav#menu').mmenu();
	});
</script>
<link href="<?php echo URL_M; ?>/css/jquery.mmenu.css?<?php echo time();?>" rel="stylesheet" type="text/css">
<link href="<?php echo URL_M; ?>/css/style.css?<?php echo time();?>" rel="stylesheet" type="text/css">
<link href="<?php echo URL_M; ?>/css/font-face.css?<?php echo time();?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo URL_M; ?>/bxslider/jquery.bxslider.min.js"></script>
<link href="<?php echo URL_M; ?>/bxslider/jquery.bxslider.css" rel="stylesheet" />
</head>
<body>
<div id="page">
	<header>
    	<div class="l_tbn"><a href="#menu" class="glyphicon glyphicon-th-large"></a></div>
        <a style="font-size:13px;color:#fff;font-weight:bold;text-decoration:none;" href="<?php echo URL_M; ?>">MoMoCMS手机版</a>
    </header>
    <div class="content">
<ul class="bxslider" style="margin:0;padding:0;display:none">
<?php
//打开 images 目录
$dir = dir("../resource/slide/images");
//列出 images 目录中的文件
while (($file = $dir->read()) !== false)
{
	if($file!="." && $file!=".."){
	?>
<li><img style="border:0;width:100%;" src="<?php echo URL; ?>/resource/slide/images/<?php echo $file; ?>" /></li>
<?php
	}
}
$dir->close();
?> 
</ul>
<script type="text/javascript">
    $(function() {
       $(".bxslider").css({display:"block"});
       $('.bxslider').bxSlider({
       	auto: true,
       	pager:false,
       	pause:'5000'
       	});
    });
    </script>
<?php
   		$id_tmp='';
		$sql_tmp="select * from ".DB_PREFIX."pages where isProduct=1";
		$query_tmp=$db->query($sql_tmp);
		$num_tmp=$query_tmp->rowCount();
		if($num_tmp>0){
			$arr_tmp=$query_tmp->fetch();
			$id_tmp=$arr_tmp['id'];
		}
         if(!empty($_GET['nd'])){$tmp_side=$_GET['nd'];}
		 if(!empty($_GET['nid'])){$tmp_side=$_GET['nid'];}
		 if(!empty($_GET['ncat'])){$tmp_side=$_GET['ncat'];}
		 $sql_side="select * from ".DB_PREFIX."mix_sidebar where pid=".intval($tmp_side)." order by sort desc";
		 $query_side=$db->query($sql_side);
		 $num_side=$query_side->rowCount();
		 if($num_side>0){
			while($arr_side=$query_side->fetch()){
         			require("../admin/".$arr_side['name']);
         		}
         }else if($arr_main['barsid']==1){
	       	$sql_mixsidebar="select * from ".DB_PREFIX."mix_sidebar where pid=".$arr_main['id']." order by sort desc";
	       	$query_mixsidebar=$db->query($sql_mixsidebar);
	       	$num_mixsidebar=$query_mixsidebar->rowCount();
         	if($num_mixsidebar>0){
         		while($arr_mixsidebar=$query_mixsidebar->fetch()){
         			require("../admin/".$arr_mixsidebar['name']);
         		}
         	}
        	}else{
        		require("../admin/product_sidebar.php");
        	}
         ?>
         
        <div id="main">
      	<div id="main_content">
      		<ol class="breadcrumb">
                 <li><a href="<?php echo URL_M; ?>">主页</a></li>
			   <li><a href="<?php echo URL_M; ?>/list/<?php echo intval($_GET['id']); ?>"><?php echo $arr_main['title']; ?></a></li>
			   <?php if(!empty($_GET['cat'])){
			   		$sql_cid="select * from ".DB_PREFIX."product where id=".intval($_GET['cat']);
			   		$query_cid=$db->query($sql_cid);
			   		$num_cid=$query_cid->rowCount();
			   		if($num_cid>0){
			   			$arr_cid=$query_cid->fetch();
			   			echo '<li><a href="'.$cat_url.'">'.$arr_cid['name'].'</a></li>';	
			   		}}	?>
			   		<?php if(!empty($_GET['nid'])){	
			   		$sql_sid="select * from ".DB_PREFIX."pages where id=".intval($_GET['nid']);
			   		$query_sid=$db->query($sql_sid);
			   		$num_sid=$query_sid->rowCount();
			   		if($num_sid>0){
			   			$arr_sid=$query_sid->fetch();
			   			$nid_title_tmp = '<li>'.$arr_sid['title'].'</li>';	
			   		}}	?>
			   		<?php if(!empty($_GET['ncat'])){	
			   		$sql_sid="select * from ".DB_PREFIX."pages where id=".intval($_GET['ncat']);
			   		$query_sid=$db->query($sql_sid);
			   		$num_sid=$query_sid->rowCount();
			   		if($num_sid>0){
			   			$arr_sid=$query_sid->fetch();
			   			$ncat_title_tmp = '<li><a href="'. URL_M.'/list/'.intval($_GET['id']).'/ncat/'.intval($_GET['ncat']).'">'.$arr_sid['title'].'</a></li>';	
			   		}}	?>
			   		<?php if(!empty($_GET['ncat'])){
			   			echo $ncat_title_tmp;
			   		}if(!empty($_GET['nid'])){
			   			echo $nid_title_tmp;
			   		} ?>
			   		<?php if(!empty($_GET['nd'])){	
			   		$sql_sid="select * from ".DB_PREFIX."pages where id=".intval($_GET['nd']);
			   		$query_sid=$db->query($sql_sid);
			   		$num_sid=$query_sid->rowCount();
			   		if($num_sid>0){
			   			$arr_sid=$query_sid->fetch();
			   			echo '<li>'.$arr_sid['title'].'</li>';	
			   		}}	?>
			   		<?php if(!empty($_GET['sid'])){	
			   		$sql_sid="select * from ".DB_PREFIX."product_sub where id=".intval($_GET['sid']);
			   		$query_sid=$db->query($sql_sid);
			   		$num_sid=$query_sid->rowCount();
			   		if($num_sid>0){
			   			$arr_sid=$query_sid->fetch();
			   			echo '<li>'.$arr_sid['name'].'</li>';	
			   		}}	?>
              </ol>
              <?php  if(!empty($arr_sid['module'])){
              		require("../admin/".$arr_sid['module']);
            	}else if(!empty($arr_main['module'])){
              		require("../admin/".$arr_main['module']);
            	}else{	?>
            		<p><?php
              	$tmp_content = !empty($arr_sid) ? $arr_sid['content'] : $arr_main['content'];
              	if (ini_get('magic_quotes_gpc')){
              		echo stripslashes(htmlspecialchars_decode($tmp_content));
              	}else{
              		echo htmlspecialchars_decode($tmp_content);
              	}	 ?></p>
            		<?php	}	?>
      	</div>
      </div>
         
        	<footer class="footer">
		    <div class="footer-inner">
		            版权所有，保留一切权利，Powered BY YouYaX
		     </div>
		</footer>
</div>
	<nav id="menu">
        <ul>
           <li class="<?php if(empty($_GET['id'])){echo 'Selected';} ?>"><a href="<?php echo URL_M; ?>"><span>首页</span></a></li>
   		<?php
   		$sql="select * from ".DB_PREFIX."pages where isMenu=1 order by sort desc";
   		$query=$db->query($sql);
   		while($arr=$query->fetch()){	?>
   			<li class="<?php if($_GET['id']==$arr['id'])echo 'Selected'; 
   					$child_sql="select * from ".DB_PREFIX."pages where id=".intval($_GET['id']);
   					$child_query=$db->query($child_sql);
   					$child_num=$child_query->rowCount();
   					if($child_num>0){
   						$child_arr=$child_query->fetch();
   						if($child_arr['pid']==$arr['id']){echo 'Selected';}
   					}
   					?>">
   				<a href="
   					<?php	if(!empty($arr['ext_url'])){
   						echo $arr['ext_url'];
   					}else{echo URL_M."/list/".$arr['id']; }?>
   					"><span><?php echo $arr['title']; ?></span></a>
   				<?php if($arr['pid']=='-1'){
   					$sec_sql="select * from ".DB_PREFIX."pages where pid=".$arr['id']." order by sort desc";
   					$sec_query=$db->query($sec_sql);
   						?>
   					<dl>
   					<?php while($sec_arr=$sec_query->fetch()){
   						 if($sec_arr['module']=='news_module.php'){
   							?>
		   				<dd><a href="
		   					<?php echo URL_M.'/list/'.$arr['id'].'/ncat/'.$sec_arr['id'];	?>
		   					"><?php echo $sec_arr['title']; ?></a></dd>
		   			<?php	}else{	?>
		   				<dd><a href="
		   					<?php echo URL_M.'/list/'.$arr['id'].'/nd/'.$sec_arr['id'];	?>
		   					"><?php echo $sec_arr['title']; ?></a></dd>
		   			<?php	}}	?>
		   			</dl>
		   		<?php	}	?>
   				<?php if($arr['isProduct']==1){
   					$p_sql="select * from ".DB_PREFIX."product order by sort desc";
   					$p_query=$db->query($p_sql);
   						?>
   					<dl>
   					<?php while($p_arr=$p_query->fetch()){	?>
		   				<dd><a href="
		   					<?php
	        	if(!empty($id_tmp)){
	        		echo URL_M.'/list/'.$id_tmp.'/cat/'.$p_arr['id'];	
	        	}
	        	?>
		   					"><span><?php echo $p_arr['name']; ?></span></a></dd>
		   			<?php	}	?>
		   			</dl>
		   		<?php	}	?>
   			</li>
   		<?php	}	?>
        </ul>
    </nav>
</div>
</body>
</html>