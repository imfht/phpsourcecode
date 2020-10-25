<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-22 21:27:59
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-04-30 23:44:58
 */
use common\helpers\ArrayHelper;
use diandi\admin\models\Bloc;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model \app\models\forms\ConfigurationForm */
/* @var $this \yii\web\View */

$this->title = Yii::t('app', '站点设置');
$bloc = Bloc::findAll(['status' => 1]);
?>
<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-member-create">
                <?php $form = ActiveForm::begin(); ?>

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?php echo $form->field($model, 'name'); ?>
                    <?php echo $form->field($model, 'intro'); ?>
                    <?php echo $form->field($model, 'keywords'); ?>
                    <?php echo $form->field($model, 'themcolor')->dropDownList([
                        'skin-blue' => 'skin-blue',
                        'skin-black' => 'skin-black',
                        'skin-red' => 'skin-red',
                        'skin-yellow' => 'skin-yellow',
                        'skin-purple' => 'skin-purple',
                        'skin-green' => 'skin-green',
                        'skin-blue-light' => 'skin-blue-light',
                        'skin-black-light' => 'skin-black-light',
                        'skin-red-light' => 'skin-red-light',
                        'skin-yellow-light' => 'skin-yellow-light',
                        'skin-purple-light' => 'skin-purple-light',
                        'skin-green-light' => 'skin-green-light',
                    ]); ?>


                    <?php echo $form->field($model, 'notice'); ?>
                    <?php echo $form->field($model, 'description')->textarea(['rows' => '4']); ?>

                    <?php echo $form->field($model, 'status')->radioList(['0' => '不关闭', '1' => '关闭']); ?>

                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?php echo $form->field($model, 'icp'); ?>
                    <?php echo $form->field($model, 'location'); ?>

                    <?php echo $form->field($model, 'footerright'); ?>
                    <?php echo $form->field($model, 'footerleft'); ?>
                    <?php echo $form->field($model, 'code'); ?>
                    <?php echo $form->field($model, 'statcode')->textarea(['rows' => 4]); ?>

                    <?php echo $form->field($model, 'develop_status')->radioList(['1' => '开启', '0' => '关闭']); ?>

                </div>


                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            
                    <?php echo $form->field($model, 'bloc_id')->dropDownList(ArrayHelper::getColumn($bloc, 'business_name', 'bloc_id')); ?>
                    
                    <?php echo $form->field($model, 'reason')->textarea(['rows' => 4]); ?>

                    <?php echo $form->field($model, 'flogo')->widget('common\widgets\webuploader\FileInput', []); ?>
                    <?php echo $form->field($model, 'blogo')->widget('common\widgets\webuploader\FileInput', []); ?>

                    <?php echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']); ?>

                </div>



                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>