<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getGroupNameLink($data)
	{
		return $this->elements_object->getGroupNameLink($data);
	}

	public function getPermission($data)
	{
		$params = array(
			'id' => $data['group_id'],
		);

		$modifyIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_MODIFY,
			'url' => $this->urlManager->getUrl('permissionmodify', $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_MODIFY,
		));

		return $modifyIcon;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['group_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('users/groups_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'group_name' => array(
				'callback' => 'getGroupNameLink'
			),
			'permission' => array(
				'callback' => 'getPermission'
			)
		),
		'columns' => array(
			'group_name',
			'sort',
			'permission',
			'description',
			'group_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('users/groups_index_btns'); ?>
