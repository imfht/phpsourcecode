<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getAlreadyGb($data)
	{
		return ($data['already_gb'] === 'y') ? $this->view->CFG_SYSTEM_GLOBAL_YES : $this->view->CFG_SYSTEM_GLOBAL_NO;
	}

	public function getOperate($data)
	{
		$params = array('tbl_name' => $data['tbl_name']);

		return ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_TOOL,
			'url' => $this->urlManager->getUrl('gb', $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_BUILDER_URLS_TBLNAMES_GB,
		));
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'already_gb' => array(
				'callback' => 'getAlreadyGb'
			)
		),
		'columns' => array(
			'stbl_name',
			'tbl_name',
			'already_gb',
			'_operate_',
		),
	)
);
?>