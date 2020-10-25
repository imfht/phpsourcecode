<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\img */

$this->title = 'Create Img';
$this->params['breadcrumbs'][] = ['label' => 'Imgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="img-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
