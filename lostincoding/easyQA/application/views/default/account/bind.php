<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <?php require_once VIEWPATH . "$theme_id/account/inc/account_nav.inc.php";?>
    <div class="layui-form layui-form-pane">
        <form method="post" onsubmit="return bind();">
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
                <button class="layui-btn">立即绑定</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#email').focus();
});

//绑定已有账号
function bind(){
    var $email = $('#email');
    var $pwd = $('#pwd');
    var email = $email.val();
    var pwd = $pwd.val();

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

    layer.load();
    $.post(
        '/baseapi/account/bind',
        {
            email: email,
            pwd: pwd
        },
        function(json){
            layer.closeAll();
            if(json.error_code == 'ok'){
                layer.alert('绑定成功', {icon:0, shade:0, title:'提示'});
                setTimeout(
                    function(){
                        document.location = '/';
                    },
                    1500
                );
            }
            else{
                show_error(json.error_code);
            }
        },
        'json'
    );
    return false;
}
</script>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>