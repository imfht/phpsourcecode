<?php
/*
*	Package:		PHPCrazy
*	Link:			http://git.oschina.net/yi942641
*	Author: 		yi小轩 <yi956716282@gmail.com>
*	Copyright:		2014-2015 yi小轩
*	License:		Please read the LICENSE file.
*/ include T('header'); ?>
<style>
.am-alert {margin: 10px 10px;}
</style>
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