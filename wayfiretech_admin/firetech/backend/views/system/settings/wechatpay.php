<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-14 01:26:18
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-17 08:56:55
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model \app\models\forms\ConfigurationForm */
/* @var $this \yii\web\View */

$this->title = Yii::t('app', '小程序设置');
?>


<?php echo $this->renderAjax('_tab'); ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-member-create">

                <?php $form = ActiveForm::begin(); ?>

                <?php echo $form->field($model, 'mch_id'); ?>
                <?php echo $form->field($model, 'app_id'); ?>
                <?php echo $form->field($model, 'key'); ?>
                <?php echo $form->field($model, 'notify_url'); ?>

                <?php echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>