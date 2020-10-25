<?php
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
		return $this->elements_object->getIsHeadLangByIsHead($data['is_head']);
	}

	public function getIsRecommend($data)
	{
		return $this->elements_object->getIsRecommendLangByIsRecommend($data['is_recommend']);
	}

	public function getIsPublished($data)
	{
		return $this->elements_object->getIsPublishedLangByIsPublished($data['is_published']);
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['post_id'],
		);

		$restoreIcon = $this->getRestoreIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $restoreIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('posts/posts_trashindex_btns'); ?>

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

<?php $this->display('posts/posts_trashindex_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>