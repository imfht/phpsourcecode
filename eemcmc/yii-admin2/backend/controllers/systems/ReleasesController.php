<?php

namespace backend\controllers\systems;

class ReleasesController extends \backend\controllers\BaseController
{

	/**
	 * 版本管理
	 */
	function actionIndex()
	{
		return $this->render('index', $this->_data);
	}

}
