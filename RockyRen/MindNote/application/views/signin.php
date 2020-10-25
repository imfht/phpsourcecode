

<div id="signin">
  <!------------------------头部--------------------->
  <div id="signin-header">
    <div class="common-container">
      <a href="<?=site_url('home/index')?>"><h1><span class="mind-text">Mind</span><span class="note-text">Note</span></h1></a>
    </div>
  </div>

  <!----------------------注册标签-------------------->
  <div id="signin-label" class="common-container">
    <div class="line"></div>
    <div class="signin-title"><span>注册</span></div>
  </div>

  <!------------------------内容--------------------->
  <div id="signin-content">

    <form id="signin-form" action="<?=site_url('home/signin');?>" method="post" name="signin">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>

          <input class="form-control" type="text" name="username" placeholder="请输入用户名"
                 value="<?php echo set_value('username'); ?>" required/>
        </div>
      </div>
      <?php echo form_error('username'); ?>

<!--      <div class="alert alert-danger alert-dismissible authority-error" role="alert">-->
<!--        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
<!--        <strong>Warning!</strong> Better check yourself-->
<!--      </div>-->

      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>

          <input class="form-control" type="password" name="password" placeholder="请输入密码" required/>
        </div>
      </div>
      <?php echo form_error('password'); ?>

<!--      <div class="alert alert-danger alert-dismissible authority-error" role="alert">-->
<!--        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
<!--        <strong>Warning!</strong> Better check yourself-->
<!--      </div>-->

      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>

          <input class="form-control" type="password" name="password_confirmation" placeholder="请再次输入密码" required/>
        </div>
      </div>
      <?php echo form_error('password_confirmation'); ?>

<!--      <div class="alert alert-danger alert-dismissible authority-error" role="alert">-->
<!--        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
<!--        <strong>Warning!</strong> Better check yourself-->
<!--      </div>-->

      <div id="captcha" class="form-group">
        <input class="form-control" type="text" name="captcha" placeholder="验证码" required/>
        <span class="captcha-image"><?=$captcha?></span>
      </div>
      <?php echo form_error('captcha'); ?>

      <div id="signin-submit" class="form-group">
        <button class="btn btn-default" type="submit">注册</button>
      </div>
      <div class="jump-to-authority">
        <a href="<?=site_url('home/index')?>">已有账号？点击登录</a>
      </div>
    </form>
  </div><!--end of => signin-content-->
</div>