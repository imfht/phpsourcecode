<?php 
$sitetitle = 'Dashboard | ' . $sitetitle;
include $tplPath . '/header.php';
?>
<a href="<?php echo adminer_url('repository.list')?>">Repository List</a>
<div style="color: red"><?php echo $error;?></div>

<?php

$all = array(
	GitWebhook::DO_STATUS_NO => $nostarts,
	GitWebhook::DO_STATUS_START => $executings,
	GitWebhook::DO_STATUS_FAILED => $faileds,
	GitWebhook::DO_STATUS_END => $ends,	
	GitWebhook::DO_STATUS_IGNORE => $ignores,	
	GitWebhook::DO_STATUS_INVALID => $invalids,	
);

?>

<?php foreach($all as $k => $d):?>
<table>
	<caption>Hookrecord List(<?php echo GitWebhook::dostatusText($k) ?> )</caption>

	<thead>
		<tr>
	      <td colspan="13" style="text-align: right">
	      	总数量: <?php echo $d['total']?>
	      </td>
	    </tr>
	</thead>

	<tbody>
	<tr>
		<th>id</th>
		<th>created_at</th>
		<th>repository_url</th>
		<th>deploy_name</th>
		<th>platform</th>
		<th>mode</th>
		<th>commits_info</th>
		<th>webhook_branch_ref</th>
		<th>branch_origin</th>
		<th>code_dir</th>		
		<th>extra_commands</th>
		<th>do_at</th>
		<th>do_msg</th>
	</tr>
	
	<?php foreach ($d['rows'] as $row): ?>
	<tr>
		<td class="cId"><?php echo $row['id'] ?></td>
		<td class="cId"><?php echo SqlHelper::timestamp($row['created_at'])?></td>
		<td class="c2"><?php echo htmlspecialchars($repositorys[ $row['repository_id'] ]['url']) ?></td>
		<td class="c2"><?php echo htmlspecialchars($deploys[ $row['deploy_id'] ]['name']) ?></td>
		<td class="cId"><?php echo GitWebhook::repoPlatformText($repositorys[ $row['repository_id'] ]['platform']) ?></td>
		<td class="cId"><?php echo GitWebhook::deployModeText($deploys[ $row['deploy_id'] ]['mode']) ?></td>
		<td class="c2">
		<?php
		$commits = GitWebhook::getCommitsInfo($row['commits_info'], $repositorys[$row['repository_id']]['platform']);
		?>
		<?php foreach ($commits as $commit):?>
		<p style="margin-bottom: 3px;background: #efc;"><?php echo '[' . $commit['timestamp'] . ' ' .$commit['author'] . ']<br>' . $commit['id'] . '<br><b>' . htmlspecialchars($commit['message']) . '</b>'?></p>
		<?php endforeach; ?>
		</td>
		<td class="c2"><?php echo htmlspecialchars($deploys[ $row['deploy_id'] ]['webhook_branch_ref']) ?></td>
		<td class="cId"><?php echo htmlspecialchars($deploys[ $row['deploy_id'] ]['branch_origin']) ?></td>
		<td class="c2"><?php echo htmlspecialchars($deploys[ $row['deploy_id'] ]['code_dir']) ?></td>		
		<td class="c2"><?php echo htmlspecialchars($deploys[ $row['deploy_id'] ]['extra_commands']) ?></td>
		<td class="cId"><?php echo $row['do_at'] ? SqlHelper::timestamp($row['do_at']) : '--'?></td>
		<td class="c2"><?php echo htmlspecialchars($row['do_msg']) ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	
</table>

<?php endforeach; ?>