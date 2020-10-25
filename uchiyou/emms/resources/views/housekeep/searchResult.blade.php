<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="renderer" content="webkit">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', '物资管家') }}</title>

<meta name="keywords" content="">
<meta name="description" content="">

<link rel="shortcut icon" href="favicon.ico">


</head>

<body class="fixed-sidebar full-height-layout gray-bg"
	style="overflow: auto">
	<link href="{{ asset('css/bootstrap.min.css?v=3.3.6') }}"
	rel="stylesheet">
<link href="{{ asset('css/styles/centerSearch.css') }}"
	rel="stylesheet">
<div class="center">
<h1>公司检索</h1>
<form role="search" class="navbar-form-custom" method="post"
	action="/admin/housekeep/search" id="centerSearchForm"
	style="display: inline-block">
	<input type="hidden" name="_token" value="{{csrf_token()}}">
	<input type="text" placeholder="请输要查找的公司名称…" class="form-control searchContent"
				style="vertical-align: middle;" name="content">
</form>
@if(isset($message))
	<h2 style="color: red;">{{$message}}</h2>
@endif
</div>
<!-- 显示结果部分 -->
@if(isset($companys))
<div class="container">
	<div class="ibox-content">
		<table class="table">
			<!--  $materials 的值有问题 -->
			<h3>
			@if(isset($place) && $place == 'index')
			最近记录:
			@else
			为您找到如下记录 ：
			@endif
			</h3>

			<thead>
				<tr>
					<th>记录条目</th>
					<th>公司名称</th>
					<th>描述</th>
					<th>注册时间</th>
					<th>状态</th>
					<th>操作</th>
				</tr>
			</thead>
			@foreach ($companys as $company)
			<tr>
				<td>{{$loop->index}}</td>
				<td>{{$company->name}}</td>
				<td>{!! $company->description or '暂无描述' !!}</td>
				<td>{!! $company->created_at or '' !!}</td>
				<td>
				@if($company->delete == 1)
				正常服务中
				@elseif($company->delete == 2)
				已关闭服务
				@endif
				</td>
				<td>
				@if($company->delete == 1)
				<a href="/admin/housekeep/company/{{$company->id}}/shutdown/delete">关闭服务</a>
				@elseif($company->delete == 2)
				<a href="/admin/housekeep/company/{{$company->id}}/shutdown/recover">恢复服务</a>
				@endif
				</td>
			</tr>
			@endforeach
		</table>
	</div>
</div>
@endif
	<div style="text-align: center;"></div>
</body>
</html>