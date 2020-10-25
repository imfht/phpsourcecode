<?php 
$sitetitle = 'Deploy Console | ' . $sitetitle;
include $tplPath . '/header.php';
$deploy_id = G::val($deploy, 'id');
?>
<a href="<?php echo adminer_url('repository.edit', array('id'=> $repository['id']))?>">Return Deploy List</a>
<div style="color: red"><?php echo $error;?></div>

<form action="<?php echo adminer_url('repository.deploy.save')?>" method="POST">
<input type="hidden" name="deploy[id]" value="<?php echo $deploy_id;?>">
<input type="hidden" name="deploy[repository_id]" value="<?php echo $repository['id'];?>">
<table>

	<caption><?php echo empty($deploy) ? 'Add' : 'Edit'?> a Deploy</caption>

	<tbody>

	<tr>
		<td>repository_name</td>	
		<td><?php echo htmlspecialchars($repository['name']) ?></td>	
	</tr>
	<tr>
		<td>platform</td>	
		<td><?php echo GitWebhook::repoPlatformText($repository['platform']) ?></td>	
	</tr>

	<tr>
		<td>repository_url</td>	
		<td><?php echo htmlspecialchars($repository['url']) ?></td>	
	</tr>

	<?php if (!empty($deploy_id)):?>
	<tr>
		<td>id</td>	
		<td><?php echo $deploy_id?></td>	
	</tr>
	<?php endif;?>

	<tr>
		<td>name</td>	
		<td><input type="text" name="deploy[name]" size="120" value="<?php echo G::val($deploy, 'name');?>"></td>	
	</tr>
	<tr>
		<td>webhook_branch_ref</td>	
		<td><input type="text" name="deploy[webhook_branch_ref]" size="120" value="<?php echo G::val($deploy, 'webhook_branch_ref');?>"></td>	
	</tr>
	<tr>
		<td>branch_origin</td>	
		<td><input type="text" name="deploy[branch_origin]" size="120" value="<?php echo G::val($deploy, 'branch_origin');?>"></td>	
	</tr>
	<tr>
		<td>code_dir</td>	
		<td><input type="text" name="deploy[code_dir]" size="120" value="<?php echo G::val($deploy, 'code_dir');?>"></td>	
	</tr>
	<tr>
		<td>webhook_password</td>	
		<td><input type="text" name="deploy[webhook_password]" size="120" value="<?php echo G::val($deploy, 'webhook_password');?>"></td>	
	</tr>
	<tr>
		<td>mode</td>	
		<td>
			<select name="deploy[mode]">
				<?php
				$mode_val = G::val($deploy, 'mode');
				?>
				<option value="<?php echo GitWebhook::DEPLOY_MODE_LOCAL?>" <?php if($mode_val == GitWebhook::DEPLOY_MODE_LOCAL):?>selected="selected"<?php endif;?>><?php echo GitWebhook::deployModeText(GitWebhook::DEPLOY_MODE_LOCAL)?></option>
				<option value="<?php echo GitWebhook::DEPLOY_MODE_REMOTE?>" <?php if($mode_val == GitWebhook::DEPLOY_MODE_REMOTE):?>selected="selected"<?php endif;?>><?php echo GitWebhook::deployModeText(GitWebhook::DEPLOY_MODE_REMOTE)?></option>
			</select>
		</td>	
	</tr>
	<tr>
		<td>xstatus</td>	
		<td>
			<select name="deploy[xstatus]">
				<?php
				$xstatus_val = G::val($deploy, 'xstatus');
				?>
				<option value="<?php echo GitWebhook::XSTATUS_ENABLE?>" <?php if($xstatus_val == GitWebhook::XSTATUS_ENABLE):?>selected="selected"<?php endif;?>><?php echo GitWebhook::xstatusText(GitWebhook::XSTATUS_ENABLE)?></option>
				<option value="<?php echo GitWebhook::XSTATUS_DISABLE?>" <?php if($xstatus_val == GitWebhook::XSTATUS_DISABLE):?>selected="selected"<?php endif;?>><?php echo GitWebhook::xstatusText(GitWebhook::XSTATUS_DISABLE)?></option>
			</select>
		</td>	
	</tr>
	<tr>
		<td>extra_commands</td>	
		<td><textarea name="deploy[extra_commands]" cols="120" rows="4"><?php echo G::val($deploy, 'extra_commands');?></textarea><p style="color: red;">多条命令使用 `;;;` 分隔,windows系统目录分隔符为`\`,linux为`/`</p></td>
	</tr>
	<tr>
		<td>created_at</td>
		<td><?php echo SqlHelper::timestamp(G::val($deploy, 'created_at', time()))?></td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><input type="Submit" value="Submit"></td>
	</tr>
	</tbody>
</table>
</form>

<?php if (!empty($deploy_id) && GitWebhook::REPO_PLATFORM_SELF == $repository['platform']):?>
<table>
	<caption>Hookrecord List</caption>

	<thead>
		<tr>
	      <td colspan="6" style="text-align: right">
	      	<?php if($xstatus_val == GitWebhook::XSTATUS_ENABLE):?>
	      	<a href="<?php echo adminer_url('repository.hookrecord.self.new',array('repository_id' => $repository['id'],'deploy_id' => $deploy_id))?>">Add a Hookrecord</a>&nbsp;&nbsp;
	      	<?php endif;?>
	      	总数量: <?php echo $hookrecords['total']?>
	      </td>
	    </tr>
	</thead>

	<tbody>
	<tr>
		<th>id</th>
		<th>mode</th>
		<th>created_at</th>
		<th>commits_info</th>
		<th>do_status</th>
		<th>do_at</th>
		<th>do_msg</th>
	</tr>
	
	<?php foreach ($hookrecords['rows'] as $row): ?>
	<tr>
		<td class="cId"><?php echo $row['id'] ?></td>
		<td class="cId"><?php echo GitWebhook::deployModeText($row['mode'])?></td>
		<td class="cId"><?php echo SqlHelper::timestamp($row['created_at'])?></td>
		<td class="c2">
		<?php
		$commits = GitWebhook::getCommitsInfo($row['commits_info'], $repository['platform']);
		?>
		<?php foreach ($commits as $commit):?>
		<p style="margin-bottom: 3px;background: #efc;"><?php echo '[' . $commit['timestamp'] . ' ' .$commit['author'] . ']<br>' . $commit['id'] . '<br><b>' . htmlspecialchars($commit['message']) . '</b>'?></p>
		<?php endforeach; ?>
		</td>
		<td class="cId"><?php echo GitWebhook::dostatusText($row['do_status'])?></td>
		<td class="cId"><?php echo $row['do_at'] ? SqlHelper::timestamp($row['do_at']) : '--'?></td>
		<td class="c2"><?php echo htmlspecialchars($row['do_msg']) ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	
</table>
<?php endif;?>