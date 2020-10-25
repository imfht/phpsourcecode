<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <?php require_once VIEWPATH . "$theme_id/account/inc/sign_nav.inc.php";?>
    <div class="layui-form layui-form-pane">
    	<form method="post" onsubmit="return signin();">
    		<div class="layui-form-item">
    			<label for="email" class="layui-form-label">邮箱</label>
    			<div class="layui-input-inline">
    				<input type="text" id="email" name="email" class="layui-input">
    			</div>
    		</div>

    		<div class="layui-form-item">
    			<label for="pwd" class="layui-form-label">密码</label>
    			<div class="layui-input-inline">
    				<input type="password" id="pwd" name="pwd" class="layui-input">
    			</div>
    		</div>

    		<div class="layui-form-item">
    			<button class="layui-btn" type="submit">立即登录</button>
    			<span style="padding-left:20px;">
    				<a href="/account/forgot_pwd">忘记密码？</a>
    			</span>
    		</div>
    	</form>
    </div>
    <div class="mt30">
    	<?php require VIEWPATH . "$theme_id/inc/open_signin_btn_lists.inc.php";?>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#email').focus();
});

//登录
function signin(){
    var $email = $('#email');
    var $pwd = $('#pwd');
    var $remember_me = $('#remember_me');
    var email = $email.val();
    var pwd = $pwd.val();
    var remember_me = 0;
    if($remember_me.is(':checked')){
        remember_me = 1;
    }

    if(!simple_validate.required(email)){
        layer.msg('请填写邮箱');
        $email.focus();
        return false;
    }

    if(!simple_validate.email(email)){
        layer.msg('请填写正确的邮箱');
        $email.focus();
        return false;
    }

    if(!simple_validate.range(email, 0, 30)){
        layer.msg('邮箱最大长度为30');
        $email.focus();
        return false;
    }

    if(!simple_validate.required(pwd)){
        layer.msg('请填写密码');
        $pwd.focus();
        return false;
    }

    if(!simple_validate.range(pwd, 6, 16)){
        layer.msg('密码长度为6-16位');
        $pwd.focus();
        return false;
    }

    var post_data = {
        email: email,
        pwd: pwd,
        remember_me: remember_me
    };
    //Geetest验证码
    var $captcha = $('#captcha');
    if($captcha.length > 0){
        post_data.geetest_challenge = $(':input[name=geetest_challenge]').val();
        post_data.geetest_validate = $(':input[name=geetest_validate]').val();
        post_data.geetest_seccode = $(':input[name=geetest_seccode]').val();
    }

    layer.load();
    $.post(
        '/baseapi/account/signin',
        post_data,
        function(json){
            if(json.error_code == 'ok'){
                document.location = '/';
            }
            else{
                show_error(json.error_code);
                layer.closeAll('loading');
            }
        },
        'json'
    );

    return false;
}
</script>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>