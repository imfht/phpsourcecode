<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CustomerPform */

$this->title = 'Update Customer Pform: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Pforms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customer-pform-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
