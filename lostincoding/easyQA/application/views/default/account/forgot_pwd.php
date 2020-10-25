<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <h2 class="page-title"><?=$title?></h2>
    <div class="layui-form layui-form-pane">
    	<form method="post">
            <?php if (empty($email)): ?>
        		<div class="layui-form-item">
        			<label for="email" class="layui-form-label">邮箱</label>
        			<div class="layui-input-inline">
        				<input type="text" id="email" name="email" class="layui-input">
        			</div>
        		</div>

        		<div class="layui-form-item">
        			<button class="layui-btn">发送验证邮件</button>
        		</div>
            <?php else: ?>
                <p><span>邮箱:<?=$email?></span></p>
                <ul>
                    <li>请进入邮箱查看验证邮件，如果没有收到邮件，请在垃圾邮件中查看一下。</li>
                    <li class="mt10"><a class="layui-btn layui-btn-small layui-btn-normal" href="<?=$email_homepage?>">进入邮箱&raquo;</a></li>
                    <li class="mt10">如果还是没有接收到邮件请 <a class="layui-btn layui-btn-mini" href="/account/forgot_pwd">重新发送</a></li>
                </ul>
            <?php endif;?>
    	</form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#email').focus();
});
</script>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>