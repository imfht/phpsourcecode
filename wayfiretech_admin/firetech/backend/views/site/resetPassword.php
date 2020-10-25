<?php
/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:39
 * @LastEditTime: 2020-04-25 02:46:00
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$settings = Yii::$app->settings;
$this->title = $settings->get('Website', 'name');
$intro = $settings->get('Website', 'intro');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firetech-main">
    <div class="panel panel-default">
    <div class="panel-heading">
            <h3 class="panel-title">修改密码</h3>
      </div>
          <div class="box-body">
                <div class="site-reset-password">
                    <div class="row">
                        <div class="col-lg-5">
                            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]); ?>

                            <div class="form-group">
                                <?= Html::submitButton('确认修改', ['class' => 'btn btn-primary']); ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
          </div>
    </div>
    
</div>