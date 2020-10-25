<!doctype html>
<html lang="en">
<head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo $lang['Admin login']; ?></title>
    <link rel="shortcut icon" href="<?php echo STATICURL; ?>/image/favicon.ico?<?php echo VERHASH; ?>">
    <!-- load css -->
    <link rel="stylesheet" href="<?php echo STATICURL; ?>/css/base.css?<?php echo VERHASH; ?>">
    <!-- IE8 fixed -->
    <!--[if lt IE 9]>
    <link rel="stylesheet" href="<?php echo STATICURL; ?>/css/iefix.css?<?php echo VERHASH; ?>">
    <![endif]-->
    <!-- private css -->
    <link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/login.css?<?php echo VERHASH; ?>">
</head>
<body>
<div class="full">
    <div class="bg">
        <img id="bg" src="<?php echo $assetUrl; ?>/image/bg_body.jpg" alt="" style="display:none;">
    </div>
    <div class="mainer">
        <div class="login-wrap <?php if (!$isbindingwx): ?> unbind<?php endif;?>">
            <h1 class="logo text-center">
                <img src="<?php echo $assetUrl; ?>/image/logo.png" width="300" height="80" alt="IBOS">
            </h1>
            <div class="login shadow radius well well-white clearfix">
                <div class="login-form pull-left">
                    <form id="loginForm" method="post"
                          action="<?php echo Yii::app()->urlManager->createUrl('dashboard/default/login'); ?>">
                        <div class="login-group">
                            <span class="fsl">管理平台登录</span>
                        </div>
                        <div class="login-group">
                            <div class="input-group">
                                <span class="input-group-addon addon-icon input-large">
                                    <i class="glyphicon-user"></i>
                                </span>
                                <input
                                    type="text" <?php if (!empty($userName)): ?> value="<?php echo $userName; ?>"<?php endif; ?>
                                    name="username" id="login_user" class="input-large" placeholder="请输入用户名">
                            </div>
                        </div>
                        <div class="login-group">
                            <div class="input-group">
                                <span class="input-group-addon addon-icon input-large">
                                    <i class="glyphicon-lock"></i>
                                </span>
                                <input type="password" class="input-large" id="login_pass" name="password" placeholder="请输入密码">
                            </div>
                        </div>
                        <div class="login-group">
                            <button id="submit-btn" type="submit" data-loading-text="<?php echo $lang['Logging']; ?>..."
                                    autocomplete="off" name="loginsubmit" class="btn btn-primary btn-large btn-block">
                                <i class="o-login-tip"></i>
                                <?php echo $lang['Login']; ?>
                            </button>
                        </div>
                        <input type="hidden" name="formhash" value="<?php echo FORMHASH; ?>">
                    </form>
                </div>
                <?php if ($isbindingwx): ?>
                    <div class="scene-form pull-right">
                        <a href="https://www.ibos.com.cn/Wxapi/Api/redirect?url=aHR0cHM6Ly9vcGVuLndvcmsud2VpeGluLnFxLmNvbS93d29wZW4vc3NvLzNyZF9xckNvbm5lY3Q/YXBwaWQ9d3hiMTVhODQzZDgzZjE3NjcwJnJlZGlyZWN0X3VyaT1odHRwJTNBJTJGJTJGd3d3Lmlib3MuY29tLmNuJTJGQXBpJTJGV3hzdWl0ZWNhbGxiYWNrJTJGYXV0aCZzdGF0ZT0mdXNlcnR5cGU9YWRtaW4=" class="wx-scene-link" target="_blank">
                            <i class="wx-scene-icon mb"></i>
                            <span class="text-center fsl">微信扫码登录</span>
                        </a>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="footer">
        Powered by <strong>IBOS <?php echo VERSION; ?> <?php echo VERSION_TYPE; ?></strong>
    </div>
</div>
<!-- load js -->
<script src="<?php echo STATICURL; ?>/js/src/core.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo STATICURL; ?>/js/src/base.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo STATICURL; ?>/js/src/common.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo $assetUrl; ?>/js/login.js?<?php echo VERHASH; ?>"></script>
</body>
</html>
