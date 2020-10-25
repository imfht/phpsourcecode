<?php
/* @var $this PostController */
/* @var $model Post */

$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('重要信息'=>array('index'),'管理信息'=>array('manage'),'更新信息'),
	'homeLink'=>false,
));
?>

<h1>更新信息</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>