<?php
/* @var $this yii\web\View */

$this->title = '管理员管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="btn-group">
	<button type="button" class="addrole btn btn-primary">
		<i class="icon-plus"></i> 创建管理员
	</button>
</div>
<div class="with-padding"></div>
<!-- 表格 -->
<div class="panel">
	<div class="table datatable table-striped"></div>
</div>

<!-- 分页容器 -->
<div id="pager"></div>


<!-- 更新管理员div -->
<div id="update" class="hidden">
	<form class="form-horizontal" role="form" method="post">
		<div class="form-group">
			<label class="col-md-3 control-label">管理员</label>
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" name="username" value="" class="username form-control">
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">真实姓名</label>
			<div class="col-md-4">
				<div class="input-group">
					<input name="realname" class="realname form-control" type="text">
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">密码</label>
			<div class="col-md-4">
				<div class="input-group">
					<input name="password" class="password form-control" type="text">
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">所属角色</label>
			<div class="col-md-6">
				<select data-placeholder="请选择角色" name="roles" class="roles chosen-select form-control" tabindex="2" multiple>
					<?php foreach ($roles as $role): ?>
					<option value="<?php echo $role->name; ?>"><?php echo $role->description; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="save btn btn-primary"><i class="icon-ok"></i> 更新</button>
		</div>
	</form>
</div>

<!-- 创建管理员div -->
<div id="create" class="hidden">
	<form class="form-horizontal" role="form" method="post">
		<div class="form-group">
			<label class="col-md-3 control-label">管理员</label>
			<div class="col-md-4">
				<input type="text" name="username" value="" class="username form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">真实姓名</label>
			<div class="col-md-4">
				<input name="realname" class="realname form-control" type="text">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">密码</label>
			<div class="col-md-4">
				<input name="password" class="password form-control" type="text">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">所属角色</label>
			<div class="col-md-6">
				<select data-placeholder="请选择角色" name="roles" class="roles chosen-select form-control" tabindex="2" multiple>
					<?php foreach ($roles as $role): ?>
						<option value="<?php echo $role->name; ?>"><?php echo $role->description; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="save btn btn-primary"><i class="icon-ok"></i> 创建</button>
		</div>
	</form>
</div>