<div id="home">
  <!---------------------头部------------------>
  <div id="main-header">
    <div class="main-header-container common-container">
      <a href="<?=site_url('home/index')?>"><h1><span class="mind-text">Mind</span><span class="note-text">Note</span></h1></a>

    </div>

  </div>
  <!---------------------主内容------------------>
  <div id="main-content">
    <div class="main-content-container common-container">
      <!---------------------登录框------------------>
      <div class="login">

        <form action="<?=site_url('home/login');?>" method="post" name="login">
          <div class="form-group">
            <input class="form-control" type="text" name="username" placeholder="您的用户名"
                   value="<?php echo set_value('username'); ?>" required/>
          </div>
          <?php echo form_error('username'); ?>

          <div class="form-group">
            <input class="form-control" type="password" name="password" placeholder="密码" required/>

          </div>

          <?php echo form_error('password'); ?>
          <!--
          <div class="form-group">
            <input id="captcha" class="form-control" type="text" placeholder="验证码" required/>

          </div>-->
          <div class="form-group">
            <button id="login-submit" value="login-submit" class="btn btn-default">登录</button>
          </div>
          <div class="jump-to-authority">
            <a href="<?=site_url('home/signin')?>">没有账号？点击注册</a>
          </div>
        </form>
      </div><!-- end of => login-->

      <!---------------------右边大字------------------>
      <div class="poster">
        <div class="poster-text">Mind <span class="glyphicon glyphicon-list-alt"></span> your life</div>
      </div>
    </div>

  </div><!-- end of => main-content-->

  <!-------------------------特色----------------------->
  <div id="feature" class="common-container">
    <div id="pencil" class="feature-item">
      <div class="image-box">
        <span class="glyphicon glyphicon-pencil"></span>
      </div>
      <div class="feature-title">笔记管理</div>
      <div class="feature-description">轻松管理笔记<br />告别混乱</div>
    </div>
    <div id="cloud" class="feature-item">
      <div class="image-box">
        <span class="glyphicon glyphicon-cloud"></span>
      </div>
      <div class="feature-title">云端存储</div>
      <div class="feature-description">数据放在云端<br />换个终端也可访问</div>

    </div>
    <div id="mindmap" class="feature-item">
      <div class="image-box">
        <span class="glyphicon glyphicon-fullscreen"></span>
      </div>
      <div class="feature-title">思维导图</div>
      <div class="feature-description">思维导图编辑<br />多样笔记编辑形式</div>
    </div>
  </div>

</div>

