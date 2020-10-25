<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getAmcaNameLink($data)
	{
		return $this->elements_object->getAmcaNameLink($data);
	}

	public function getAmcaPname($data)
	{
		return $this->elements_object->getAmcaNameByAmcaId($data['amca_pid']);
	}

	public function getCategoryLang($data)
	{
		return $this->elements_object->getCategoryLangByCategory($data['category']);
	}

	public function getOperate($data)
	{
		if (!$this->elements_object->isMod($data['category'])) {
			return '';
		}

		$params = array(
			'id' => $data['amca_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$synchIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_TOOL,
			'url' => $this->urlManager->getUrl('synch', $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_USERS_URLS_AMCAS_CTRLSYNCH,
		));

		$output = $modifyIcon . $removeIcon . $synchIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('users/amcas_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'amca_name' => array(
				'callback' => 'getAmcaNameLink'
			),
			'amca_pname' => array(
				'callback' => 'getAmcaPname'
			),
			'category' => array(
				'callback' => 'getCategoryLang'
			),
		),
		'columns' => array(
			'amca_name',
			// 'amca_pname',
			'prompt',
			'sort',
			'category',
			'amca_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('users/amcas_index_btns'); ?>
