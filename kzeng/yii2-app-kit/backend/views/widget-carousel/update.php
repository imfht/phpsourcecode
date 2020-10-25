<?php

use yii\grid\GridView;
use yii\helpers\Html;
use vova07\imperavi\Widget;
/* @var $this yii\web\View */
/* @var $model common\models\WidgetCarousel */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Widget Carousel',
]) . ' ' . $model->key;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Widget Carousels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="widget-carousel-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

    <p>
        <?php echo Html::a(Yii::t('backend', 'Create Widget Carousel Item', [
            'modelClass' => 'Widget Carousel Item',
        ]), ['/widget-carousel-item/create', 'carousel_id'=>$model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $carouselItemsProvider,
        'columns' => [
            //'order',
            [
                'attribute' => 'order',
                'options' => ['style' => 'width: 10%']
            ],

            [
                'attribute' => 'path',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->path ? Html::img($model->getImageUrl(), ['style'=>'width: 50%']) : null;
                }
            ],
            //'url:url',
            [
                'attribute' => 'url',
                'options' => ['style' => 'width: 20%']
            ],

            [
                'format' => 'html',
                'attribute' => 'caption',
                'options' => ['style' => 'width: 20%']
            ],
            'status',

            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => '/widget-carousel-item',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>


</div>
