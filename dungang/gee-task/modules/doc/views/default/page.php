<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\MiscHelper;
use modules\doc\models\Doc;
use app\widgets\ZTree;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model modules\doc\models\Doc */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>