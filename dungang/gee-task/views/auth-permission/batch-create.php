<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\AuthRule;

/* @var $this yii\web\View */
/* @var $model app\models\AuthPermission */

$this->title = '添加权限';
$this->params['breadcrumbs'][] = ['label' => '权限', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$rules = AuthRule::allIdToName('name', 'name');
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><?= Html::encode($this->title) ?></h4>
</div>
<div class="modal-body">

    <div class="auth-permission-form">

        <?php $form = ActiveForm::begin(['id' => 'auth-permissions-form']); ?>
        <table id="data-form" class="table table-bordered" data-index="<?= count($models) ?>" data-target="tr">
            <tr>
                <th>名称</th>
                <th>说明</th>
                <th>上级权限</th>
                <th>规则</th>
                <td></td>
            </tr>
            <?php foreach ($models as $i => $model) : ?>
                <tr>
                    <td>
                        <?= $form->field($model, "[$i]name")->label(false)->textInput(['maxlength' => true]) ?>
                    </td>
                    <td>
                        <?= $form->field($model, "[$i]description")->label(false)->textInput(['maxlength' => true]) ?>
                    </td>
                    <td>
                        <?= $form->field($model, "[$i]parent")->label(false)->textInput(['maxlength' => true]) ?>
                    </td>
                    <td>
                        <?= $form->field($model, "[$i]rule_name")->label(false)->dropDownList($rules, ['prompt' => '']) ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="delete-self btn btn-sm btn-danger"><i class=" glyphicon glyphicon-trash"></i></a>
                        <a href="javascript:void(0);" class="copy-self btn btn-sm btn-success"><i class="glyphicon glyphicon-plus"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); 
        $this->registerJs("$('#data-form').dynamicline()");
        ?>

    </div>

</div>