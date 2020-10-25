<?php
/* @var $this yii\web\View */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- 搜索UI -->
<div class="input-group" id="search-from">
	<span class="input-group-addon"><i class="icon-user"></i></span>
	<input type="text" class="form-control"name="uid" placeholder="用户id">

	<span class="input-group-addon fix-border"><i class="icon-user"></i></span>
	<input type="text" class="form-control" name="nickname" placeholder="用户昵称">
	
	<span class="input-group-addon fix-border"><i class="icon-user"></i></span>
	<input type="text" class="form-control" name="marrier" placeholder="用户姓名">
	
	<span class="input-group-addon fix-border"><i class="icon-user"></i></span>
	<input type="text" class="form-control" name="mobile_number" placeholder="手机号码">

	<span class="input-group-addon fix-border"><i class="icon-umbrella"></i></span>
	<select name="status" class="form-control">
		<option value="0" selected="selected">用户状态</option>
		<option value="1">1.正常</option>
		<option value="2">2.封禁</option>
	</select>

	<span class="input-group-btn fix-border">
		<button id="search" class="btn btn-default"><i class="icon-search"></i> 搜索</button>
	</span>

	<span class="input-group-btn">
		<button id="remove" class="btn btn-default"><i class="icon-remove"></i> 清除</button>
	</span>
</div>

<!-- 表格 -->
<div class="panel">
	<div class="table datatable table-striped"></div>
</div>

<!-- 分页容器 -->
<div id="pager"></div>

<!-- 更新用户div -->
<div id="update" class="hidden">
	<form class="form-horizontal" role="form" method="post">
		<div class="alert alert-info">用户密码为空则表示不修改</div>
		<div class="form-group">
			<label class="col-md-2 control-label">昵称</label>
			<div class="col-md-4">
				<input type="text" name="nickname" value="" class="nickname form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">用户姓名</label>
			<div class="col-md-4">
				<input type="text" name="marrier" value="" class="marrier form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">手机号码</label>
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" name="mobile_number" value="" class="mobile_number form-control">
					<label class="radio-inline"> <input type="radio" name="is_auth" value="0" class="is_auth" checked="true"> 未认证<span class="icon-remove"></span> </label>
					<label class="radio-inline"> <input type="radio" name="is_auth" value="1" class="is_auth">已认证<span class="icon-ok"></span> </label>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">用户密码</label>
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" name="password_hash" value="" class="password_hash form-control">
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="save btn btn-primary"><i class="icon-ok"></i> 保存</button>
		</div>
	</form>
</div>
