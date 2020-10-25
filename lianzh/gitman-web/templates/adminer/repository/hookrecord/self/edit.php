<?php 
$sitetitle = 'SelfHookrecord Console | ' . $sitetitle;
include $tplPath . '/header.php';
$deploy_id = G::val($deploy, 'id');
$repository_id = G::val($repository, 'id');
?>
<a href="<?php echo adminer_url('repository.deploy.edit', array('id'=> $deploy_id))?>">Return Deploy Console</a>
<div style="color: red"><?php echo $error;?></div>

<form action="<?php echo adminer_url('repository.hookrecord.self.save')?>" method="POST">
<input type="hidden" name="hookrecord[id]" value="">
<input type="hidden" name="hookrecord[deploy_id]" value="<?php echo $deploy_id;?>">
<input type="hidden" name="hookrecord[repository_id]" value="<?php echo $repository_id;?>">
<table>

	<caption><?php echo empty($deploy) ? 'Add' : 'Edit'?> a Hookrecord</caption>

	<tbody>

	<tr>
		<td>platform</td>	
		<td><?php echo GitWebhook::repoPlatformText($repository['platform']) ?></td>	
	</tr>
	<tr>
		<td>repository_name</td>	
		<td><?php echo htmlspecialchars($repository['name']) ?></td>	
	</tr>
	<tr>
		<td>repository_url</td>
		<td><?php echo htmlspecialchars($repository['url']) ?></td>	
	</tr>
	<tr>
		<td>deploy_name</td>	
		<td><?php echo G::val($deploy, 'name');?></td>	
	</tr>
	<tr>
		<td>webhook_branch_ref</td>	
		<td><?php echo G::val($deploy, 'webhook_branch_ref');?></td>	
	</tr>
	<tr>
		<td>branch_origin</td>	
		<td><?php echo G::val($deploy, 'branch_origin');?></td>	
	</tr>
	<tr>
		<td>mode</td>	
		<td><?php echo GitWebhook::deployModeText(G::val($deploy, 'mode'));?></td>	
	</tr>
	<tr>
		<td>code_dir</td>	
		<td><?php echo G::val($deploy, 'code_dir');?></td>	
	</tr>
	<tr>
		<td>commit_id</td>	
		<td><input type="text" name="hookrecord[commit_id]" size="50" value="<?php echo G::val($hookrecord, 'commit_id');?>"></td>
	</tr>
	<tr>
		<td>commit_author</td>	
		<td><input type="text" name="hookrecord[commit_author]" size="50" value="<?php echo G::val($hookrecord, 'commit_author');?>"></td>
	</tr>
	<tr>
		<td>commit_msg</td>	
		<td><textarea name="hookrecord[commit_msg]" cols="80" rows="5"><?php echo G::val($hookrecord, 'commit_msg');?></textarea></td>
	</tr>
	<tr>
		<td>commit_date</td>	
		<td><input type="text" name="hookrecord[commit_date]" size="50" value="<?php echo G::val($hookrecord, 'commit_date');?>"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="Submit" value="Submit"></td>
	</tr>
	</tbody>
</table>
</form>