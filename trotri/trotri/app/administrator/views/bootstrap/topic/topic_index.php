<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getTopicNameLink($data)
	{
		return $this->elements_object->getTopicNameLink($data);
	}

	public function getCover($data)
	{
		$output = $this->html->img($data['cover'], '', array('width' => '200px', 'height' => '100px'));
		return $output;
	}

	public function getIsPublished($data)
	{
		$params = array(
			'id' => $data['topic_id'],
			'column_name' => 'is_published'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['topic_id'],
			'name' => 'is_published',
			'value' => $data['is_published'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getUseHeader($data)
	{
		$params = array(
			'id' => $data['topic_id'],
			'column_name' => 'use_header'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['topic_id'],
			'name' => 'use_header',
			'value' => $data['use_header'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getUseFooter($data)
	{
		$params = array(
			'id' => $data['topic_id'],
			'column_name' => 'use_footer'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['topic_id'],
			'name' => 'use_footer',
			'value' => $data['use_footer'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['topic_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('topic/topic_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'topic_name' => array(
				'callback' => 'getTopicNameLink'
			),
			'cover' => array(
				'callback' => 'getCover'
			),
			'is_published' => array(
				'callback' => 'getIsPublished'
			),
			'use_header' => array(
				'callback' => 'getUseHeader'
			),
			'use_footer' => array(
				'callback' => 'getUseFooter'
			),
		),
		'columns' => array(
			'topic_name',
			'topic_key',
			'cover',
			'is_published',
			'use_header',
			'use_footer',
			'sort',
			'dt_created',
			'topic_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('topic/topic_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>