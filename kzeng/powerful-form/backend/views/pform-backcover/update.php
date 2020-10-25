<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PformBackcover */

$this->title = '修改: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="pform-backcover-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'flag' => 'update',
    ]) ?>

</div>
