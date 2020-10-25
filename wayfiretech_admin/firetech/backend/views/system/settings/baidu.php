<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 08:56:35
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-17 08:56:35
 */
 

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model \app\models\forms\ConfigurationForm */
/* @var $this \yii\web\View */

$this->title = Yii::t('app', '百度SDK设置');
?>
<?php echo $this->renderAjax('_tab'); ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-member-create">
                <?php $form = ActiveForm::begin(); ?>

                <?php echo $form->field($model, 'name'); ?>
                <?php echo $form->field($model, 'APP_ID'); ?>

                <?php echo $form->field($model, 'API_KEY'); ?>
                <?php echo $form->field($model, 'SECRET_KEY'); ?>

                <?php echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

