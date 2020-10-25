<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ZkEvent */

$this->title = 'Create Zk Event';
$this->params['breadcrumbs'][] = ['label' => 'Zk Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zk-event-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
