<?php
    use yeesoft\helpers\Html;
    use yeesoft\widgets\ActiveForm;
    use backend\modules\mp\models\MpMenu;

    /**
     * @var yeesoft\widgets\ActiveForm $form
     * @var yeesoft\models\Role $model
     */
?>

<div class="menu-form">
    <?php
        $form = ActiveForm::begin([
            'id' => 'menu-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $form->field($model, 'type')->dropDownList(MpMenu::getMenuType()) ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'key')->textInput(['maxlength' => 64]) ?>

                    <?= $form->field($model, 'type')->dropDownList(MpMenu::getMenuType()) ?>
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
                                <?= Html::a(Yii::t('yee', 'Cancel'), ['/user/menu/index'], ['class' => 'btn btn-default']) ?>
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