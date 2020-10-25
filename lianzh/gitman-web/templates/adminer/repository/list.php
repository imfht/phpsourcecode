<?php 
$sitetitle = 'Repository Console | ' . $sitetitle;
include $tplPath . '/header.php';
?>
<a href="<?php echo adminer_url('index')?>">Dashboard</a>
<div style="color: red"><?php echo $error;?></div>
<table>
	<caption>Repository List</caption>

	<thead>
		<tr>
	      <td colspan="8" style="text-align: right">
	      	<a href="<?php echo adminer_url('repository.new')?>">Add a repository</a>
	      </td>
	    </tr>
	</thead>

	<tbody>
	<tr>
		<th>id</th>
		<th>name</th>
		<th>url</th>
		<th>platform</th>
		<th>git_http_url</th>
		<th>git_ssh_url</th>
		<th>description</th>
		<th>created_at</th>
	</tr>
	
	<?php foreach ($rows as $row): ?>
	<tr>
		<td class="cId"><?php echo $row['id'] ?></td>
		<td class="c2"><?php echo htmlspecialchars($row['name']) ?></td>
		<td class="c3"><a href="<?php echo adminer_url('repository.edit',array('id'=>$row['id']))?>"><?php echo $row['url'] ?></a></td>
		<td class="c2"><?php echo GitWebhook::repoPlatformText($row['platform']) ?></td>		
		<td class="c3"><?php echo htmlspecialchars($row['git_http_url']) ?></td>
		<td class="c3"><?php echo htmlspecialchars($row['git_ssh_url']) ?></td>
		<td class="c1"><?php echo htmlspecialchars($row['description'])?></td>
		<td class="cId"><?php echo SqlHelper::timestamp($row['created_at'])?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	
</table>
