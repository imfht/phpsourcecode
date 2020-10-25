<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;
    use yii\widgets\Pjax;
    use yeesoft\helpers\Html;

    use yeesoft\grid\GridPageSize;
    use yeesoft\grid\GridQuickLinks;
    use yeesoft\grid\GridView;

    use backend\modules\mp\models\MpMaterial;
    use yeesoft\models\Role;

    /**
     * @var yii\web\View $this
     * @var yii\data\ActiveDataProvider $dataProvider
     * @var backend\modules\mp\models\search\MpFansSearch $searchModel
     */

    $this->title = "素材管理";
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="material-index">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('yee', '拉取图片素材信息'), ['/mp/mp-material/synimages'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'material-grid-pjax']) ?>
                </div>
            </div>

            <?php
                Pjax::begin([
                    'id' => 'material-grid-pjax',
                ])
            ?>

            <?= GridView::widget([
                'id' => 'fans-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActions' => ' ',
                'columns' => [
                    //'url',
                    // [
                    //     'attribute' => 'url',
                    //     'value' => function (MpMaterial $model) {
                    //         if(empty($model->url))
                    //             return Html::img('http://placehold.it/240x160');
                    //          else
                    //             return Html::img($model->url, ['width' => 240, 'height' => 160]);
                    //     },
                    //     'format' => 'html',
                    // ],

                    [
                        'attribute' => 'url',
                        'value' => function (MpMaterial $model) {
      
                            if(stripos($model->name,"mybookgoal")!== false)
                            {
                                $myurl = explode('public', $model->name);
                                return Html::img('http://2016.bookgo.com.cn'.$myurl[1], ['width' => 200, 'height' => 120]);
                            }
                            else
                            {
                                //官方上传无法预览
                                return Html::img('http://placehold.it/200x120');
                            }


                        },
                        'format' => 'html',
                    ],

                    [
                        'attribute' => 'media_id',
                        'value' => function (MpMaterial $model) {
                                return $model->media_id;
                        },
                        'class' => 'yeesoft\grid\columns\TitleActionColumn',
                        'title' => function (MpMaterial $model) {
                            return Html::a( $model->media_id . ' 删除', ['/mp/mp-material/delete', 'media_id' => $model->media_id]);
                        },
                        'buttonsTemplate' => ' ',
                    ],
                    [
                        'attribute' => 'update_time',
                        'class' => 'yeesoft\grid\columns\DateFilterColumn',
                        'value' => function (MpMaterial $model) {
                            return date('Y-m-d H:i:s', $model->update_time);
                        },
                        'options' => ['style' => 'width:18%'],
                        'format' => 'raw',
                    ],
                ],
            ]) ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>