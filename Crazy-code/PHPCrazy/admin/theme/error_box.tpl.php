<?php
/*
*	Package:		PHPCrazy
*	Link:			http://git.oschina.net/yi942641
*	Author: 		yi小轩 <yi956716282@gmail.com>
*	Copyright:		2014-2015 yi小轩
*	License:		Please read the LICENSE file.
*/	if (!empty($error)): ?>
<style type="text/css">
.am-alert {margin: 10px 10px;}
</style>
				<div class="am-alert am-alert-warning">
		<?php foreach ($error as $key => $msg): ?>
					<p class="text-warning"><?php echo $msg; ?></p>
		<?php endforeach; ?>
				</div>
	<?php endif; ?>