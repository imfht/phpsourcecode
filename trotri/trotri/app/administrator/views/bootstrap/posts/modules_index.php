<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getModuleNameLink($data)
	{
		return $this->elements_object->getModuleNameLink($data);
	}

	public function getForbidden($data)
	{
		$params = array(
			'id' => $data['module_id'],
			'column_name' => 'forbidden'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['module_id'],
			'name' => 'forbidden',
			'value' => $data['forbidden'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['module_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('posts/modules_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'module_name' => array(
				'callback' => 'getModuleNameLink'
			),
			'forbidden' => array(
				'callback' => 'getForbidden'
			),
		),
		'columns' => array(
			'module_name',
			'forbidden',
			'description',
			'module_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('posts/modules_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>