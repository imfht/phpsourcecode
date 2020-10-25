<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:42:02
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:42:03
 */
 

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DdMemberGroup */

$this->title = $model->item_name;
$this->params['breadcrumbs'][] = ['label' => 'Dd Member Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<?= $this->render('_tab') ?>

<div class=" firetech-main">
<div class="dd-member-group-view">

    <div class="panel panel-default">
        <div class="box-body">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->item_name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->item_name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'item_name',
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>

</div>
    </div>
</div>
</div>