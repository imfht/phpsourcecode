<?php
class TableRender extends views\bootstrap\components\TableRender
{
	public function getRankNameLink($data)
	{
		return $this->elements_object->getRankNameLink($data);
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['rank_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = ($data['rank_id'] > 1) ? $this->getRemoveIcon($params) : '';

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('member/ranks_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'rank_name' => array(
				'callback' => 'getRankNameLink'
			),
		),
		'columns' => array(
			'rank_name',
			'experience',
			'sort',
			'description',
			'rank_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('member/ranks_index_btns'); ?>