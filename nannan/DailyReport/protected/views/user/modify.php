<?php
$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('日报'=>array('dailyreport/index'),'日报设置'),
	'homeLink'=>false,
));
?>

<h1>更新日报设置：</h1>

<?php echo $this->renderPartial('_modify', array('model'=>$model)); ?>