<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getTitleLink($data)
	{
		return $this->elements_object->getTitleLink($data);
	}

	public function getModuleName($data)
	{
		return $this->elements_object->getModuleNameByModuleId($data['module_id']);
	}

	public function getIsHead($data)
	{
		$params = array(
			'id' => $data['post_id'],
			'column_name' => 'is_head'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['post_id'],
			'name' => 'is_head',
			'value' => $data['is_head'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getIsRecommend($data)
	{
		$params = array(
			'id' => $data['post_id'],
			'column_name' => 'is_recommend'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['post_id'],
			'name' => 'is_recommend',
			'value' => $data['is_recommend'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getIsPublished($data)
	{
		$params = array(
			'id' => $data['post_id'],
			'column_name' => 'is_published'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['post_id'],
			'name' => 'is_published',
			'value' => $data['is_published'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getSort($data)
	{
		return $this->html->text('sort[' . $data['post_id'] . ']', $data['sort'], array('class' => 'form-control input-listsort', 'size' => '14'));
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['post_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$trashIcon = $this->getTrashIcon($params);

		$output = $modifyIcon . $trashIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('posts/posts_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'title' => array(
				'callback' => 'getTitleLink'
			),
			'module_id' => array(
				'callback' => 'getModuleName'
			),
			'is_head' => array(
				'callback' => 'getIsHead'
			),
			'is_recommend' => array(
				'callback' => 'getIsRecommend'
			),
			'is_published' => array(
				'callback' => 'getIsPublished'
			),
			'sort' => array(
				'callback' => 'getSort'
			),
		),
		'columns' => array(
			'title',
			'category_name',
			'module_id',
			'is_head',
			'is_recommend',
			'is_published',
			// 'hits',
			'sort',
			'creator_name',
			'last_modifier_name',
			'dt_created',
			'post_id',
			'_operate_',
		),
		'checkedToggle' => 'post_id',
	)
);
?>

<?php $this->display('posts/posts_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>