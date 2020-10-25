<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 08:56:50
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-08 14:12:42
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

                <?php echo $form->field($model, 'backendurl'); ?>

                <?php echo $form->field($model, 'frendurl'); ?>
                <?php echo $form->field($model, 'apiurl'); ?>
                
                <?php echo $form->field($model, 'urls'); ?>

                <?php echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

