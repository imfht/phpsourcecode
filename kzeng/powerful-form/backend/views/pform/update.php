<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Pform */

$this->title = '修改表单: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '表单', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="pform-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
