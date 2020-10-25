<?php
$this->widget(
	'components\posts\ListBox',
	array(
		'find_type' => 'head',
		'limit' => 4
	),
	array(
		'tplName' => 'fourpics'
	)
);
?>

<?php
$this->widget(
	'components\posts\ListBox',
	array(
		'find_type' => 'recommend',
		'limit' => 4
	),
	array(
		'tplName' => 'fourpics'
	)
);
?>

<?php
$this->widget(
	'components\posts\ListBox',
	array(
		'find_type' => 'catid-1',
		'limit' => 4
	),
	array(
		'tplName' => 'fourpics'
	)
);
?>