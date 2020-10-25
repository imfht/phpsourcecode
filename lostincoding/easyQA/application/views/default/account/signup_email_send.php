<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <h2 class="page-title"><?=$title?></h2>
    <?php if (isset($email)): ?>
    <p><span>邮箱:<?=$email?></span></p>
    <?php endif;?>
    <ul>
        <li>请进入邮箱查看验证邮件，如果没有收到邮件，请在垃圾邮件中查看一下。</li>
        <li class="mt10"><a class="layui-btn layui-btn-small layui-btn-normal" href="<?=$email_homepage?>">进入邮箱&raquo;</a></li>
        <li class="mt10">如果还是没有接收到邮件请 <a class="layui-btn layui-btn-mini" href="/account/signup_email_send/resend">重新发送</a></li>
    </ul>
</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>