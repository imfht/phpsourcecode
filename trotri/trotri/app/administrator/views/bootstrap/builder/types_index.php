<?php
class TableRender extends views\bootstrap\components\TableRender
{
	public function getTypeNameLink($data)
	{
		return $this->elements_object->getTypeNameLink($data);
	}

	public function getOperate($data)
	{
		$params = array('id' => $data['type_id']);
		$output = $this->getModifyIcon($params) . $this->getRemoveIcon($params);
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('builder/types_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'type_name' => array(
				'callback' => 'getTypeNameLink'
			),
		),
		'columns' => array(
			'type_name',
			'form_type',
			'field_type',
			'category',
			'sort',
			'type_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('builder/types_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>