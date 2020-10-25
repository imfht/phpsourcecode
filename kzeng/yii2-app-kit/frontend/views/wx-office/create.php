<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\WxOffice */

$this->title = 'Create Wx Office';
$this->params['breadcrumbs'][] = ['label' => 'Wx Offices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-office-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
