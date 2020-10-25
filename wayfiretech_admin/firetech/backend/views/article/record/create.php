<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\addons\diandi_dingzuo\models\record */

$this->title = '添加 Record';
$this->params['breadcrumbs'][] = ['label' => 'Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<ul class="nav nav-tabs">
    <li class="active">
        <?= Html::a('添加 Record', ['create'], ['class' => 'btn btn-primary']) ?>
    </li>
    <li>
        <?= Html::a('Record管理', ['index'], ['class' => '']) ?>
    </li>
</ul>
<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="record-create">

                <?= $this->render('_form', [
                'model' => $model,
                ]) ?>

            </div>
        </div>
    </div>
</div>