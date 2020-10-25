<?php
namespace app\controllers;

use app\core\BaseController;
use app\filters\AccessFilter;

abstract class AdminController extends BaseController
{

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors[AccessFilter::ID] = AccessFilter::className();
        return $behaviors;
    }
}

