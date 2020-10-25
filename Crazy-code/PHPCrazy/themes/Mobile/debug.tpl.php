<?php
/*
*	Package:		PHPCrazy
*	Link:			http://53109774.qzone.qq.com/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/ include T('header'); ?>
<style type="text/css">
.am-alert {margin: 10px 10px;}
</style>
		<div class="main">
			<div class="am-alert am-alert-warning">
				<strong><?php echo $msg; ?></strong>
				<dl class="lr">
					<strong><?php echo L('行'); ?>:</strong>
					<?php echo $line; ?>
				</dl>
				<dl class="lr">
					<strong><?php echo L('文件'); ?>:</strong>
					<?php echo $file; ?>
				</dl>
			</div>
		</div>
<?php include T('footer'); ?>