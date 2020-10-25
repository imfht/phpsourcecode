<div class="span-19">
	<div id="content">
		<?php echo '开启定时服务'; ?>
	</div><!-- content -->
</div>
<?php
	DailyreportController::sendEmails();
?>