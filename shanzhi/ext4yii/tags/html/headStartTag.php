<?php 
	$ie_version=self::getIEVersion();
	$skin=self::getExtSkin();
	$ext=self::getExtPath();
	$title=isset($atts['title'])?$atts['title']:'';
?>
<head>
<?php if($title){?>
<title><?php echo $title?></title>
<?php }?>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<?php if($ie_version >= 10){ //IE10及以上版本统一使用10来渲染。?>
<meta http-equiv="X-UA-Compatible" content="IE=10">
<?php }?>
<?php 
##IE10以下版本统一使用自己的最高版本来渲染。如果安装了GCF，则使用GCF来渲染页面，如果没有则使用IE最高模式进行渲染。
##GCF只会对678版本有效。
?>
<?php if($ie_version < 10){?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php }?>
<?php 
##可以让部分国产双核浏览器默认采用高速模式渲染页面
?>
<meta name="renderer" content="webkit">
<?php require dirname(__FILE__).'/common/dynamicCssTag.php';?>