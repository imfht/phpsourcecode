<form id="pagerForm" method="post" action="demo_page1.html">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keywords" value="${param.keywords}" />
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="${model.numPerPage}" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
</form>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="<?= URL("generate/modelConfirm"); ?>?model={model}" target="navTab" rel="modelConfirm"><span>生成model</span></a></li>
			<li><a class="add" href="<?= URL("generate/controllerConfirm"); ?>?controller={model}" target="navTab" rel="controllerConfirm"><span>生成controller</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
			<tr>
				<th width="80">表名</th>
				<th width="100">对应model</th>
				<th width="100">是否已生成</th>
				<th width="100">对应控制器</th>
				<th width="100">是否已生成</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($tables as $key => $value) {?>
			<tr target="model" rel="<?php echo $value["table"]; ?>">
					<td><?php echo $value["table"]; ?></td>
					<td><?php echo $value["moderName"]; ?></td>
					<td>
						<?php if ($value["isModel"]) { ?>
							是
						<?php }else{ ?>
							否
						<?php } ?>
					</td>
					<td><?php echo $value["controllerName"]; ?></td>
					<td>
						<?php if ($value["isController"]) { ?>
							是
						<?php }else{ ?>
							否
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="20">20</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="200">200</option>
			</select>
			<span>条，共${totalCount}条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="200" numPerPage="20" pageNumShown="10" currentPage="1"></div>
	</div>
</div>

