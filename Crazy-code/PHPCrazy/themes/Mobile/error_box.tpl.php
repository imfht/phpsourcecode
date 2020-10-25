<?php
/*
*   Package:        PHPCrazy
*   Link:           http://53109774.qzone.qq.com/
*   Author:         Crazy <mailzhangyun@qq.com>
*   Copyright:      2014-2015 Crazy
*   License:        Please read the LICENSE file.
*/	if (!empty($error)): ?>
                <div class="am-alert am-alert-warning" data-am-alert>
                    <button type="button" class="am-close">&times;</button>
		<?php foreach ($error as $key => $msg): ?>
					<p class="text-warning"><?php echo $msg; ?></p>
		<?php endforeach; ?>
				</div>
	<?php endif; ?>