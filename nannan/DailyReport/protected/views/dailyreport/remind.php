<div class="span-19">
	<div id="content">
		<?php echo '邮件提醒'; ?>
	</div><!-- content -->
</div>
<?php
	DailyreportController::sendCountEmails();
?>