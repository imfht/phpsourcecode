<?php
/*
*   Package:        PHPCrazy
*   Link:           http://53109774.qzone.qq.com/
*   Author:         Crazy <mailzhangyun@qq.com>
*   Copyright:      2014-2015 Crazy
*   License:        Please read the LICENSE file.
*/ include T('header'); ?>
        <header data-am-widget="header" class="am-header am-header-default">
            <div class="am-header-left am-header-nav">
              <a href="<?php echo HomeUrl(); ?>" class="" data-am-modal="{target: '#my-actions'}">
                <i class="am-header-icon am-icon-arrow-left"></i>
              </a>
            </div>
        </header>
<?php if ($level == SUCCESS): ?>
		<div class="am-alert am-alert-success">
			<p class="text-success"><?php echo $message; ?></p>
		</div>
<?php elseif ($level == INFO): ?>
		<div class="am-alert am-alert-success">
			<p class="text-info"><?php echo $message; ?></p>
		</div>
<?php elseif ($level == WARNING): ?>
		<div class="am-alert am-alert-warning">
			<p class="text-warning"><?php echo $message; ?></p>
		</div>
<?php elseif ($level == DANGER): ?>
		<div class="am-alert am-alert-danger">
			<p class="text-danger"><?php echo $message; ?></p>
		</div>
<?php else: ?>
		<div class="am-alert am-alert-danger">
			<p class="text-info"><?php echo $message; ?></p>
		</div>
<?php endif; ?>
<?php include T('footer'); ?>