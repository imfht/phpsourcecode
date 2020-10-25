<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getTypeNameLink($data)
	{
		return $this->elements_object->getTypeNameLink($data);
	}

	public function getMenuCount($data)
	{
		return $this->elements_object->getMenuCount($data['type_key']);
	}

	public function getMenus($data)
	{
		$params = array('type_key' => $data['type_key']);

		$indexIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_INDEX,
			'url' => $this->urlManager->getUrl('index', 'menus', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_MENUS_URLS_MENUS_INDEX,
		));

		$createIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('create', 'menus', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_MENUS_URLS_MENUS_CREATE,
		));

		// $output = $indexIcon . $createIcon;
		$output = $indexIcon;
		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['type_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('menus/types_index_btns'); ?>

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
			'menu_count' => array(
				'callback' => 'getMenuCount'
			),
			'menus' => array(
				'callback' => 'getMenus'
			),
		),
		'columns' => array(
			'type_name',
			'type_key',
			'description',
			'menu_count',
			'menus',
			'type_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('menus/types_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>