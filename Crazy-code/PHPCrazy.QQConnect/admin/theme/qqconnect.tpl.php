<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/ include T('admin_header', true); ?>
		<div class="main">
			<div class="row">
				<h1 class="text-center"><?php echo L('QQC 信息'); ?></h1>
			</div>			
			<?php if ($submit): include T('error_box'); endif; ?>
			<form action="<?php echo AdminUrl('qqconnect'); ?>" method="post">
				<div class="row">
					<p><?php echo sprintf(L('QQC 后台说明'), '<a href="http://connect.qq.com/">http://connect.qq.com/</a>'); ?></p>
				</div>
				<div class="row">
					<dl class="lr">
						<label for="inputAppid"><dd class="left"><?php echo L('QQC appid'); ?></dd></label>
						<dt class="left">
							<input id="inputAppid" type="text" name="qqc_appid" value="<?php echo $GLOBALS['C']['qqc_appid']; ?>" />
						</dt>
					</dl>
					<dl class="lr">
						<label for="inputAppkey"><dd class="left"><?php echo L('QQC appkey'); ?></dd></label>
						<dt class="left">
							<input id="inputAppkey" type="text" name="qqc_appkey" value="<?php echo $GLOBALS['C']['qqc_appkey']; ?>" />
						</dt>
					</dl>
					<dl class="lr">
						<label for="inputScope"><dd class="left"><?php echo L('QQC scope'); ?></dd></label>
						<dt class="left">
							<input id="inputScope" type="text" name="qqc_scope" value="<?php echo $GLOBALS['C']['qqc_scope']; ?>" />
						</dt>
					</dl>
				</div>
				<div class="row">
					<dl class="lr">
						<dd class="left"><a href="admin.php">&laquo;<?php echo L('返回上级'); ?></a></dd>
						<dt class="left"><input type="submit" name="submit" value="<?php echo L('保存'); ?>" /></dt>
					</dl>
				</div>
			</form>
		</div>
<?php include T('admin_footer', true); ?>