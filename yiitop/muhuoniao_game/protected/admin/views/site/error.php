<?php
$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>
<div id="error">
<img class="error_img" src="<?php echo Yii::app()->request->baseUrl; ?>/system/images/error.png" />
<div class="error_right">
<h2>Error <?php echo $code; ?></h2>
<p><?php echo CHtml::encode($message); ?></p>
<p><a href="javascript:history.go(-1);">返回</a></p>
</div>
</div>