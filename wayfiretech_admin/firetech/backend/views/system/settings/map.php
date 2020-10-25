<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-04-30 20:51:52
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-20 18:34:25
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model \app\models\forms\ConfigurationForm */
/* @var $this \yii\web\View */

$this->title = Yii::t('app', '地图APK');
?>
<?php echo $this->renderAjax('_tab'); ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-member-create">
                <?php $form = ActiveForm::begin(); ?>

                <?php echo $form->field($model, 'baiduApk'); ?>
                <?php echo $form->field($model, 'amapApk'); ?>

                <?php echo $form->field($model, 'tencentApk'); ?>
                <div class="form-group">
                        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']); ?>
                       
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

