<?php
class TableRender extends views\bootstrap\components\TableRender
{
	public function getGroupNameLink($data)
	{
		return $this->elements_object->getGroupNameLink($data);
	}

	public function getBuilderName($data)
	{
		return $this->elements_object->getBuilderNameByBuilderId($data['builder_id']);
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['group_id'],
			'builder_id' => $data['builder_id']
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('builder/groups_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'group_name' => array(
				'callback' => 'getGroupNameLink'
			),
			'builder_name' => array(
				'callback' => 'getBuilderName'
			)
		),
		'columns' => array(
			'group_name',
			'builder_name',
			'prompt',
			'sort',
			'description',
			'group_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('builder/groups_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>