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
            <h1 class="am-header-title"><?php echo L('重置密码'); ?></h1>
            <div class="am-header-right am-header-nav">
                <a href="<?php echo HomeUrl(); ?>" class="">
                    <i class="am-header-icon am-icon-home"></i>
                </a>
            </div>
        </header>
        <style type="text/css">
          #vld-tooltip {
            position: absolute;
            z-index: 1000;
            padding: 5px 10px;
            background: #F37B1D;
            min-width: 150px;
            color: #fff;
            transition: all 0.15s;
            box-shadow: 0 0 5px rgba(0,0,0,.15);
            display: none;
          }

          #vld-tooltip:before {
            position: absolute;
            top: -8px;
            left: 50%;
            width: 0;
            height: 0;
            margin-left: -8px;
            content: "";
            border-width: 0 8px 8px;
            border-color: transparent transparent #F37B1D;
            border-style: none inset solid;
          }
        </style>
        <div class="am-g">
        	<div class="am-u-sm-12 am-u-sm-centered">
				<form action="<?php echo HomeUrl('index.php/main:login/?action=resetpassword&key='.$activation_key); ?>" method="post">
                    <br />
                    <div class="am-input-group">
                        <span class="am-input-group-label">
                            <i class="am-icon-lock am-icon-fw"></i>
                        </span>
                        <input type="password" name="password1" class="am-form-field" value="" placeholder="<?php echo L('新密码'); ?>" data-foolish-msg="填写新密码"/>
                    </div>
                    <br />
                    <div class="am-input-group">
                        <span class="am-input-group-label">
                            <i class="am-icon-lock am-icon-fw"></i>
                        </span>
                        <input type="password" name="password2" class="am-form-field" value="" placeholder="<?php echo L('确认新密码'); ?>" data-foolish-msg="再次填写新密码"/>
                    </div>
                    <br />
                    <input type="submit" class="am-btn am-btn-success am-btn-block" name="submit" value="<?php echo L('确认'); ?>" />
    			</form>
			</div>
		</div>
<?php include T('footer'); ?>
<script type="text/javascript">
$(function() {
  var $form = $('#form-with-tooltip');
  var $tooltip = $('<div id="vld-tooltip">提示信息！</div>');
  $tooltip.appendTo(document.body);

  $form.validator();

  var validator = $form.data('amui.validator');

  $form.on('focusin focusout', '.am-input-group input', function(e) {
    if (e.type === 'focusin') {
      var $this = $(this);
      var offset = $this.offset();
      var msg = $this.data('foolishMsg') || validator.getValidationMessage($this.data('validity'));

      $tooltip.text(msg).show().css({
        left: offset.left + 10,
        top: offset.top + $(this).outerHeight() + 10
      });
    } else {
      $tooltip.hide();
    }
  });
});
</script>