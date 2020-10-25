<?php
    use yii\helpers\Html;
    use yeesoft\widgets\ActiveForm;

    /**
     * @var yii\web\View $this
     * @var backend\modules\mp\models\Mp $model
     */

    $this->title = Yii::t('yee/user', 'Wechat MP Config');
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="mp-config">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="mp-form">
                <?php $form = ActiveForm::begin([
                    'id' => 'mp',
                    'layout' => 'horizontal',
                ]); ?>

                <?= $form->field($model, 'title')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                <?= $form->field($model, 'appid')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                <?= $form->field($model, 'appsecret')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                <?= $form->field($model, '')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                <?= $form->field($model, 'gh')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <?= Html::submitButton(Yii::t('yee', 'Save'), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>