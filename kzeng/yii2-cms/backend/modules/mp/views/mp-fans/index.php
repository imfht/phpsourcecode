<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;
    use yii\widgets\Pjax;
    use yeesoft\helpers\Html;

    use yeesoft\grid\GridPageSize;
    use yeesoft\grid\GridQuickLinks;
    use yeesoft\grid\GridView;

    use backend\modules\mp\models\MpFans;
    use yeesoft\models\Role;

    /**
     * @var yii\web\View $this
     * @var yii\data\ActiveDataProvider $dataProvider
     * @var backend\modules\mp\models\search\MpFansSearch $searchModel
     */

    $this->title = "粉丝管理";
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="fans-index">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('yee', '拉取粉丝信息'), ['/mp/mp-fans/syn'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'fans-grid-pjax']) ?>
                </div>
            </div>

            <?php
                Pjax::begin([
                    'id' => 'fans-grid-pjax',
                ])
            ?>

            <?= GridView::widget([
                'id' => 'fans-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActions' => ' ',
                'columns' => [
                    [
                        'attribute' => 'headimgurl',
                        'value' => function (MpFans $model) {
                            if(empty($model->headimgurl))
                                return Html::img('http://placehold.it/64x64');
                             else
                                return Html::img($model->headimgurl, ['width' => 64, 'height' => 64]);
                        },
                        'format' => 'html',
                    ],
                    'nickname',
                    [
                        'attribute' => 'openid',
                        'options' => ['style' => 'width:18%'],
                    ],
                    [
                        'attribute' => 'sex',
                        'class' => 'yeesoft\grid\columns\StatusColumn',
                        // 'value' => function (MpFans $model) {
                        //     //return '<img src="'.$model->headimgurl.'" width=96 height=96>';
                        //     return ($model->sex==1)?"男":"女";
                        // },
                        'optionsArray' => [
                            [0, '？', 'default'],
                            [1, '♂', 'success'],
                            [2, '♀', 'info'],
                        ],
                        // 'format' => 'html',
                        // 'options' => ['style' => 'width: 60px;'],
                    ],
                    'country',
                    'province',
                    'city',
                    [
                        'attribute' => 'subscribe_time',
                        'class' => 'yeesoft\grid\columns\DateFilterColumn',
                        'value' => function (MpFans $model) {
                            return date('Y-m-d H:i:s', $model->subscribe_time);
                        },
                        'format' => 'raw',
                        'options' => ['style' => 'width:150px'],
                    ],
                    'language',
                ],
            ]) ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>