<style>
.td_sub{
	text-align:left; 
	padding-left:30px;
}
</style>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="/admin/system/column_add" target="navTab"><span>添加顶级栏目</span></a></li>
		</ul>
	</div>
	<table class="table" width='100%' layoutH="75">
		<thead>
			<tr align='center'>
				<th width="80">编号</th>
				<th width="20%">栏目名称</th>
				<th width="20%">栏目URL</th>
				<th>内容模型</th>
				<th>排序</th>
				<th>操作</th>
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
				<td><?php echo $data['column_url']; ?></td>
				<td><?php echo CF::getSystemModel($data['model_id'])->model_name; ?></td>
				<td><?php echo $data['c_order']; ?></td>
				<td>
					<?php 
						if($data['fid'] == 0){
							$strUrl = "/admin/system/column_add/fid/".$data['id'];
							echo "&nbsp;<a href='{$strUrl}' target='navTab'>添加子栏目</a>"; 
						} 
					?>
					&nbsp;<a href='/admin/system/column_edit/id/<?php echo $data['id']; ?>' target='navTab'>编辑</a>
					&nbsp;<a href='/admin/system/column_delete/id/<?php echo $data['id']; ?>' target="ajaxTodo" title="确定要删除吗?">删除</a>
				</td>
			</tr>		
			<?php
					// 二级栏目
					if($data['child']){
						foreach($data['child'] as $data_child){
						?>
						<tr align='center' target="sid_user" rel="<?php echo $data_child['id']; ?>">
							<td><?php echo $data_child['id']; ?></td>
							<td class='td_sub'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data_child['column_name']; ?></td>
							<td><?php echo $data_child['column_url']; ?></td>
							<td><?php echo CF::getSystemModel($data_child['model_id'])->model_name; ?></td>
							<td><?php echo $data_child['c_order']; ?></td>
							<td>
								<?php 
									$strUrl = "/admin/system/column_add/fid/".$data_child['id'];
									echo "&nbsp;<a href='{$strUrl}' target='navTab'>添加子栏目</a>"; 
								?>
								&nbsp;<a href='/admin/system/column_edit/id/<?php echo $data_child['id']; ?>' target='navTab'>编辑</a>
								&nbsp;<a href='/admin/system/column_delete/id/<?php echo $data_child['id']; ?>' target="ajaxTodo" title="确定要删除吗?">删除</a>
							</td>
						</tr>
						
						<?php
							// 三级栏目
							if($data_child['child']){
								foreach($data_child['child'] as $data2_child){
								?>
								<tr align='center' target="sid_user" rel="<?php echo $data2_child['id']; ?>">
									<td><?php echo $data2_child['id']; ?></td>
									<td class='td_sub'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data2_child['column_name']; ?></td>
									<td><?php echo $data2_child['column_url']; ?></td>
									<td><?php echo CF::getSystemModel($data2_child['model_id'])->model_name; ?></td>
									<td><?php echo $data2_child['c_order']; ?></td>
									<td>
										&nbsp;<a href='/admin/system/column_edit/id/<?php echo $data2_child['id']; ?>' target='navTab'>编辑</a>
										&nbsp;<a href='/admin/system/column_delete/id/<?php echo $data2_child['id']; ?>' target="ajaxTodo" title="确定要删除吗?">删除</a>
									</td>
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
		</tbody>
	</table>
</div>
