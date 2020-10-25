<?php
/*
* Package:    PHPCrazy
* Link:       http://53109774.qzone.qq.com/
* Author:     Crazy <mailzhangyun@qq.com>
* Copyright:  2014-2015 Crazy
* License:    Please read the LICENSE file.
*/ include T('header'); ?>
        <header data-am-widget="header" class="am-header am-header-default">
            <div class="am-header-left am-header-nav">
              <a href="<?php echo HomeUrl(); ?>" class="" data-am-modal="{target: '#my-actions'}">
                <i class="am-header-icon am-icon-arrow-left"></i>
              </a>
            </div>
            <h1 class="am-header-title"><?php echo L('登录'); ?></h1>
            <div class="am-header-right am-header-nav">
                <a href="<?php echo HomeUrl(); ?>" class="">
                    <i class="am-header-icon am-icon-home"></i>
                </a>
            </div>
        </header>
        <?php if ($submit): include T('error_box'); endif; ?>
        <div class="am-g">
        		<div class="am-u-sm-12 am-u-sm-centered">
          		<form action="<?php echo $form_action; ?>" method="post" class="am-form">
      	    		<div class="am-animation-fade am-animation-slide-left">
                  <br />
                  <div class="am-input-group">
                    <span class="am-input-group-label">
                      <i class="am-icon-user am-icon-fw"></i>
                    </span>
                    <input type="text" name="account" class="am-form-field" placeholder="<?php echo L('邮箱 ID 用户名'); ?>" value="" />
                  </div>
      	      		<br />
                  <div class="am-input-group">
                    <span class="am-input-group-label"><i class="am-icon-lock am-icon-fw"></i></span>
                    <input type="text" type="password" name="password" class="am-form-field" placeholder="<?php echo L('输入密码'); ?>">
                  </div>
                  <br />
                  <input type="submit" name="submit" class="am-btn am-btn-primary am-btn-block" value="<?php echo L('登录'); ?>" />
                  <a class="am-btn am-btn-default am-btn-block" href="<?php echo HomeUrl('index.php/main:login/?action=forgetpassword'); ?>">
                    <?php echo L('忘记密码'); ?>？
                  </a>
          		</form>
        		</div>
      	</div>
<?php include T('footer'); ?>