@extends('layouts.adminFrame')
@section('content')
<div class="container">
	<div style="margin-bottom: 60px;">
		<h3>
			<a href="/admin/material/rent/history/{{ $where }}/unreturn"
				@if($type== 'unreturn')
					style="font-size: 40px;"
				@endif>待还记录</a> 
				<a href="/admin/material/rent/history/{{ $where }}/all" 
				@if($type=='all')
					style="font-size: 40px;"
				@endif>历史记录</a>
		</h3>
<?php
$url = '/admin/material/rent/history/search/' . $where . '/' . $type;
?>
<div class="pull-right">
@include('elements/historySearchForm')
</div>
	</div>
	{{ $usingRecords->links() }}
	<div class="table-responsive" id="print">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>序号</th>
					<th>物资图片</th>
					<th>物资名称</th>
					<th>物资编号</th>
					<th>借用人</th>
					<th>电话</th>
					<th>描述</th>
					<th>租借日期</th>
					<th>归还日期</th>
					<th>状态</th>
					<th>操作</th>
				</tr>
			</thead>
			@foreach ($usingRecords as $usingRecord)
			<tr class="tableTr">
				<td>{{$loop->index}}</td>
				<td>@if(isset($usingRecord->pictureUrl)) <img
					src="/picture/download/{!! $usingRecord->pictureUrl !!}"
					class="stayShape" data-action="zoom" height="50" width="50" />
					@else 暂无图片 @endif
				</td>
				<td><a href="/admin/material/{{ $usingRecord->material_id }}/show">
						{{ $usingRecord->materialName }} </a></td>
				<td>{{ $usingRecord->materialNumber }}</td>
				<td>{{ $usingRecord->userName }}</td>
				<td>{{ $usingRecord->phone }}</td>
				<td>{{ $usingRecord->materialDescription }}</td>
				<td>{{ $usingRecord->startTime }}</td>
				<td>{{ $usingRecord->deadline }}</td>
				<td>
				@if($usingRecord->has_deliver == 1) 
					待发货
				@elseif($usingRecord->has_deliver == 2 || $usingRecord->has_deliver == 0) 
					租用中 
				@elseif($usingRecord->has_deliver == 3) 
					已发货
				@elseif($usingRecord->has_deliver == 4) 
					已归还 
				@endif
				</td>
				<td>@if($usingRecord->has_deliver == 1)
					@elseif($usingRecord->has_deliver == 3) 
						<a href="/admin/material/deliver/step/{{ $usingRecord->id }}/accepted" 
						class="myrequest">
						确认收货</a>
					@elseif(($usingRecord->has_deliver == 2
					|| $usingRecord->has_deliver == 0)) 
						<a href="/admin/material/return/{{ $usingRecord->id }}"
						class="myrequest">归还</a> 
					@endif @if(Auth::user()->job_type == 1 ||
					$usingRecord->has_deliver == 4) 
						<a href="/admin/delete/rent/{{ $usingRecord->id }}/{{ $where }}"
						class="myrequest">删除</a> @endif <a
						href="/admin/material/{{ $usingRecord->material_id }}/show">物资详情</a>
				</td>
			</tr>
			@endforeach
		</table>
	</div>
	@if($usingRecords->count()>0)
		<input type="button" onclick=" print()" class="pull-right" value="打印当前页"/>
	@endif
</div>

<div id="error-message"></div>
@endsection @section('importJs')
<script src="{{ asset('js/jquery.form.js') }}"> </script>
@endsection
