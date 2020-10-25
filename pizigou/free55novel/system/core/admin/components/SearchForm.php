<?php

Yii::import('zii.widgets.CPortlet');

class SearchForm extends CPortlet
{
	public $categorys;
	public function init()
	{
		parent::init();
	}

	protected function renderContent()
	{
		$this->render('searchForm',array(
			'categorys'=>$this->categorys,
		));
	}
}