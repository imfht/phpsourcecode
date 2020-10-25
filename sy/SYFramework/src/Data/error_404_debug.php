<?php
use Sy\Http\Dispatcher;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Error</title>
	<style>
	body {
		margin: 0;
	}
	.base {
		background: #edecea;
		padding: 100px 80px;
		margin-bottom: 20px;
	}
	.base .message {
		font-weight: 300;
		font-size: 35px;
		line-height: 43px;
		margin-bottom: 15px;
	}
	.base .description {
		font-weight: 300;
		font-size: 18px;
	}
	.request {
		padding: 50px 80px;
	}
	.request .title {
		text-transform: uppercase;
		font-size: 18px;
		letter-spacing: 1px;
		padding: 0 5px 5px 5px;
		margin-bottom: 15px;
	}
	.request table {
		width: 100%;
		border-collapse: collapse;
		margin-bottom: 80px;
	}
	.request table td {
		padding: 8px 6px;
		font-size: 13px;
		color: #455275;
		border-bottom: 1px solid #e8e8e8;
		word-break: break-word;
	}
	.request table td.name {
		font-weight: 600;
		color: #999;
		width: 30%;
		text-transform: uppercase;
	}
	</style>
</head>
<body>
	<div class="base">
		<div class="message">404 Not Found</div>
		<div class="description">
			<?php
			switch ($code) {
				case Dispatcher::ROUTE_ERR_ACTION:
					echo 'ROUTE_ERR_ACTION';
					break;
				case Dispatcher::ROUTE_ERR_CONTROLLER:
					echo 'ROUTE_ERR_CONTROLLER';
					break;
				case Dispatcher::ROUTE_ERR_MODULE:
					echo 'ROUTE_ERR_MODULE';
					break;
			}
			?>
		</div>
	</div>
	<div class="request">
		<div class="title">Request</div>
		<table>
			<tr><td class="name">module</td><td><?=htmlspecialchars($request->module)?></td></tr>
			<tr><td class="name">controller</td><td><?=htmlspecialchars($request->controller)?></td></tr>
			<tr><td class="name">action</td><td><?=htmlspecialchars($request->action)?></td></tr>
			<tr><td class="name">request_uri</td><td><?=htmlspecialchars($request->server['REQUEST_URI'])?></td></tr>
			<tr><td class="name">uri</td><td><?=htmlspecialchars($request->uri)?></td></tr>
			<tr><td class="name">extension</td><td><?=htmlspecialchars($request->extension)?></td></tr>
			<tr><td class="name">param</td><td><?=htmlspecialchars(var_export($request->param, true))?></td></tr>
		</table>
		<div class="title">Server</div>
		<table>
			<?php foreach ($request->server as $k => $v) { ?>
			<tr><td class="name"><?=htmlspecialchars($k)?></td><td><?=htmlspecialchars($v)?></td></tr>
			<?php } ?>
		</table>
		<?php if (is_array($request->get)) { ?>
		<div class="title">Get</div>
		<table>
			<?php foreach ($request->get as $k => $v) { ?>
			<tr><td class="name"><?=htmlspecialchars($k)?></td><td><?=htmlspecialchars($v)?></td></tr>
			<?php } ?>
		</table>
		<?php } ?>
		<?php if (is_array($request->post)) { ?>
		<div class="title">Post</div>
		<table>
			<?php foreach ($request->post as $k => $v) { ?>
			<tr><td class="name"><?=htmlspecialchars($k)?></td><td><?=htmlspecialchars($v)?></td></tr>
			<?php } ?>
		</table>
		<?php } ?>
		<?php if (is_array($request->cookie)) { ?>
		<div class="title">Cookie</div>
		<table>
			<?php foreach ($request->cookie as $k => $v) { ?>
			<tr><td class="name"><?=htmlspecialchars($k)?></td><td><?=htmlspecialchars($v)?></td></tr>
			<?php } ?>
		</table>
		<?php } ?>
	</div>
</body>
</html>