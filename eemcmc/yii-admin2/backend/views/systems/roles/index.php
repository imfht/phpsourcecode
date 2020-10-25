<?php
/* @var $this yii\web\View */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="btn-group">
	<button type="button" class="addrole btn btn-primary">
		<i class="icon-plus"></i> 创建角色
	</button>
</div>
<div class="with-padding"></div>
<!-- 表格 -->
<div class="panel">
	<div class="table datatable table-striped"></div>
</div>

<!-- 分页容器 -->
<div id="pager"></div>


<!-- 更新角色div -->
<div id="update" class="hidden">
	<form class="form-horizontal" role="form" method="post">
		<div class="form-group">
			<label class="col-md-3 control-label">角色名称</label>
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" name="description" value="" class="description form-control">
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">角色唯一标识</label>
			<div class="col-md-4">
				<div class="input-group">
					<input class="name form-control" type="text" disabled="">
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="save btn btn-primary"><i class="icon-ok"></i> 更新</button>
		</div>
	</form>
</div>
<!-- 创建角色div -->
<div id="create" class="hidden">
	<form class="form-horizontal" role="form" method="post">
		<div class="form-group">
			<label class="col-md-3 control-label">角色名称</label>
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" name="description" value="" class="description form-control">
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">角色唯一标识</label>
			<div class="col-md-4">
				<div class="input-group">
					<input name="name" class="name form-control" type="text">
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="save btn btn-primary"><i class="icon-ok"></i> 创建</button>
		</div>
	</form>
</div>