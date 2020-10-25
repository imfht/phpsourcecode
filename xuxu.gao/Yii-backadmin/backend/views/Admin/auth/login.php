<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Material Admin</title>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-content">
<?php $this->beginBody() ?>
<!-- Login -->
<?= Html::beginForm([Url::toRoute('Admin/auth/authlogin')],'post',['id'=>'loginform']) ?>
<div class="lc-block toggled" id="l-login">
    <div class="input-group m-b-20">
        <span class="input-group-addon"><i class="md md-person"></i></span>
        <div <?php if(array_key_exists('username',$error)) echo 'class="form-group has-error"';?> >
            <div class="fg-line">
                <?php if(array_key_exists('username',$error)){ ?>
                <label class="control-label" for="inputError1"><?php echo $error['username'][0] ?></label>
                <?php }?>
                <?= Html::input('text', 'username',isset($model->username) ? $model->username : '',['class' => 'form-control','placeholder'=>'用户名','id'=>'username']) ?>
            </div>
       </div>
    </div>

    <div class="input-group m-b-20">
        <span class="input-group-addon"><i class="md md-accessibility"></i></span>
        <div <?php if(array_key_exists('password',$error)) echo 'class="form-group has-error"';?> >
        <div class="fg-line">
            <?php if(array_key_exists('password',$error)){ ?>
                <label class="control-label" for="inputError1"><?php echo $error['password'][0] ?></label>
            <?php }?>
            <?= Html::input('password', 'password','',['class' => 'form-control','placeholder'=>'密码','id'=>'password']) ?>
        </div>
       </div>
    </div>

    <div class="clearfix"></div>

    <div class="checkbox">
        <label>
            <?= Html::input('checkbox' ,'rememberMe','') ?>
            <i class="input-helper"></i>
            记住帐号密码
        </label>
    </div>

    <a href="javascript:void(0)" class="btn btn-login btn-danger btn-float loginsubmit"><i class="md md-arrow-forward"></i></a>

    <ul class="login-navigation">
        <li data-block="#l-register" class="bgm-red">Register</li>
        <li data-block="#l-forget-password" class="bgm-orange">Forgot Password?</li>
    </ul>
</div>
<?= Html::endForm() ?>
<!-- Register -->
<div class="lc-block" id="l-register">
    <div class="input-group m-b-20">
        <span class="input-group-addon"><i class="md md-person"></i></span>
        <div class="fg-line">
            <input type="text" class="form-control" placeholder="Username">
        </div>
    </div>

    <div class="input-group m-b-20">
        <span class="input-group-addon"><i class="md md-mail"></i></span>
        <div class="fg-line">
            <input type="text" class="form-control" placeholder="Email Address">
        </div>
    </div>

    <div class="input-group m-b-20">
        <span class="input-group-addon"><i class="md md-accessibility"></i></span>
        <div class="fg-line">
            <input type="password" class="form-control" placeholder="Password">
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="checkbox">
        <label>
            <input type="checkbox" value="">
            <i class="input-helper"></i>
            Accept the license agreement
        </label>
    </div>

    <a href="" class="btn btn-login btn-danger btn-float" ><i class="md md-arrow-forward"></i></a>

    <ul class="login-navigation">
        <li data-block="#l-login" class="bgm-green">Login</li>
        <li data-block="#l-forget-password" class="bgm-orange">Forgot Password?</li>
    </ul>
</div>

<!-- Forgot Password -->
<div class="lc-block" id="l-forget-password">
    <p class="text-left">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eu risus. Curabitur commodo lorem fringilla enim feugiat commodo sed ac lacus.</p>

    <div class="input-group m-b-20">
        <span class="input-group-addon"><i class="md md-email"></i></span>
        <div class="fg-line">
            <input type="text" class="form-control" placeholder="Email Address">
        </div>
    </div>

    <a href="" class="btn btn-login btn-danger btn-float"><i class="md md-arrow-forward"></i></a>

    <ul class="login-navigation">
        <li data-block="#l-login" class="bgm-green">Login</li>
        <li data-block="#l-register" class="bgm-red">Register</li>
    </ul>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<script type="text/javascript">


    $(document).ready(function(){

        $(document).on("click",".loginsubmit",function(){

           /*if(!$("#username").val()){

               swal({ title: "提示信息 ",text: "用户名不能为空!",showConfirmButton: false,type: "warning",timer:2000});

               return false;
           }
            if(!$("#password").val()){

                swal({ title: "提示信息 ",text: "密码不能为空!",showConfirmButton: false,type: "warning",timer:2000});

                return false;
            }*/

           $("#loginform").submit();

        });
    });

</script>