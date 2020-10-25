<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:41:44
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:41:44
 */
 

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DdMemberGroup */

$this->title = '添加 Dd Member Group';
$this->params['breadcrumbs'][] = ['label' => 'Dd Member Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_tab') ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-member-group-create">

                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>

            </div>
        </div>
    </div>
</div>