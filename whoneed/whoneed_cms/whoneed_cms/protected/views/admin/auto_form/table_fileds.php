<div class="pageContent">
	<table class="table" width='800' layoutH="75">
		<thead>
			<tr align='center'>
				<th>序号</th>
				<th>物理字段名</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($objData){
				$i = 1;
				$j = count($objData->tableSchema->columns);
				foreach($objData->tableSchema->columns as $k=>$v){
		?>
			<tr align='center'>
				<td><?php echo $i; ?></td>
				<td><?php echo $k; ?></td>
				<td><a href='/admin/auto_form/table_filed_config/tid/<?php echo $tid; ?>/fname/<?php echo $k; ?>/c_oid/<?php echo $j; ?>' target="navTab" rel='table_filed_config'>配制此字段</a></td>
			</tr>
		<?php
					$i++;
					$j--;
				}	
			}
		?>
		</tbody>
	</table>
</div>
