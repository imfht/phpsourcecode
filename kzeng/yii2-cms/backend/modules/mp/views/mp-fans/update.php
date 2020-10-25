<?php
    use yeesoft\helpers\YeeHelper;
    use yeesoft\helpers\Html;
    use yeesoft\models\User;
    use yeesoft\widgets\ActiveForm;

	/**
	 * @var yii\web\View $this
	 * @var yeesoft\models\User $model
	 * @var yeesoft\widgets\ActiveForm $form
	 */

	$this->title = Yii::t('yee/fans', 'Update Fans');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('yee/mp', 'Fans'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="fans-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
	<div class="fans-form">
	    <?php
	        $form = ActiveForm::begin([
	            'id' => 'fans',
	            'validateOnBlur' => false,
	        ]);
	    ?>

	    <div class="row">
	        <div class="col-md-9">
	            <div class="panel panel-default">
	                <div class="panel-body">
	                    <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

	                    <div class="row">
	                        <div class="col-md-6">
	                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>
	                        </div>
	                        <div class="col-md-6">
	                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>
	                        </div>
	                    </div>

	                    <div class="row">
	                        <div class="col-md-4">
	                            <?= $form->field($model, 'gender')->dropDownList(User::getGenderList()) ?>
	                        </div>
	                    </div>
	                    
	                    <div class="row">
	                        <div class="col-md-3">
	                            <?= $form->field($model, 'birth_day')->textInput(['maxlength' => 2]) ?>
	                        </div>
	                        <div class="col-md-4">
	                            <?= $form->field($model, 'birth_month')->dropDownList(YeeHelper::getMonthsList()) ?>
	                        </div>
	                        <div class="col-md-3">
	                            <?= $form->field($model, 'birth_year')->textInput(['maxlength' => 4]) ?>
	                        </div>
	                    </div>

	                    <?= $form->field($model, 'info')->textarea(['maxlength' => 255]) ?>
	                </div>
	            </div>
	        </div>
	        <div class="col-md-3">
	            <div class="panel panel-default">
	                <div class="panel-body">
	                    <div class="record-info">
	                        <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(User::getStatusList()) ?>

	                        <?php if (User::hasPermission('editUserEmail')): ?>
	                            <?= $form->field($model, 'email_confirmed')->checkbox() ?>
	                        <?php endif; ?>

	                        <?= $form->field($model, 'skype')->textInput(['maxlength' => 64]) ?>

	                        <?= $form->field($model, 'phone')->textInput(['maxlength' => 24]) ?>

	                        <?php if (User::hasPermission('bindUserToIp')): ?>
	                            <?= $form->field($model, 'bind_to_ip')->textInput(['maxlength' => 255])->hint(Yii::t('yee', 'For example') . ' : 123.34.56.78, 234.123.89.78') ?>
	                        <?php endif; ?>
	                    </div>
	                </div>
	            </div>
	            <div class="panel panel-default">
	                <div class="panel-body">
	                    <div class="record-info">
	                        <div class="form-group clearfix">
	                            <label class="control-label" style="float: left; padding-right: 5px;">
	                                <?= $model->attributeLabels()['registration_ip'] ?> :
	                            </label>
	                            <span><?= $model->registration_ip ?></span>
	                        </div>
	                        <div class="form-group clearfix">
	                            <label class="control-label" style="float: left; padding-right: 5px;">
	                                <?= $model->attributeLabels()['created_at'] ?> :
	                            </label>
	                            <span><?= "{$model->createdDate} {$model->createdTime}" ?></span>
	                        </div>
	                        <div class="form-group clearfix">
	                            <label class="control-label" style="float: left; padding-right: 5px;">
	                                <?= $model->attributeLabels()['updated_at'] ?> :
	                            </label>
	                            <span><?= $model->updatedDatetime ?></span>
	                        </div>

	                        <div class="form-group ">
	                            <?php if ($model->isNewRecord): ?>
	                                <?= Html::submitButton(Yii::t('yee', 'Create'), ['class' => 'btn btn-primary']) ?>
	                                <?= Html::a(Yii::t('yee', 'Cancel'), ['/user/default/index'], ['class' => 'btn btn-default']) ?>
	                            <?php else: ?>
	                                <?= Html::submitButton(Yii::t('yee', 'Save'), ['class' => 'btn btn-primary']) ?>
	                                <?= Html::a(Yii::t('yee', 'Delete'), ['/user/default/delete', 'id' => $model->id], [
	                                    'class' => 'btn btn-default',
	                                    'data' => [
	                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
	                                        'method' => 'post',
	                                    ],
	                                ])
	                                ?>
	                            <?php endif; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <?php ActiveForm::end(); ?>
	</div>
</div>