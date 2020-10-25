<?php 
$atts=self::resolveAtts($atts);
if(isset($atts['lib'])){
	$lib=explode(",", $atts['lib']);
	foreach ($lib as $val){
		$$val=true;
	}
}

?>
<?php ##导入WEBUI库?>
<?php $extPath=self::getExtPath();?>
<?php if(isset($ext)){?>
	<?php $skin=self::getExtSkin();?>
	<?php if($skin == "neptune"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $extPath?>/weblib/ext/resources/css/ext-all-neptune.css" />
	<?php }elseif($skin == "gray"){ ?>
<link rel="stylesheet" type="text/css" href="<?php echo $extPath?>/weblib/ext/resources/css/ext-all-gray.css" />
	<?php }else{ ?>
<link rel="stylesheet" type="text/css" href="<?php echo $extPath?>/weblib/ext/resources/css/ext-all.css" />
	<?php }?>	
<link rel="stylesheet" type="text/css" href="<?php echo $extPath?>/css/ext4yii.css" />
<script type="text/javascript" src="<?php echo $extPath?>/weblib/ext/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $extPath?>/weblib/ext/locale/ext-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo $extPath?>/js/ext4yii.js"></script>
<?php ##awesome组件更改为标配组件 ?>
<link rel="stylesheet" type="text/css" href="<?php echo $extPath?>/weblib/awesome/css/font-awesome.min.css" />
<?php }?>

<?php ##JQuery库 ?>
<?php if(isset($jquery)){?>
<script type="text/javascript" src="<?php echo $extPath?>/weblib/jquery/jquery.min-1.10.2.js"></script>
<?php }?>
<?php ##如果需要使用下拉菜单，则还需要引入buttons.js ?>
<?php if(isset($buttons)){?>
<link rel="stylesheet" type="text/css" href="<?php echo $extPath?>/weblib/buttons/css/buttons.css" />
<?php }?>
<?php if(isset($raphael)){?>
<script type="text/javascript" src="<?php echo $extPath?>/weblib/raphael/raphael.js"></script>
<?php }?>

<?php ##导入指定的资源文件?>
<?php if(isset($atts['css'])){?>
<link rel="stylesheet" type="text/css" href="<?php echo $atts['css']?>" />
<?php }?>
<?php if(isset($atts['js'])){?>
<script type="text/javascript" src="<?php echo $atts['js']?>"></script>
<?php }?>