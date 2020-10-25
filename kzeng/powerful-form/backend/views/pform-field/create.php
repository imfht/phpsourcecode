<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PformField */

$this->title = 'Create Pform Field';
$this->params['breadcrumbs'][] = ['label' => 'Pform Fields', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pform-field-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
