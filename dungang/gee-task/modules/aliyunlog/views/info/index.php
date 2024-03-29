<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\ListView;
use app\helpers\MiscHelper;
/* @var $this yii\web\View */
/* @var $searchModel \modules\aliyunlog\models\Query */

$projectName = $searchModel->projectName;
$logstoreName = $searchModel->logstoreName;
$this->title = $logstoreName . '@' . $projectName . "日志";
$this->params['breadcrumbs'][] = [
    'label' => '日志项目',
    'url' => [
        '/aliyun-log-project/index'
    ]
];
$this->params['breadcrumbs'][] = [
    'label' => $projectName,
    'url' => [
        '/aliyun-log-store/index',
        'projectName' => $projectName
    ]
];
$this->params['breadcrumbs'][] = $logstoreName;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project">
	<?php

$form = ActiveForm::begin([
    'method' => 'get',
    //'action'=>['index'],
    'options' => [
        'data-pjax' => 1
    ]
]);
?>
    <div class='row'>
		<div class='col-md-1'>
			<label>&nbsp;</label>
			<div class="form-group">
			<?= MiscHelper::goBackButton()?>
			</div>
		</div>
		<div class='col-md-2'>
    		<?= $form->field($searchModel, 'from')->input('datetime') ?>
    	</div>
		<div class='col-md-2'>
    		<?= $form->field($searchModel, 'to')->input('datetime') ?>
    	</div>
		<div class='col-md-2'>
        	<?= $form->field($searchModel, 'line')->dropDownList([100=>100,200=>200,500=>500]) ?>
		</div>
		<div class='col-md-4'>
    		<?= $form->field($searchModel, 'query') ?>
    	</div>
		<div class='col-md-1'>
			<label>&nbsp;</label>
			<div class="form-group">
    		<?= Html::submitButton('查询', ['class' => 'btn btn-primary btn-small']) ?>
			</div>
		</div>
	</div>
    <?php ActiveForm::end(); ?>
	    <?php

    echo ListView::widget([
        'options'=>[
            'tag'=>'pre',
            //'style'=>'border:0;background-color: #2b1c1c;color: #ad7224;border-radius: 0;padding:15px;',
            'style'=>'font-size:10px;padding:15px;'
        ],
        'dataProvider' => $dataProvider,
        //'summary' => false,
        'itemView' => function ($model, $key, $index, $widget) {
            $msgs = [];
            $msgs[] = date('Y-m-d H:i:s', $model['time']);
            $msgs[] = $model['ip'];
            $msgs[] = '<p>'.Html::encode($model['content']).'</p>';
            return implode(" ", $msgs);
        }
    ]);
    ?>
</div>