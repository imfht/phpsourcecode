<?php

Yii::import('zii.widgets.CPortlet');

class ActionInfo extends CPortlet
{
	public function init()
	{
		parent::init();
	}

	protected function renderContent()
	{
		$this->render('actionInfo');
	}
}