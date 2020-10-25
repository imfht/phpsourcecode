<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <?php if ($user['verify_type'] == 1): ?>
                    <p class="t_red mb20">您还未认证，您可以进行站长认证，或使用已认证的微博认证信息进行认证（需先<a href="/u/bind">绑定微博账号</a>）。</p>
                <?php elseif ($user['verify_type'] == 2): ?>
                    <p class="t_green mb20">您已通过站长认证，认证域名：<a href="http://<?=$user['verify_details']?>" target="_blank"><?=$user['verify_details']?></a></p>
                <?php elseif ($user['verify_type'] == 3): ?>
                    <p class="t_green mb20">您已通过绑定的微博认证。</p>
                <?php endif;?>
                <div class="layui-form layui-form-pane mb30">
                    <form method="post" onsubmit="return verify_website();">
                        <div class="layui-form-item">
                            <label for="domain" class="layui-form-label">域名</label>
                            <div class="layui-input-inline">
                                <input type="text" id="domain" name="domain" class="layui-input">
                            </div>
                            <div class="layui-form-mid layui-word-aux">您需要认证的网站域名，示例：qq.com或weixin.qq.com</div>
                        </div>

                        <div class="layui-form-item">
                            <a class="layui-btn layui-btn-small layui-btn-radius" onclick="return download_website_verify_file(this);" target="_blank">下载验证文件</a>
                            <button class="layui-btn layui-btn-small layui-btn-radius layui-btn-normal" type="submit">已上传，立即认证</button>
                        </div>
                    </form>
                </div>
                <p>
                    <button class="layui-btn layui-btn-small layui-btn-radius layui-btn-danger" onclick="verify_weibo();">使用账号已绑定的微博认证信息进行认证</button>
                </p>
            </div>
            <div id="LAY-page"></div>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>

<script type="text/javascript">
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/u/verify.min.js');
</script>

</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>