<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PformBackcover */

$this->title = '创建表单成功页面';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pform-backcover-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'flag' => 'create', 
        'uid' => $uid, 
    ]) ?>

</div>
