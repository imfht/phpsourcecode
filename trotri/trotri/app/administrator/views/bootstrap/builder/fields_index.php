<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getFieldNameLink($data)
	{
		return $this->elements_object->getFieldNameLink($data);
	}

	public function getBuilderName($data)
	{
		return $this->elements_object->getBuilderNameByBuilderId($data['builder_id']);
	}

	public function getGroupId($data)
	{
		return $this->elements_object->getPromptByGroupId($data['group_id']);
	}

	public function getTypeId($data)
	{
		return $this->elements_object->getTypeNameByTypeId($data['type_id']);
	}

	public function getColumnAutoIncrement($data)
	{
		$params = array(
			'id' => $data['field_id'],
			'column_name' => 'column_auto_increment'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['field_id'],
			'name' => 'column_auto_increment',
			'value' => $data['column_auto_increment'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getBuilderFieldValidators($data)
	{
		$params = array('field_id' => $data['field_id']);

		$indexIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_INDEX,
			'url' => $this->urlManager->getUrl('index', 'validators', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_URLS_VALIDATORS_INDEX,
		));

		$createIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('create', 'validators', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_URLS_VALIDATORS_CREATE,
		));

		// $output = $indexIcon . $createIcon;
		$output = $indexIcon;
		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['field_id'],
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

<?php $this->display('builder/fields_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'field_name' => array(
				'callback' => 'getFieldNameLink'
			),
			'builder_name' => array(
				'callback' => 'getBuilderName'
			),
			'group_id' => array(
				'callback' => 'getGroupId'
			),
			'type_id' => array(
				'callback' => 'getTypeId'
			),
			'column_auto_increment' => array(
				'callback' => 'getColumnAutoIncrement'
			),
			'builder_field_validators' => array(
				'callback' => 'getBuilderFieldValidators'
			)
		),
		'columns' => array(
			'field_name',
			'builder_name',
			'group_id',
			'type_id',
			'sort',
			'html_label',
			'column_auto_increment',
			'builder_field_validators',
			'field_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('builder/fields_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>