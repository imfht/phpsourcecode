<style>
.td_sub{
	text-align:left; 
	padding-left:30px;
}
</style>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
		</ul>
	</div>
	<form action='/admin/system/role_column_save' method='post' onsubmit="return validateCallback(this, navTabAjaxDone)">
		<table class="table" width='40%' layoutH="75">
			<thead>
				<tr align='center'>
					<th width="20%">编号</th>
					<th>栏目名称</th>
					<th width="20%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
		<?php
			if($arrDataList){
				// 一级栏目
				foreach($arrDataList as $data){
			?>
				<tr align='center' target="sid_user" rel="<?php echo $data['id']; ?>">
					<td><?php echo $data['id']; ?></td>
					<td class='td_sub'>&nbsp;&nbsp;<?php echo $data['column_name']; ?></td>
					<td><input name='column_id[]' value='<?php echo $data['id']; ?>' <?php if(array_search($data['id'], $arrAuth) !== false){ echo 'checked=checked';} ?> type='checkbox'></td>
				</tr>		
			<?php
					// 二级栏目
					if($data['child']){
						foreach($data['child'] as $data_child){
						?>
						<tr align='center' target="sid_user" rel="<?php echo $data_child['id']; ?>">
							<td><?php echo $data_child['id']; ?></td>
							<td class='td_sub'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data_child['column_name']; ?></td>
							<td><input name='column_id[]' value='<?php echo $data_child['id']; ?>' <?php if(array_search($data_child['id'], $arrAuth) !== false){ echo 'checked=checked';} ?> type='checkbox'></td>
						</tr>						
						<?php
							// 三级栏目
							if($data_child['child']){
								foreach($data_child['child'] as $data2_child){
								?>
								<tr align='center' target="sid_user" rel="<?php echo $data2_child['id']; ?>">
									<td><?php echo $data2_child['id']; ?></td>
									<td class='td_sub'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data2_child['column_name']; ?></td>
									<td><input name='column_id[]' value='<?php echo $data2_child['id']; ?>' <?php if(array_search($data2_child['id'], $arrAuth) !== false){ echo 'checked=checked';} ?> type='checkbox'></td>
								</tr>					
								<?php
								}
							}
						?>
						<?php						
						}						
					}
				}
			}
		?>
			<tr align='center' height=30>
				<td colspan=3 height=30>
					<input type='submit' value='提交' />
				</td>
			</tr>
			</tbody>
		</table>

		<input type='hidden' name='role_id' value='<?php echo intval($_GET['role_id']); ?>' />
	</form>
</div>
