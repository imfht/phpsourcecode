<div class="container">

<div class="col-xs-12 col-sm-10">
  <div class="row">
  <?php echo $this->getHtml()->openForm('#', 'post', array('class' => 'form-horizontal', 'id' => 'repwdoldpwd')); ?>

    <h2 class="form-signin-heading"><?php echo $this->MOD_MEMBER_REPWD_OLDPWD_LABEL; ?><small class="alert"></small></h2>

    <div class="form-group">
      <label class="col-lg-2 control-label"><?php echo $this->MOD_MEMBER_REPWD_OLD_PASSWORD_LABEL; ?></label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->password('old_pwd', '', array('class' => 'form-control input-sm')); ?>
      </div>
      <span class="control-label" title=""></span>
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

    <div class="form-group">
      <label class="col-lg-2 control-label">&nbsp;&nbsp;</label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->button($this->CFG_SYSTEM_GLOBAL_CONFIRM, '', array('class' => 'btn btn-lg btn-primary btn-block', 'onclick' => 'return Member.ajaxRepwdoldpwd();')); ?>
      </div>
    </div>

  <?php echo $this->getHtml()->closeForm(); ?>
  </div>
</div>

</div><!-- /.container -->