@extends('layouts.adminFrame') 
@section('content')
<div class="container">
	<div style="margin-bottom: 60px;">
		<h3>
			<a href="/admin/material/repaire/history/applys" @if($type==
				'applys')
	style="font-size: 40px;" @endif>待维修</a> <a
				href="/admin/material/repaire/history/all" @if($type==
				'all')
	style="font-size: 40px;" @endif>历史记录</a>
		</h3>
<?php
$url = '/admin/material/repaire/history/search/' . $type;
?>
<div class="pull-right">@include('elements/historySearchForm')</div>
	</div>
	{{$repaireRecords->links()}}
	<div class="table-responsive" id="prints">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>序号</th>
					<th>物资图片</th>
					<th>物资名称</th>
					<th>申报人</th>
					<th>所在部门</th>
					<th>手机号码</th>
					<th>故障描述</th>
					<th>申报时间</th>
					<th>状态</th>
					<th>操作</th>
				</tr>
			</thead>
			@foreach ($repaireRecords as $repaireRecord)
			<tr>
				<td>{{$loop->index}}</td>
				<td>@if( isset($repaireRecord->picture_url)) <img alt=""
					src="/picture/download/{{ $repaireRecord->picture_url }}"
					class="stayShape" data-action="zoom" height="50" width="50"> @else
					暂无图片 @endif
				</td>
				<td><a href="/admin/material/{{ $repaireRecord->id }}/show"> {{
						$repaireRecord->name }} </a></td>
				<td>{{ $repaireRecord->userName }}</td>
				<td>{{ $repaireRecord->departmentName or session('company')->name }}</td>
				<td>{{ $repaireRecord->phone }}</td>
				<td>{{ $repaireRecord->faultDescription }}</td>
				<td>{{ $repaireRecord->upTime }}</td>
				<td>@if($repaireRecord->repaireStatus == 1) 
					待维修
					@elseif($repaireRecord->repaireStatus == 2)
					 维修中
					@elseif($repaireRecord->repaireStatus == 3)
					 已修复
					@elseif($repaireRecord->repaireStatus == 5)
					 已报废
					@endif</td>
				<td>
				@if($repaireRecord->repaireStatus == 1) 
					<a class="myrequest" 
					href="/admin/material/repaire/{{ $repaireRecord->recordId }}/result/sucess">修复成功</a>
					<a class="myrequest"
					href="/admin/material/repaire/{{ $repaireRecord->recordId }}/result/shutdown">报废</a>
				@endif 
					<a class="myrequest" href="/admin/delete/repaire/{{ $repaireRecord->recordId }}/manage">删除</a>
				</td>
			</tr>
			@endforeach

		</table>
	</div>
	@if($repaireRecords->count()>0)
		<input type="button" onclick=" print()" class="pull-right" value="打印当前页"/>
	@endif
</div>
@endsection
