<?php
/* @var $this PostController */
/* @var $model Post */

$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('重要信息'=>array('index'),'发布信息'),
	'homeLink'=>false,
));
?>

<h1>发布信息</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>