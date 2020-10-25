<meta charset="utf-8">
<title>数据库版本 Migration</title>
<style>
table{ border-collapse:collapse;border:1px solid #CCC;background:#efefef;width:100%; margin-bottom:1em;}
table th{ text-align:left; font-weight:bold; padding:.5em 2em .5em .75em; line-height:1.6em; font-size:12px; border:1px solid #CCC;}
table td{ padding:.5em .75em; line-height:1.6em; font-size:12px; border:1px solid #CCC;background-color:#fff;}
.c1{ width: 250px;}
.c2{ width: 120px;}
.c3{ width: 130px;}
.c4{ width: 130px;}
caption{ font-size:14px; font-weight:bold; line-height:2em; text-align:left; }
</style>
<script>
function changeVersion(src,dist){
	var info = '';
	var sec = dist - src;
	if (sec == 0) info = '';
	if (sec > 0) info = '预备<b> 正向 </b>迁移<i style="color: red;"> ' + sec + ' </i>个版本';
	if (sec < 0) info = '预备<b> 反向 </b>迁移<i style="color: red;"> ' + Math.abs(sec) + ' </i>个版本';
	
	document.getElementById('vc').innerHTML = info;
}
function fnOnSubmit(form) {
	
	if (form.lastversion.value == form.newversion.value) {
		alert('并未进行变更版本');
		form.newversion.focus();
		return false;
	}
	return true;
}
</script>
<table>
	<caption>Migration 管理</caption>
	
	<tbody>
		<tr><th>当前版本</th><th>迁移类</th><th>迁移类描述</th></tr>
		<tr>
			<td class="c2"><?php echo isset($migrations[$version]) ? $migrations[$version]['id']  : '&nbsp;'?></td>
			<td class="c3"><?php echo isset($migrations[$version]) ? $migrations[$version]['class']  : '&nbsp;' ?></td>
			<td class="c1"><?php echo isset($migrations[$version]) ? $migrations[$version]['instance']->description() : '&nbsp;'?></td>
		</tr>
	</tbody>
	
	<tbody>
		<tr><th>&nbsp;</th><th>操作</th><th>迁移信息描述</th></tr>
		<tr>
			<td class="c2">&nbsp;</td>
			<td class="c3">
			<form action="<?php echo $saveurl?>" method="POST" onsubmit="return fnOnSubmit(this);">
			<select name="newversion" onchange="changeVersion(<?php echo $version?>,this.value);">
							
			<?php
				// 选择迁移版本
				$items = array();$index = 1;
				foreach( $migrations as $ii )
				{
					$items[ $ii['id'] ] = $index++;
				}
				$items = array_merge(array(0 => '**回滚到原始状态**'),$items);
				
				foreach( $items as $id => $text )
				{
					$selected = $id == $version ? "selected='{$version}'" : '';
					echo "<option value='{$id}' {$selected}> {$text} </option>";
				}
			?>
			</select>
			<input type="hidden" name="lastversion" value="<?php echo $version?>">
			<input type="Submit" name="commit" value="提交">
			</form>
			</td>
			<td class="c1" id="vc"></td>
		</tr>
	</tbody>
	
</table>

<table>
	<caption>Migration 类</caption>

	<tbody>
	<tr><th>迁移标识</th><th>迁移类</th><th>迁移类描述</th></tr>
	
	<?php foreach ($migrations as $migration): ?>
	<tr>
		<td class="c2"><?php echo $migration['id'] ?></td>
		<td class="c3"><?php echo $migration['class'] ?></td>
		<td class="c1"><?php echo $migration['instance']->description() ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	
</table>