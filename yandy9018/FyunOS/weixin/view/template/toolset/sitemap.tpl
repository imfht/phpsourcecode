  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
  <div class="heading">
	<h2><?php echo $heading_title; ?></h2>
    <div class="buttons"><a onclick="$('#form').submit();" class="btn"><span><?php echo $button_save; ?></span></a></div>
  </div>
	 <p><?php echo $entry_description; ?></p>
  <div>
	<form action="<?php echo $action; ?>" method="post"
		enctype="multipart/form-data" id="form">
	<div id="tab_general" class="page">
	<table class="form">
		<tr>
			<td><?php echo $entry_frequency; ?></td>
			<td><select name="freq">
				<option value="" selected>None</option>
				<option value="always">Always</option>
				<option value="hourly">Hourly</option>
				<option value="daily">Daily</option>
				<option value="weekly">Weekly</option>
				<option value="monthly">Monthly</option>
				<option value="yearly">Yearly</option>
				<option value="never">Never</option>
	
			</select></td>
		</tr>
		<tr>
			<td width="30%"><?php echo $entry_priority; ?></td>
			<td align="left" width="70%">
			<select name="priority"><option value="none" selected>None</option>
			<option value="0.1">0.1</option>
			<option value="0.3">0.2</option>
			<option value="0.4">0.4</option>
			<option value="0.5">0.5</option>
			<option value="0.6">0.6</option>
			<option value="0.7">0.7</option>
			<option value="0.8">0.8</option>
			<option value="0.9">0.9</option>
			<option value="1.0">1.0</option>
			</select>
	 		</td>
		</tr>
	</table>
	</div>
	</form>
</div>

