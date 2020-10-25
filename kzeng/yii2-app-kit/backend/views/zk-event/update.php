<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ZkEvent */

$this->title = 'Update Zk Event: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Zk Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->event_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="zk-event-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
