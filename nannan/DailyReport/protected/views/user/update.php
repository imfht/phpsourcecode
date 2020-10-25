<?php
$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('个人信息'=>array('view'),'更改个人信息'),
	'homeLink'=>false,
));
?>

<h2>更新信息：</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>