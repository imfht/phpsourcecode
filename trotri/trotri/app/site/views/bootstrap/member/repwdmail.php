<div class="container">

<div class="col-xs-12 col-sm-10">
  <div class="row">
  <?php echo $this->getHtml()->openForm('#', 'post', array('class' => 'form-horizontal', 'id' => 'repwdmail')); ?>

    <h2 class="form-signin-heading"><?php echo $this->MOD_MEMBER_REPWD_MAIL_LABEL; ?>
    <?php if ($this->err_no > 0) : ?>
      <small class="alert" <?php if ($this->err_no > 0) : ?>style="color: #a94442;"<?php endif; ?>><?php echo $this->err_msg; ?></small>
    <?php else : ?>
      <small class="alert"></small>
    <?php endif; ?>
    </h2>

    <?php if ($this->err_no === 0) : ?>
    <div class="form-group">
      <label class="col-lg-2 control-label"><?php echo $this->MOD_MEMBER_LOGIN_LOGIN_NAME_LABEL; ?></label>
      <div class="col-lg-4">
        <?php echo $this->login_name; ?>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label"><?php echo $this->MOD_MEMBER_REGISTER_PASSWORD_LABEL; ?></label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->password('password', '', array('class' => 'form-control input-sm')); ?>
      </div>
      <span class="control-label" title="<?php echo $this->MOD_MEMBER_REGISTER_PASSWORD_HINT; ?>">
        <?php echo $this->MOD_MEMBER_REGISTER_PASSWORD_HINT; ?>
      </span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label"><?php echo $this->MOD_MEMBER_REGISTER_REPASSWORD_LABEL; ?></label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->password('repassword', '', array('class' => 'form-control input-sm')); ?>
      </div>
      <span class="control-label" title="<?php echo $this->MOD_MEMBER_REGISTER_REPASSWORD_HINT; ?>">
        <?php echo $this->MOD_MEMBER_REGISTER_REPASSWORD_HINT; ?>
      </span>
    </div>

    <?php echo $this->getHtml()->hidden('cipher', $this->cipher); ?>

    <div class="form-group">
      <label class="col-lg-2 control-label">&nbsp;&nbsp;</label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->button($this->CFG_SYSTEM_GLOBAL_CONFIRM, '', array('class' => 'btn btn-lg btn-primary btn-block', 'onclick' => 'return Member.ajaxRepwdmail();')); ?>
      </div>
    </div>

    <?php endif; ?>

  <?php echo $this->getHtml()->closeForm(); ?>
  </div>
</div>

</div><!-- /.container -->