<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\WxOffice */

$this->title = 'Update Wx Office: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Wx Offices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->office_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="wx-office-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
