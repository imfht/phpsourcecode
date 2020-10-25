<?php 
$skin=self::getExtSkin();
$extPath=self::getExtPath();
$id=isset($atts['id'])?$atts['id']:self::getUUID4Tag();
?>
<?php if(isset($atts['backGround']) && $atts['backGround']=="true"){?>
<style type="text/css">
body {
	background-color: #FFFFFF !important;
	background-attachment: fixed !important;
	background-position: bottom center !important;
	background-repeat: repeat-x !important;
	background-image:url("<?php echo $extPath?>/static/image/background/main/<?php echo $skin?>.png") !important;
}
</style>
<?php }?>
<body id="<?php echo $id?>"<?php echo isset($atts['class'])?' class="'.$atts['class'].'"':''?>>