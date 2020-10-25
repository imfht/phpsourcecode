<?php
class TableRender extends views\bootstrap\components\TableRender
{
	public function getOptionNameLink($data)
	{
		return $this->elements_object->getOptionNameLink($data);
	}

	public function getSort($data)
	{
		return $this->html->text('sort[' . $data['option_id'] . ']', $data['sort'], array('class' => 'form-control input-listsort', 'size' => '5'));
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['option_id'],
			'poll_id' => $data['poll_id']
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('poll/polloptions_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'option_name' => array(
				'callback' => 'getOptionNameLink'
			),
			'sort' => array(
				'callback' => 'getSort'
			),
		),
		'columns' => array(
			'option_name',
			'votes',
			'sort',
			'option_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('poll/polloptions_index_btns'); ?>
