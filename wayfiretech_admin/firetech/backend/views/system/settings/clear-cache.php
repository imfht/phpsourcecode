<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-04 19:00:54
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:38:47
 */
use yii\widgets\ActiveForm;

$this->title = '清理缓存';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="firetech-main">

    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">基本信息</h3>
                </div>
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}{hint}{error}</div>",
                    ],
                ]); ?>
                <div class="box-body">
                    <?= $form->field($model, 'cache')->checkbox(); ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">保存</button>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>