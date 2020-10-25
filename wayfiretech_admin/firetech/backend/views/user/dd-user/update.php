<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DdUser */

$this->title = '修改资料: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '会员', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box box-default">

<ul class="nav nav-tabs">
    <li  class="active">
        <?= Html::a('添加会员', ['create'], ['class' => 'btn btn-primary']) ?>
    </li>
    <li>
        <?= Html::a('会员管理', ['index'], ['class' => '']) ?>
    </li>
</ul>
<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-user-update">


                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
