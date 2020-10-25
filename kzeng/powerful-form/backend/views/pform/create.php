<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Pform */

$this->title = '创建表单';
$this->params['breadcrumbs'][] = ['label' => '表单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pform-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
