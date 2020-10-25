<?php
/* @var $this DailyreportController */
/* @var $model Dailyreport */

$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('日报'=>array('index'),'写日报'),
	'homeLink'=>false,
));?>

<h1>写日报</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>