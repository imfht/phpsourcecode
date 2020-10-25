<?php
    use yeesoft\helpers\Html;
    use yeesoft\widgets\ActiveForm;

    /**
     * @var yeesoft\widgets\ActiveForm $form
     * @var yeesoft\models\Role $model
     */
?>

<div class="material-form">
    <?php
        $form = ActiveForm::begin([
            'id' => 'material-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $form->field($model, 'description')->textInput(['maxlength' => 255, 'autofocus' => $model->isNewRecord ? true : false]) ?>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('yee', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('yee', 'Cancel'), ['/user/role/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('yee', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('yee', 'Delete'),
                                    ['delete', 'id' => $model->name], [
                                        'class' => 'btn btn-default',
                                        'data' => [
                                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>