<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Articles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a(
            //Yii::t('backend', 'Create {modelClass}', ['modelClass' => 'Article']),
            Yii::t('backend', 'Create Article', ['modelClass' => 'Article']),
            ['create'],
            ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            //'id',
            [
                'attribute' => 'id',
                'headerOptions' => array('style'=>'width:5%;'),
            ],
            //'slug',
            [
                'attribute' => 'slug',
                'headerOptions' => array('style'=>'width:10%;'),
            ],
            'title',
            [
                'attribute'=>'category_id',
                'value'=>function ($model) {
                    return $model->category ? $model->category->title : null;
                },
                'filter'=>\yii\helpers\ArrayHelper::map(\common\models\ArticleCategory::find()->all(), 'id', 'title')
            ],
            [
                'attribute'=>'author_id',
                'value'=>function ($model) {
                    return $model->author->username;
                },
                'headerOptions' => array('style'=>'width:10%;'),
            ],
            [
                'class'=>\common\grid\EnumColumn::className(),
                'attribute'=>'status',
                'enum'=>[
                    Yii::t('backend', 'Not Published'),
                    Yii::t('backend', 'Published')
                ]
            ],
            'published_at:datetime',
            'created_at:datetime',

            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete}'
            ]
        ]
    ]); ?>

</div>
