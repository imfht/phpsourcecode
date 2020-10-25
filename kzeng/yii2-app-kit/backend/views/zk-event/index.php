<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ZkEventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Zk Events';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zk-event-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        \yii\bootstrap\Modal::begin([
                'header' => '<h4>Event</h4>',
                'id' => 'modal',
                'size' => 'modal-md',
        ]);

        echo "<div id='modalContent'></div>";

        \yii\bootstrap\Modal::end();
    ?>

    <?= \yii2fullcalendar\yii2fullcalendar::widget(array(
        'events'=> $events,
        'options' => [
            'lang' => 'zh-cn',
            //... more options to be defined here!
        ],
        ));
    ?>

</div>
