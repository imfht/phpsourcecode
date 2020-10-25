<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\img */

$this->title = 'Update Img: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Imgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="img-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
