<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <h2 class="page-title"><?=$title?></h2>
    <?php if (isset($email)): ?>
        <p><span>邮箱:<?=$email?></span></p>
    <?php endif;?>
    <?php if ($error_code == 'ok'): ?>
        <p class="t_green">邮箱验证成功。</p>
        <p><a class="layui-btn layui-btn-small" href="/">进入首页&raquo;</a></p>
    <?php elseif ($error_code == -1): ?>
        <p class="">邮箱验证失败，参数错误。</p>
    <?php elseif ($error_code == -2): ?>
        <p class="">邮箱验证失败，链接已经过期，请重新发送验证邮件。</p>
        <p><a class="layui-btn layui-btn-small" href="/account/signup_email_send/resend">重新发送验证邮件</a></p>
    <?php else: ?>
        <p class="t_red">邮箱验证失败，错误代码：<?=$error_code?>。</p>
    <?php endif;?>
    <?php if (empty($user)): ?>
        <p>重新发送验证邮件请先 <a class="layui-btn layui-btn-mini" href="/account/signin">登录</a></p>
    <?php endif;?>
</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>