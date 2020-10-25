<?php
/* @var $this yii\web\View */

$this->title = '操作日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- 搜索UI -->
<div class="input-group" id="search-from">
	<span class="input-group-addon fix-border"><i class="icon-user"></i></span>
	<input type="text" class="form-control" name="user_id" placeholder="操作人id">

	<span class="input-group-addon fix-border"><i class="icon-user"></i></span>
	<input type="text" class="form-control" name="url"  placeholder="URL">

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