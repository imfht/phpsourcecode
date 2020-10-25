<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 08:56:40
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-17 08:56:40
 */
 

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\forms\Email */
/* @var $form ActiveForm */
?>
<?php echo $this->renderAjax('_tab'); ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-member-create">
                <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'host') ?>
                    <?= $form->field($model, 'port') ?>
                    <?= $form->field($model, 'username') ?>
                    <?= $form->field($model, 'password') ?>
                    <?= $form->field($model, 'title') ?>
                    <?= $form->field($model, 'encryption')->radioList([
                            'tls' => 'tls',
                            'ssl' => 'ssl',
                        ], ['style' => 'padding-top:7px;']) ?>
                        
                    <div class="form-group">
                        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>

    </div>
        </div>
    </div>
</div>

