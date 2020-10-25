<?php

namespace backend\controllers\systems;

class HistorysController extends \backend\controllers\BaseController
{

	/**
	 * 操作日志
	 * @return array
	 */
	public function actionIndex()
	{
		return $this->render('index', $this->_data);
	}

}
