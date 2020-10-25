<?php
/* @var $this yii\web\View */

$this->title = '版本发布管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="btn-group">
	<button type="button" class="addrole btn btn-primary">
		<i class="icon-plus"></i> 创建新版本
	</button>
</div>
<div class="with-padding"></div>
<!-- 表格 -->
<div class="panel">
	<div class="table datatable table-striped"></div>
</div>

<!-- 分页容器 -->
<div id="pager"></div>


<!-- 创建管理员div -->
<div id="create" class="hidden">
	<form class="form-horizontal" role="form" method="post">
		<div class="form-group">
			<label class="col-md-3 control-label">版本名称</label>
			<div class="col-md-4">
				<input type="text" name="name" value="" class="name form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">状态</label>
			<div class="col-md-4">
				<select data-placeholder="请选择状态" name="dostatus" class="dostatus chosen-select form-control" tabindex="1">
					<option value="1">开发中</option>
					<option value="2">测试中</option>
					<option value="3">预发布</option>
					<option value="4">已发布</option>
					<option value="5">已废弃</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">客户端类型</label>
			<div class="col-md-8">
				<label class="radio-inline"> <input type="radio" name="client_type" value="1" class="client_type" checked="true"> IOS </label>
				<label class="radio-inline"> <input type="radio" name="client_type" value="2" class="client_type"> Android </label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">客户端版本</label>
			<div class="col-md-4">
				<input name="client_version" class="client_version form-control" type="text">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">接口版本</label>
			<div class="col-md-4">
				<input type="text" name="api_version" value="" class="api_version form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">接口网关</label>
			<div class="col-md-8">
				<input type="text" name="api_gateway" value="" class="api_gateway form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">下载地址</label>
			<div class="col-md-8">
				<input type="text" name="download_url" value="" class="download_url form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-3 control-label">版本说明</label>
			<div class="col-md-8">
				<textarea name="changes" rows="3" class="changes form-control"></textarea>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="save btn btn-primary"><i class="icon-ok"></i> 保存</button>
		</div>
	</form>
</div>