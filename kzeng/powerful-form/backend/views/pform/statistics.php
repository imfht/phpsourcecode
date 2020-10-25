<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = '表单统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pform-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => array('style'=>'width:8%;'),
            ], [
                'attribute' => 'form_img_url',
                'format' => 'html',
                'value' => function($model, $key, $index, $column){
                    if(!empty($model->form_img_url))
                        $form_img_url = '<img src=/admin' . $model->form_img_url .' width=160px height=90px>';
                    else
                        $form_img_url = '';

                    return $form_img_url;
                },
                'headerOptions' => array('style'=>'width:160px;'),
            ], 'title', [
                'label' => '填单人数',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    // $customerform_count = \backend\models\CustomerPform::find()
                    //     ->where(["pform_uid" => $model->uid])
                    //     ->count();
                    // $formfield_count = \backend\models\PformField::find()
                    //     ->where(["pform_uid" => $model->uid])
                    //     ->count();
                    // return $customerform_count/$formfield_count;
                    
                    $customerform_count = \backend\models\CustomerPform::find()
                        ->select(['customer_pform_uid'])
                        ->where(["pform_uid" => $model->uid])
                        ->distinct()
                        ->count();
                    return $customerform_count;
                },
            ], [
                'label' => '包含字段',
                'value'=>function ($model, $key, $index, $column) {

                    $formfields = \backend\models\PformField::find()
                                    ->where(["pform_uid" => $model->uid])
                                    ->all();

                    $field_str = "";
                    if(empty($formfields))
                        return $field_str;

                    foreach ($formfields as $formfield) {
                        $field_str = $field_str."【".$formfield->title."】<br>";
                    }
                    return "<span style='color:blue; font-size:12pt'>".$field_str."</span>";
                },
                'format' => 'html',
                //'filter' => \common\models\CampaignOrder::getGhOption(),
                'headerOptions' => array('style'=>'width:25%;'),
            ], [
                'class' => 'yii\grid\ActionColumn', 
                'template' => '{list}',
                'headerOptions' => array('style'=>'width:12%;'),
                'buttons' => [
                    'list' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['customer-pform/statistics', 'uid' => $model->uid]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
