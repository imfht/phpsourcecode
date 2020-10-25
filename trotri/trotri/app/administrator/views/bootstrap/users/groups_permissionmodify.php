<?php
$formBuilder = $this->createWidget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'permissionmodify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'_button_save_',
			'_button_saveclose_',
			'_button_cancel_'
		)
	)
);

$html = $formBuilder->getHtml();
$eleName = 'amcas';

$extendParentPermissionIcon = $html->tag('span', array(
	'class' => 'glyphicon glyphicon-share-alt',
	'data-original-title' => $this->MOD_USERS_USER_GROUPS_EXTEND_PARENT_PERMISSION_LABEL,
	'title' => $this->MOD_USERS_USER_GROUPS_EXTEND_PARENT_PERMISSION_LABEL
), '');
?>

<!-- FormBuilder -->
<?php echo $formBuilder->openForm(); ?>

<ul class="nav nav-tabs">
<?php
$attributes = array('class' => 'active');
foreach ($this->data as $appName => $rows) :
	echo $html->openTag('li', $attributes);
	echo $html->a($rows['prompt'], '#'.$appName, array('data-toggle' => 'tab'));
	$attributes = array();
endforeach;
?>
</ul><!-- /.nav nav-tabs -->

<?php $this->widget('views\bootstrap\widgets\Breadcrumbs', $this->breadcrumbs); ?>

<div class="form-group">
  <div class="col-lg-1"></div>
  <div class="col-lg-11"><?php echo $formBuilder->getButtons(); ?></div>
</div><!-- /.form-group -->

<div class="tab-content">

<!-- app -->
<?php
$attributes = array('class' => 'tab-pane fade active in');
foreach ($this->data as $appName => $apps) :
	echo $html->openTag('div', array('id' => $appName) + $attributes), "\n";
	$attributes = array('class' => 'tab-pane fade');
?>

<!-- mod -->
<?php
foreach ($apps['rows'] as $modName => $mods) :
	echo $formBuilder->createElement('views\bootstrap\components\form\ICheckboxElement', array(
		'label' => $mods['prompt'] . ' [' . $modName . '] : ',
		'name' => '__mod__',
		'options' => array(
			$eleName . '[' . $appName . '][' . $modName . ']' => $this->CFG_SYSTEM_GLOBAL_CHECKED_ALL
		),
	))->fetch();
	if (!isset($mods['rows'])) : continue; endif;
?>

<!-- ctrl -->
<?php
foreach ($mods['rows'] as $ctrlName => $ctrls) :
	$name = $eleName . '[' . $appName . '][' . $modName . '][' . $ctrlName . '][]';
	$options = array();
	foreach ($ctrls['powers'] as $key => $value) {
		if (isset($ctrls['pchecked']) && in_array($key, $ctrls['pchecked'])) {
			$value .= '&nbsp;' . $extendParentPermissionIcon;
		}

		$options[$key] = $value;
	}

	echo $formBuilder->createElement('views\bootstrap\components\form\ICheckboxElement', array(
		'label' => $ctrls['prompt'],
		'name' => $name,
		'options' => $options,
		'value' => isset($ctrls['checked']) ? $ctrls['checked'] : array()
	))->fetch();
endforeach;
?>
<!-- /ctrl -->

<?php endforeach; ?>
<!-- /mod -->

<?php
	echo "\n", $html->closeTag('div');
endforeach;
?>
<!-- /app -->

</div><!-- /.tab-content -->

<div class="form-group">
  <div class="col-lg-1"></div>
  <div class="col-lg-11"><?php echo $formBuilder->getButtons(); ?></div>
</div><!-- /.form-group -->

<?php echo $formBuilder->closeForm(); ?>
