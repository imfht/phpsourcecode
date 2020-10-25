<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <h2 class="page-title"><?=$title?></h2>
    <div class="layui-form layui-form-pane">
        <form method="post" onsubmit="return bind();">
            <?php if (!empty($encrypt_code)): ?>
                <div class="layui-form-item">
                    <label for="email" class="layui-form-label">邮箱</label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="encrypt_code" name="encrypt_code" value="<?=$encrypt_code?>">
                        <input type="text" id="email" name="email" class="layui-input" value="<?=$email?>" disabled="disabled">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="pwd" class="layui-form-label">密码</label>
                    <div class="layui-input-inline">
                        <input type="password" id="pwd" name="pwd" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <button class="layui-btn">确定</button>
                </div>
            <?php else: ?>
                <p>账号<?=$email?>密码重置成功，请<a class="layui-btn layui-btn-small" href="/account/signin">重新登录&raquo;</a></p>
            <?php endif;?>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#pwd').focus();
});
</script>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>