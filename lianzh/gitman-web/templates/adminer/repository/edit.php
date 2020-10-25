<?php 
$sitetitle = 'Repository Console | ' . $sitetitle;
include $tplPath . '/header.php';
$repository_id = G::val($repository, 'id');
?>

<a href="<?php echo adminer_url('repository.list')?>">Return Repository List</a>
<div style="color: red"><?php echo $error;?></div>

<form action="<?php echo adminer_url('repository.save')?>" method="POST">
<input type="hidden" name="repository[id]" value="<?php echo $repository_id;?>">
<table>

	<caption><?php echo empty($repository_id) ? 'Add' : 'Edit'?> a Repository</caption>

	<tbody>

	<?php if (!empty($repository_id)):?>
	<tr>
		<td>id</td>	
		<td><?php echo $repository_id?></td>	
	</tr>
	<?php endif;?>
	<tr>
		<td>name</td>	
		<td><input type="text" name="repository[name]" size="120" value="<?php echo G::val($repository, 'name');?>"></td>	
	</tr>
	<tr>
		<td>url</td>	
		<td><input type="text" name="repository[url]" size="240" value="<?php echo G::val($repository, 'url');?>"></td>	
	</tr>
	<tr>
		<td>git_http_url</td>	
		<td><input type="text" name="repository[git_http_url]" size="240" value="<?php echo G::val($repository, 'git_http_url');?>"></td>	
	</tr>
	<tr>
		<td>git_ssh_url</td>	
		<td><input type="text" name="repository[git_ssh_url]" size="240" value="<?php echo G::val($repository, 'git_ssh_url');?>"></td>	
	</tr>
	<tr>
		<td>platform</td>	
		<td>
			<select name="repository[platform]"<?php if(!empty($repository_id)):?> disabled="disabled"<?php endif;?>>
				<?php
				$platform_val = G::val($repository, 'platform');
				?>
				<option value="<?php echo GitWebhook::REPO_PLATFORM_SELF?>" <?php if($platform_val == GitWebhook::REPO_PLATFORM_SELF):?>selected="selected"<?php endif;?>><?php echo GitWebhook::repoPlatformText(GitWebhook::REPO_PLATFORM_SELF)?></option>
				<option value="<?php echo GitWebhook::REPO_PLATFORM_GITEE?>" <?php if($platform_val == GitWebhook::REPO_PLATFORM_GITEE):?>selected="selected"<?php endif;?>><?php echo GitWebhook::repoPlatformText(GitWebhook::REPO_PLATFORM_GITEE)?></option>
				<option value="<?php echo GitWebhook::REPO_PLATFORM_GITHUB?>" <?php if($platform_val == GitWebhook::REPO_PLATFORM_GITHUB):?>selected="selected"<?php endif;?>><?php echo GitWebhook::repoPlatformText(GitWebhook::REPO_PLATFORM_GITHUB)?></option>
			</select>
			<?php if(!empty($repository_id)):?><input type="hidden" name="repository[platform]" value="<?php echo $platform_val?>"><?php endif;?>
		</td>	
	</tr>
	<tr>
		<td>description</td>	
		<td><textarea name="repository[description]" cols="120" rows="4"><?php echo G::val($repository, 'description');?></textarea></td>	
	</tr>

	<tr>
		<td>created_at</td>
		<td><?php echo SqlHelper::timestamp(G::val($repository, 'created_at', time()))?></td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><input type="Submit" value="Submit"></td>
	</tr>
	</tbody>
</table>
</form>

<?php if (!empty($repository_id)):?>
<table>
	<caption>Deploy List</caption>

	<thead>
		<tr>
	      <td colspan="9" style="text-align: right">
	      	<a href="<?php echo adminer_url('repository.deploy.new',array('repository_id' => $repository_id))?>">Add a deploy</a>
	      </td>
	    </tr>
	</thead>

	<tbody>
	<tr>
		<th>id</th>
		<th>name</th>
		<th>webhook_branch_ref</th>
		<th>branch_origin</th>
		<th>code_dir</th>
		<th>webhook_password</th>
		<th>xstatus</th>
		<th>extra_commands</th>
		<th>created_at</th>
	</tr>
	<?php if(!empty($deploys)):?>	
	<?php foreach ($deploys as $row): ?>
	<tr>
		<td class="cId"><?php echo $row['id'] ?></td>
		<td class="c3"><a href="<?php echo adminer_url('repository.deploy.edit',array('id'=>$row['id'],'repository_id' => $repository_id))?>"><?php echo $row['name'] ?></a></td>
		<td class="c2"><?php echo htmlspecialchars($row['webhook_branch_ref']) ?></td>
		<td class="c2"><?php echo htmlspecialchars($row['branch_origin']) ?></td>
		<td class="c2"><?php echo htmlspecialchars($row['code_dir']) ?></td>
		<td class="c2"><?php echo htmlspecialchars($row['webhook_password']) ?></td>
		<td class="c2"><?php echo GitWebhook::xstatusText($row['xstatus']) ?></td>
		<td class="c1"><textarea><?php echo $row['extra_commands']?></textarea></td>
		<td class="cId"><?php echo SqlHelper::timestamp($row['created_at'])?></td>
	</tr>
	<?php endforeach; ?>
	<?php endif;?>
	</tbody>
	
</table>
<?php endif;?>