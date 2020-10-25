<?php
/* @var $this DailyreportController */
/* @var $model Dailyreport */

$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('日报'=>array('index'),'更改今天日报'),
	'homeLink'=>false,
));
?>

<h2>更该今天的日报</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>