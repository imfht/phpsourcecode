<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/10 16:50
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

?>
<div class="main-content" style="margin-top: 10%">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="login-container">
                <div class="center">
                    <h1>
                        <i class="ace-icon fa fa-leaf green"></i>
                        <span class="red">YII</span>
                        <span class="white" id="id-text2">ADMIN</span>
                    </h1>
                </div>

                <div class="space-6"></div>

                <div class="position-relative">
                    <div id="login-box" class="login-box visible widget-box no-border">
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="space-6"></div>

                                <?php $form = ActiveForm::begin(['id' => 'login-form', 'action' => Url::toRoute('site/login')]); ?>
                                <fieldset>
                                    <label class="block clearfix">
														<span class="block input-icon input-icon-right">
                                                            <?= $form->field($model, 'username')
                                                                ->textInput([
                                                                    'autofocus' => true,
                                                                    'class' => 'form-control',
                                                                    'placeholder' => '用户名'
                                                                ])
                                                                ->label(false) ?>

                                                            <i class="ace-icon fa fa-user"></i>
														</span>
                                    </label>

                                    <label class="block clearfix">
														<span class="block input-icon input-icon-right">
                                                            <?= $form->field($model, 'password')
                                                                ->textInput([
                                                                    'class' => 'form-control',
                                                                    'placeholder' => '密码'
                                                                ])
                                                                ->label(false) ?>
                                                            <i class="ace-icon fa fa-lock"></i>
														</span>
                                    </label>

                                    <div class="space"></div>


                                    <div class="clearfix">
                                        <label class="inline">
                                            <input type="checkbox" name='loginForm[rememberMe]' value="1" class="ace">

                                            <span class="lbl">记住我</span>

                                        </label>

                                        <button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
                                            <i class="ace-icon fa fa-key"></i>
                                            <span class="bigger-110">登入</span>
                                        </button>
                                    </div>

                                    <div class="space-4"></div>
                                </fieldset>
                                <?php ActiveForm::end(); ?>

                            </div><!-- /.widget-main -->

                        </div><!-- /.widget-body -->
                    </div><!-- /.login-box -->

                </div><!-- /.position-relative -->

            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.main-content -->