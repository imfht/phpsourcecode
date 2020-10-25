<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getPollNameLink($data)
	{
		return $this->elements_object->getPollNameLink($data);
	}

	public function getIsPublished($data)
	{
		$params = array(
			'id' => $data['poll_id'],
			'column_name' => 'is_published'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['poll_id'],
			'name' => 'is_published',
			'value' => $data['is_published'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getJoinType($data)
	{
		return $this->elements_object->getJoinTypeLangByJoinType($data['join_type']);
	}

	public function getAllowUnregistered($data)
	{
		return $this->elements_object->getAllowUnregisteredByAllowUnregistered($data['allow_unregistered']);
	}

	public function getIsVisible($data)
	{
		return $this->elements_object->getIsVisibleLangByIsVisible($data['is_visible']);
	}

	public function getIsMultiple($data)
	{
		return $this->elements_object->getIsMultipleLangByIsMultiple($data['is_multiple']);
	}

	public function getPolloptions($data)
	{
		$params = array('poll_id' => $data['poll_id']);

		$indexIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_INDEX,
			'url' => $this->urlManager->getUrl('index', 'polloptions', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_POLL_URLS_POLLOPTIONS_INDEX,
		));

		$createIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('create', 'polloptions', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_POLL_URLS_POLLOPTIONS_CREATE,
		));

		$output = $indexIcon . $createIcon;
		// $output = $indexIcon;
		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['poll_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('poll/polls_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'poll_name' => array(
				'callback' => 'getPollNameLink'
			),
			'allow_unregistered' => array(
				'callback' => 'getAllowUnregistered'
			),
			'is_published' => array(
				'callback' => 'getIsPublished'
			),
			'join_type' => array(
				'callback' => 'getJoinType'
			),
			'is_visible' => array(
				'callback' => 'getIsVisible'
			),
			'is_multiple' => array(
				'callback' => 'getIsMultiple'
			),
			'polloptions' => array(
				'callback' => 'getPolloptions'
			),
		),
		'columns' => array(
			'poll_name',
			'poll_key',
			'allow_unregistered',
			'join_type',
			'is_published',
			'dt_publish_up',
			'dt_publish_down',
			'is_visible',
			'is_multiple',
			'polloptions',
			'poll_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('poll/polls_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>