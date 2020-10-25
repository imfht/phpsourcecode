<?php

namespace api\modules\officialaccount\controllers;

use api\controllers\AController;

/**
* Default controller for the `wechat` module
*/
class DefaultController extends AController
{
/**
* Renders the index view for the module
* @return string
*/
public function actionIndex()
{
return $this->render('index');
}
}