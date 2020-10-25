<?php
class TableRender extends views\bootstrap\components\TableRender
{
	public function getValidatorNameLink($data)
	{
		return $this->elements_object->getValidatorNameLink($data);
	}

	public function getFieldName($data)
	{
		return $this->elements_object->getFieldNameByFieldId($data['field_id']);
	}

	public function getOptionCategory($data)
	{
		return $this->elements_object->getOptionCategoryLangByOptionCategory($data['option_category']);
	}

	public function getWhen($data)
	{
		return $this->elements_object->getWhenLangByWhen($data['when']);
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['validator_id'],
			'field_id' => $data['field_id']
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);
		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('builder/validators_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'validator_name' => array(
				'callback' => 'getValidatorNameLink'
			),
			'field_name' => array(
				'callback' => 'getFieldName'
			),
			'option_category' => array(
				'callback' => 'getOptionCategory'
			),
			'when' => array(
				'callback' => 'getWhen'
			)
		),
		'columns' => array(
			'validator_name',
			'field_name',
			'options',
			'option_category',
			'message',
			'sort',
			'when',
			'validator_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('builder/validators_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>