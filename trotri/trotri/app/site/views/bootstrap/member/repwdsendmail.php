<div class="container">

<div class="col-xs-12 col-sm-10">
  <div class="row">
  <?php echo $this->getHtml()->openForm('#', 'post', array('class' => 'form-horizontal', 'id' => 'repwdsendmail')); ?>

    <h2 class="form-signin-heading"><?php echo $this->MOD_MEMBER_REPWD_MAIL_LABEL; ?><small class="alert"><?php echo $this->MOD_MEMBER_REPWD_MAIL_HINT; ?></small></h2>

    <div class="form-group">
      <label class="col-lg-2 control-label"><?php echo $this->MOD_MEMBER_REPWD_MEMBER_MAIL_LABEL; ?></label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->text('member_mail', '', array('class' => 'form-control input-sm')); ?>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label">&nbsp;&nbsp;</label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->button($this->MOD_MEMBER_REPWD_BUTTON_SEND_MAIL, '', array('class' => 'btn btn-lg btn-primary btn-block', 'onclick' => 'return Member.ajaxRepwdsendmail();')); ?>
      </div>
    </div>

  <?php echo $this->getHtml()->closeForm(); ?>
  </div>
</div>

</div><!-- /.container -->