<?php
$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('日报'=>array('dailyreport/index'),'请假'),
	'homeLink'=>false,
));
?>
<div class="alert alert-info">
    <strong>注意：</strong> 请假了不能发日报，但不会出现在日报统计当中，请假结束后切换到未请假状态。
</div>
<?php echo $this->renderPartial('_off', array('model'=>$model)); ?>