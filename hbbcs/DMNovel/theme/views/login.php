<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#login" role="tab" data-toggle="tab">用户登录</a></li>
    <li role="presentation"><a href="#register" role="tab" data-toggle="tab">注册新用户</a></li>

</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="login">

        <div class="login-div">
            <div class="panel panel-default">
                <div class="panel-body">

                    <p class="bg-warning hidden" id="error_message"></p>

                    <form class="form" id="loginForm" action="<?= site_url('/user/login') ?>" method="post">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-user"></i></span>
                            <input class="form-control" data-required="true" placeholder="USERNAME" title="用户名" name="username" type="text" maxlength="20" value=""/>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-key"></i></span>
                            <input class="form-control" data-required="true" placeholder="PASSWORD" title="密码" name="password" type="password" maxlength="32" value=""/>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-lock"></i></span>
                            <input class="form-control" data-required="true" placeholder="CAPTCHA" title="验证码" name="captcha" type="text" maxlength="4" size="20" value=""/>
                            <span class="input-group-addon" id="captchaImage"></span>
                        </div>

                        <input class="btn btn-primary btn-lg btn-block" type="submit" value="登录"/>

                    </form>
                </div>
            </div>
        </div>

    </div>
    <div role="tabpanel" class="tab-pane" id="register">


        <div class="login-div">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="bg-warning hidden" id="error_message"></p>

                    <form class="form" id="registerForm" action="<?= site_url('/user/register') ?>" method="post">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-user"></i></span>
                            <input class="form-control" data-required="true" placeholder="USERNAME" name="username" type="text" maxlength="20" value=""/>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-key"></i></span>
                            <input class="form-control" data-required="true" placeholder="PASSWORD" name="password" type="password" maxlength="32" value=""/>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-reply"></i></span>
                            <input class="form-control" data-required="true" placeholder="RE-PASSWORD" name="repassword" type="password" maxlength="32" value=""/>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-lock"></i></span>
                            <input class="form-control" data-required="true" placeholder="CAPTCHA" title="验证码" name="captcha" type="text" maxlength="4" size="20" value=""/>
                            <span class="input-group-addon" id="captchaImage"></span>
                        </div>

                        <input class="btn btn-primary btn-lg btn-block" type="submit" value="注册"/>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= THEMEPATH ?>/js/validator.js"></script>

<script type="text/javascript">
    $(function () {
        $('#captchaImage,#captchaImageRegister').load('<?=site_url('user/captcha')?>');
        $('#captchaImage,#captchaImageRegister').click(function () {
            $('#captchaImage,#captchaImageRegister').load('<?=site_url('user/captcha')?>');
        });
        //提交表单
        $('#loginForm').formValidator({
            sending: {
                type: 'ajax',
                success: function (data) {
                    var e = $.parseJSON(data);
                    if (e.error) {
                        show_error({'message': e.error, 'color': 'danger'});
                    } else {
                        show_error(e.success);
                        ajax_dialog.close();
                        $('#userMenu').html('<img src="<?=THEMEPATH?>/images/avatar/default.jpg" class="img-circle avatar-small" /> ' + e.user.name)
                            .parent('.btn-group').attr('data-user-id', e.user.id)
                            .append(
                            $('<ul>', {'class': 'dropdown-menu', 'role': 'menu', 'aria-labelledby': 'userMenu'})
                                .append(
                                $('<li>').append($('<a>', {'href': '<?=site_url('user/profile/bookmark')?>'}).html('<i class="icon-coffee"></i> 我的书架'))
                            ).append(
                                $('<li>').append($('<a>', {'href': '<?=site_url('user/profile')?>'}).html('<i class="icon-user"></i> 我的属性'))
                            ).append(
                                $('<li>', {'class': "divider"})
                            ).append(
                                $('<li>').append($('<a>', {'href': '<?=site_url('user/logout')?>'}).html('<i class="icon-signout"></i> 退出登陆'))
                            )
                        );
                    }
                },
                error: function () {
                    show_error("提交失败！");
                }
            }
        });

        //提交表单
        $('#registerForm').formValidator({
            sending: {
                type: 'ajax',
                success: function (data) {
                    var e = $.parseJSON(data);
                    if (e.error) {
                        show_error({'message': e.error, 'color': 'danger'});
                    } else {
                        show_error(e.success);
                        ajax_dialog.close();
                        $('#userMenu').html('<img src="<?=THEMEPATH?>/images/avatar/default.jpg" class="img-circle avatar-small" /> ' + e.user.name)
                            .parent('.btn-group').attr('data-user-id', e.user.id)
                            .append(
                            $('<ul>', {'class': 'dropdown-menu', 'role': 'menu', 'aria-labelledby': 'userMenu'})
                                .append(
                                $('<li>').append($('<a>', {'href': '<?=site_url('user/profile/bookmark')?>'}).html('<i class="icon-coffee"></i> 我的书架'))
                            ).append(
                                $('<li>').append($('<a>', {'href': '<?=site_url('user/profile')?>'}).html('<i class="icon-user"></i> 我的属性'))
                            ).append(
                                $('<li>', {'class': "divider"})
                            ).append(
                                $('<li>').append($('<a>', {'href': '<?=site_url('user/logout')?>'}).html('<i class="icon-signout"></i> 退出登陆'))
                            )
                        );
                    }
                },
                error: function () {
                    show_error("提交失败！");
                }
            }
        });

    });
</script>