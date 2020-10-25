<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <div class="layui-form layui-form-pane">
                    <div class="layui-form layui-form-pane">
                        <form method="post" onsubmit="return update_profile();">
                            <div class="layui-form-item">
                                <label class="layui-form-label">邮箱</label>
                                <div class="layui-input-block" style="width: 240px;">
                                    <?php if (empty($user['email'])): ?>
                                        <a class="layui-btn layui-btn layui-btn-normal" href="javascript:;" onclick="bind_email_show();">绑定邮箱</a>
                                    <?php else: ?>
                                        <input type="text" id="email" name="email" class="layui-input" value="<?=$user['email']?>" disabled title="邮箱不可更改">
                                    <?php endif;?>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">性别</label>
                                <div class="layui-input-block" style="width: 80px;">
                                    <select id="gender" name="gender">
                                        <option value="f"<?=$user['gender'] == 'f' ? ' selected' : ''?>>女</option>
                                        <option value="m"<?=$user['gender'] == 'm' ? ' selected' : ''?>>男</option>
                                        <option value="n"<?=$user['gender'] == 'n' ? ' selected' : ''?>>保密</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label for="brief" class="layui-form-label">签名</label>
                                <div class="layui-input-block">
                                    <textarea placeholder="随便写些什么刷下存在感" id="brief"  name="brief" autocomplete="off" class="layui-textarea" style="height: 80px;"><?=!empty($user['brief']) ? xss_filter($user['brief']) : ''?></textarea>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn">确认修改</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="LAY-page"></div>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>

<script type="text/javascript">
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/u/profile.min.js');
</script>

</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>