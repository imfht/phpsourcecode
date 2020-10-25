<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;
    use yii\widgets\Pjax;
    use yeesoft\helpers\Html;

    use yeesoft\grid\GridPageSize;
    use yeesoft\grid\GridQuickLinks;
    use yeesoft\grid\GridView;

    use backend\modules\mp\models\MpMenu;
    use yeesoft\models\Role;

    /**
     * @var yii\web\View $this
     * @var yii\data\ActiveDataProvider $dataProvider
     * @var backend\modules\mp\models\search\MpFansSearch $searchModel
     */

    $this->title = "菜单管理";
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="menu-index">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a('新建', ['/mp/mp-menu/create'], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a('拉取菜单信息', ['/mp/mp-menu/syn'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?= GridQuickLinks::widget([
                        'model' => MpMenu::className(),
                        'searchModel' => $searchModel,
                    ]) ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'menu-grid-pjax']) ?>
                </div>
            </div>

            <?php
                Pjax::begin([
                    'id' => 'menu-grid-pjax',
                ])
            ?>

            <?= GridView::widget([
                'id' => 'menu-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'menu-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('yee', 'Delete')]
                ],
                'columns' => [
                    ['class' => 'yeesoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                    	'attribute' => 'type',
                        'value' => function (MpMenu $model) {
                            if ( $model->type ) {
                                return $model->getMenuType()[$model->type];
                            } else {
                                return Yii::t('yee', '(Not Set)');
                            }
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function (MpMenu $model) {
                            if ( $model->type ) {
                                return $model->getMenuType()[$model->type];
                            } else {
                                return Yii::t('yee', '(Not Set)');
                            }
                        },
                        'class' => 'yeesoft\grid\columns\TitleActionColumn',
                        'title' => function (MpMenu $model) {
                            return Html::a($model->name, ['/mp/mp-menu/update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {delete}',
                    ],
                    'key',
                ],
            ]) ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>