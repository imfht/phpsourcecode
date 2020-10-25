<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <div class="layui-form layui-form-pane">
                    <?php if (!empty($oschina_user)): ?>
                        <p>
                            <span>已绑定开源中国oschina账号：<?=$oschina_user['nickname']?></span>
                            <a href="javascript:;" ref="oschina" ref_name="开源中国oschina" onclick="account_unbind(this);">解除绑定</a>
                        </p>
                    <?php endif;?>

                    <?php if (!empty($github_user)): ?>
                        <p>
                            <span>已绑定Github账号：<?=$github_user['nickname']?></span>
                            <a href="javascript:;" ref="github" ref_name="Github" onclick="account_unbind(this);">解除绑定</a>
                        </p>
                    <?php endif;?>

                    <?php if (!empty($weixin_user)): ?>
                        <p>
                            <span>已绑定微信账号：<?=$weixin_user['nickname']?></span>
                            <a href="javascript:;" ref="weixin" ref_name="微信" onclick="account_unbind(this);">解除绑定</a>
                        </p>
                    <?php endif;?>

                    <?php if (!empty($qc_user)): ?>
                        <p>
                            <span>已绑定QQ账号：<?=$qc_user['nickname']?></span>
                            <a href="javascript:;" ref="qc" ref_name="QQ" onclick="account_unbind(this);">解除绑定</a>
                        </p>
                    <?php endif;?>

                    <?php if (!empty($weibo_user)): ?>
                        <p>
                            <span>已绑定微博账号：<?=$weibo_user['nickname']?></span>
                            <a href="javascript:;" ref="weibo" ref_name="微博" onclick="account_unbind(this);">解除绑定</a>
                        </p>
                    <?php endif;?>

                    <p class="open_signup_btns mt30">
                        <?php if (empty($oschina_user)): ?>
                            <a class="oschina" href="/account/oschina" title="使用开源中国oschina账号登录"><i class="iconfont">&#xe604;</i></a>
                        <?php endif;?>

                        <?php if (empty($github_user)): ?>
                            <a class="github" href="/account/github" title="使用Github账号登录"><i class="iconfont">&#xe735;</i></a>
                        <?php endif;?>

                        <?php if (empty($weixin_user)): ?>
                            <a class="weixin" href="/account/weixin" title="使用微信账号登录"><i class="iconfont">&#xe636;</i></a>
                        <?php endif;?>

                        <?php if (empty($qc_user)): ?>
                            <a class="qq_connect" href="/account/qq_connect" title="使用QQ账号登录"><i class="iconfont">&#xe616;</i></a>
                        <?php endif;?>

                        <?php if (empty($weibo_user)): ?>
                            <a class="weibo" href="/account/weibo" title="使用微博账号登录"><i class="iconfont">&#xe89c;</i></a>
                        <?php endif;?>
                    </p>
                </div>
            </div>
            <div id="LAY-page"></div>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>

<script type="text/javascript">
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/u/bind.min.js');
</script>

</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>