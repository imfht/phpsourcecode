<?php

$command = 'status';

// 这里需要改成定时任务的入口文件
$file = "/Users/jlb/github/cron-manager/tests/test.php";

$str = shell_exec("php $file $command");

$table = explode("\n", str_replace(array('+','|'), '', $str));


$managerStatus = [];
$taskList = [];

foreach ($table as $v) {
	if (strpos($v, '--') !== false) {
		continue;
	}
	if (trim($v)) {
		$tr = preg_split('#\s{2,}#', $v);
		// cron信息
		if (count($tr) < 4) {
			$managerStatus[trim($tr[0])] = $tr[1];
		} 
		// task信息
		else {
			$task['id'] = trim($tr[0]);			
			$task['name'] = $tr[1];			
			$task['tag'] = $tr[2];			
			$task['status'] = $tr[3];			
			$task['count'] = $tr[4];			
			$task['last_time'] = $tr[5];			
			$task['next_time'] = $tr[6];
			$taskList[] = $task;
		}
	}
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>可视化定时任务demo</title>
	<style>
		.manager button{
			margin-right: 10px;
		}
	</style>
</head>
<body>

	<h1>控制台</h1>
	<div class="manager">
		<button>开始</button><button>停止</button><button>重启</button>
	</div>
	<br>
	
	<h1>主进程信息</h1>
	<table border="1">
		<?php foreach ($managerStatus as $key=>$v): ?>
		<tr>
			<td><?=$key?></td>
			<td><?=$v?></td>
		</tr>
		<?php endforeach ?>
	</table>
	
	<h1>定时任务列表</h1>
	<div class="manager">
		<button onclick="runCommand('run')">执行</button><button onclick="runCommand('start')">开始</button><button onclick="runCommand('stop')">停止</button>
	</div>
	<br>
	<table border="1">
		<?php foreach ($taskList as $k=>$d): ?>
		<tr>
			<?php if ($k == 0): ?>
			<th><input type="checkbox" id="checkAll"></th>
			<th><?=$d['id']?></th>
			<th><?=$d['name']?></th>
			<th><?=$d['tag']?></th>
			<th><?=$d['status']?></th>
			<th><?=$d['count']?></th>
			<th><?=$d['last_time']?></th>
			<th><?=$d['next_time']?></th>
			<?php else: ?>
			<td align="center"><input type="checkbox" value="<?=$d['id']?>" name="id"></td>
			<td><?=$d['id']?></td>
			<td><?=$d['name']?></td>
			<td><?=$d['tag']?></td>
			<td><?=$d['status']?></td>
			<td><?=$d['count']?></td>
			<td><?=$d['last_time']?></td>
			<td><?=$d['next_time']?></td>
			<?php endif ?>
			
		</tr>
		<?php endforeach ?>
	</table>
	
	<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
	<script>

		$('#checkAll').click(function(){
			$('input[type=checkbox]').prop('checked', $(this).prop('checked'));
		});


		function runCommand(command) {

			var ids = [];
			$('input[name=id]:checked').each(function(){
				ids.push($(this).val());
			});

			if (!ids.length) {
				return alert('请至少勾选一个任务');
			}


		}
	</script>
</body>
</html>