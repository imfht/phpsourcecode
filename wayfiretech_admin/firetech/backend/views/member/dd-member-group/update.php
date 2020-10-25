<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:41:57
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:41:57
 */
 

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DdMemberGroup */

$this->title = 'Update Dd Member Group: ' . $model->item_name;
$this->params['breadcrumbs'][] = ['label' => 'Dd Member Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->item_name, 'url' => ['view', 'id' => $model->item_name]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-member-group-update">


                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
