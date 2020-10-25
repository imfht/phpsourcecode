<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\doc\models\Doc;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model modules\doc\models\Doc */
/* @var $form yii\widgets\ActiveForm */

$this->title = '添加 Doc';
$this->params['breadcrumbs'][] = ['label' => 'Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?= Html::encode($this->title) ?></h4>
</div>
<div class="modal-body">

<div class="doc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pid')->dropDownList(Doc::allIdToName('id','title',['gid'=>$model->gid])) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->widget('app\widgets\WangEditor',[
        'clientOptions'=>[
            'uploadImgServer'=>Url::to(['/attachment/wang-editor']),
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
