<?php
class TableRender extends views\bootstrap\components\TableRender
{
	public function getTypeNameLink($data)
	{
		return $this->elements_object->getTypeNameLink($data);
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['type_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = ($data['type_id'] > 1) ? $this->getRemoveIcon($params) : '';

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('member/types_index_btns'); ?>

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
			'sort',
			'description',
			'type_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('member/types_index_btns'); ?>