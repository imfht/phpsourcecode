<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getBuilderNameLink($data)
	{
		return $this->elements_object->getBuilderNameLink($data);
	}

	public function getTblProfile($data)
	{
		$params = array(
			'id' => $data['builder_id'],
			'column_name' => 'tbl_profile'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['builder_id'],
			'name' => 'tbl_profile',
			'value' => $data['tbl_profile'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getBuilderFieldGroups($data)
	{
		$params = array('builder_id' => $data['builder_id']);

		$indexIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_INDEX,
			'url' => $this->urlManager->getUrl('index', 'groups', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_URLS_GROUPS_INDEX,
		));

		$createIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('create', 'groups', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_URLS_GROUPS_CREATE,
		));

		$output = $indexIcon . $createIcon;
		return $output;
	}

	public function getBuilderFields($data)
	{
		$params = array('builder_id' => $data['builder_id']);

		$indexIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_INDEX,
			'url' => $this->urlManager->getUrl('index', 'fields', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_URLS_FIELDS_INDEX,
		));

		$createIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('create', 'fields', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_URLS_FIELDS_CREATE,
		));

		$output = $indexIcon . $createIcon;
		return $output;
	}

	public function getOperate($data)
	{
		$params = array('id' => $data['builder_id']);
		$modifyIcon = $this->getModifyIcon($params);
		$trashIcon = $this->getTrashIcon($params);

		// 生成代码按钮
		$gcIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_TOOL,
			'url' => $this->urlManager->getUrl('gc', $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_BUILDERS_GC_LABEL
		));

		$output = $modifyIcon . $trashIcon . $gcIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('builder/builders_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'builder_name' => array(
				'callback' => 'getBuilderNameLink'
			),
			'tbl_profile' => array(
				'callback' => 'getTblProfile'
			),
			'builder_field_groups' => array(
				'label' => $this->MOD_BUILDER_URLS_GROUPS_INDEX,
				'callback' => 'getBuilderFieldGroups'
			),
			'builder_fields' => array(
				'label' => $this->MOD_BUILDER_URLS_FIELDS_INDEX,
				'callback' => 'getBuilderFields'
			),
		),
		'columns' => array(
			'builder_name',
			'tbl_name',
			'tbl_profile',
			'tbl_engine',
			'tbl_charset',
			'app_name',
			'mod_name',
			'cls_name',
			'ctrl_name',
			'builder_field_groups',
			'builder_fields',
			'builder_id',
			'_operate_',
		),
		'checkedToggle' => 'builder_id',
	)
);
?>

<?php $this->display('builder/builders_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>