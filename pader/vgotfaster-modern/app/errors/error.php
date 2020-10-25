<?php if($exit): ?>
<html>
<head>
<title>Error</title>
<?php endif; ?>
<style type="text/css">
<?php if($exit): ?>body {background-color:#fff; margin: 40px;}<?php endif; ?>
#vgotfaster_error {border:#999 1px solid; background-color:#fff; padding:18px; font-family:Lucida Grande,Verdana,Sans-serif; font-size:14px; color:#000;}
#vgotfaster_error h1 {font-size:14px; color:#990000; margin:0; margin-bottom:10px; padding:0px;}
#vgotfaster_error p {padding:0; margin:3px 0 3px 8px;}
</style>
</head>
<body>
	<div id="vgotfaster_error">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
</body>
<?php if($exit): ?>
</html>
<?php endif; ?>