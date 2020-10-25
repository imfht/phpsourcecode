<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ZkEvent */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Zk Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zk-event-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->event_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->event_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'event_id',
            'title',
            'desc',
            'create_time',
        ],
    ]) ?>

</div>
