@extends('layouts.adminFrame')
@section('content')
<div class="container">
	<div style="margin-bottom: 60px;">
		<h3>
			<a href="/admin/material/deliver/history/orders" @if($type==
				'orders')
	style="font-size: 40px;" @endif>待递送</a> <a
				href="/admin/material/deliver/history/all" @if($type==
				'all')
	style="font-size: 40px;" @endif>历史记录</a>
		</h3>
<?php
$url = '/admin/material/repaire/history/search/' . $type;
?>
<div class="pull-right">@include('elements/historySearchForm')</div>
	</div>
	{{ $delivers->links() }}
	<div class="table-responsive" id="print">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>序号</th>
					<th>物资名称</th>
					<th>图片</th>
					<th>姓名</th>
					<th>所在部门</th>
					<th>电话</th>
					<th>地址</th>
					<th>物资编号</th>
					<th>下单时间</th>
					<th>操作</th>
				</tr>
			</thead>
			@foreach ($delivers as $deliver)
			<tr>
				<td>{{$loop->index}}</td>
				<td><a href="/admin/material/{{ $deliver->materialId }}/show"> {!!
						$deliver->materialName !!} </a></td>
				<td>@if( isset($deliver->pictureUrl)) <img alt=""
					src="/picture/download/{{ $deliver->pictureUrl }}"
					class="stayShape" data-action="zoom" height="50" width="50"> @else
					暂无图片 @endif
				</td>
				<td>{!! $deliver->accepter_name or ''!!}</td>
				<td>{!! $deliver->departmentName or session('company')->name!!}</td>
				<td>{!! $deliver->phone or ''!!}</td>
				<td>{!! $deliver->address or ''!!}</td>
				<td>{!! $deliver->materialNumber or ''!!}</td>
				<td>{!! $deliver->startTime or ''!!}</td>
				<td>@if($deliver->status == 1) <a class="myrequest"
					href="/admin/material/deliver/step/{{ $deliver->id }}/start">开始递送</a>
					@endif <a class="myrequest"
					href="/admin/delete/deliver/{{ $deliver->id }}/manage">删除</a>

				</td>
			</tr>
			@endforeach

		</table>
	</div>
	@if($delivers->count()>0)
		<input type="button" onclick=" print()" class="pull-right" value="打印当前页"/>
	@endif
</div>
@endsection
