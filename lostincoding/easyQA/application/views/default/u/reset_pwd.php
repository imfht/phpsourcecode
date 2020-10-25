<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <?php if (!empty($user['email'])): ?>
                    <div class="layui-form layui-form-pane">
                        <form method="post" onsubmit="return account_reset_pwd();">
                            <div class="layui-form-item">
                                <label for="email" class="layui-form-label">邮箱</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="email" name="email" class="layui-input" value="<?=$user['email']?>" disabled>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label for="pwd" class="layui-form-label">原密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="pwd" name="pwd" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label for="new_pwd" class="layui-form-label">新密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="new_pwd" name="new_pwd" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <button class="layui-btn" type="submit">确认</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <p>请先 <a class="layui-btn layui-btn-mini" href="javascript:;" onclick="bind_email_show();">绑定邮箱</a></p>
                <?php endif;?>
            </div>
            <div id="LAY-page"></div>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>

<script type="text/javascript">
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/u/reset_pwd.min.js');
</script>

</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>