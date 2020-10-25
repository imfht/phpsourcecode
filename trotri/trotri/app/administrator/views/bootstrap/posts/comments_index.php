<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getCommentIdLink($data)
	{
		return $this->elements_object->getCommentIdLink($data);
	}

	public function getPostTitle($data)
	{
		return $this->elements_object->getPostTitleByPostId($data['post_id']);
	}

	public function getIsPublished($data)
	{
		$params = array(
			'id' => $data['comment_id'],
			'column_name' => 'is_published'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['comment_id'],
			'name' => 'is_published',
			'value' => $data['is_published'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getIpCreated($data)
	{
		return long2ip($data['ip_created']);
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['comment_id'],
			'post_id' => $data['post_id']
		);

		$removeIcon = $this->getRemoveIcon($params);

		$output = $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('posts/comments_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'comment_id' => array(
				'callback' => 'getCommentIdLink'
			),
			'post_title' => array(
				'callback' => 'getPostTitle'
			),
			'is_published' => array(
				'callback' => 'getIsPublished'
			),
			'ip_created' => array(
				'callback' => 'getIpCreated'
			),
		),
		'columns' => array(
			'content',
			'author_name',
			'author_mail',
			'post_title',
			'is_published',
			'dt_created',
			'ip_created',
			'comment_id',
			'_operate_',
		),
		'checkedToggle' => 'comment_id',
	)
);
?>

<?php $this->display('posts/comments_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>