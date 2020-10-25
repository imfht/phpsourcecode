<?php //动态CSS文件?>
<style type="text/css">
<?php //滑动通知所需?>
.ux-notification-light {
  background-image: url('<?php echo $ext?>/resources/ext-theme-<?php echo $skin?>/images/notification/<?php echo $skin?>.png');
}
<?php //neptune主题的滑动通知地板是蓝色的,设置高亮字体颜色?>
<?php if($skin == "neptune"){?>
.ux-notification-light .x-window-body {
	color:#FFFFFF;
}
<?php }?>
</style>
